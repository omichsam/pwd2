"""DocAssist client — answers chatbot questions from the organisation's documents.

DocAssist is an external REST service (see docs/DOCASSIST.md):

    POST {DOCASSIST_BASE_URL}/chat   {"question", "session_id"?}
        → {"success", "answer", "session_id", "sources", "citations", "timestamp"}

It keeps conversation context server-side, keyed by `session_id`. We store that
id in the Django session (never in the browser) so follow-ups ("explain that
more simply") continue the same conversation, and drop it when the user starts
a new chat.

Like `chatbot_rag.answer`, `answer()` returns None whenever DocAssist is not
configured, rate-limited or fails, so the caller falls through to the next
engine and the chatbot keeps working. Personal numbers are masked and no user
identifiers are sent.
"""

import json
import logging
import re
import urllib.error
import urllib.request

from .chatbot_config import get_config
from .chatbot_rag import mask_personal_numbers, within_rate_limit

logger = logging.getLogger(__name__)

SESSION_KEY = 'docassist_session_id'
MAX_CITATIONS = 3

# How short a user-facing answer should be. Answers are trimmed to whole
# sentences up to this length (always keeping at least the first sentence).
MAX_ANSWER_CHARS = 450

# Inline source markers DocAssist mixes into the text, e.g. "[policy.pdf, page 3]",
# "[guide.md]" or "[page 1]". The clean source list is shown separately, so these
# are removed. A bracket without a filename or "page N" (e.g. "[note]") is kept.
_INLINE_CITE_RE = re.compile(
    r'\s*\[[^\[\]\n]*?(?:\.(?:md|pdf|txt|docx?|csv|html?)\b|page\s*\d+)[^\[\]\n]*?\]',
    re.IGNORECASE,
)
# Chatty filler sentences to drop from the end.
_FILLER_RE = re.compile(
    r'(?:please\s+let\s+me\s+know|let\s+me\s+know\s+if|feel\s+free\s+to|'
    r'i\s+hope\s+this\s+helps|if\s+you\s+have\s+any\s+(?:other\s+)?questions|'
    r'is\s+there\s+anything\s+else|do\s+you\s+have\s+any\s+(?:other\s+)?questions)'
    r'[^.!?]*[.!?]*\s*$',
    re.IGNORECASE,
)
_SPACE_BEFORE_PUNCT_RE = re.compile(r'\s+([.,;:!?])')
_MULTISPACE_RE = re.compile(r'[ \t]{2,}')
_MULTINEWLINE_RE = re.compile(r'\n{3,}')
_SENTENCE_SPLIT_RE = re.compile(r'(?<=[.!?])\s+')


def clean_answer(text, max_chars=MAX_ANSWER_CHARS):
    """Make a DocAssist answer short, plain and easy to read for end users."""
    if not text:
        return ''
    text = _INLINE_CITE_RE.sub('', text)
    text = _SPACE_BEFORE_PUNCT_RE.sub(r'\1', text)
    text = _MULTISPACE_RE.sub(' ', text)
    text = _MULTINEWLINE_RE.sub('\n\n', text)
    # Drop a trailing chatty sentence, then any now-trailing whitespace.
    text = _FILLER_RE.sub('', text).strip()

    if len(text) <= max_chars:
        return text
    # Too long: keep whole sentences up to the limit (at least the first one).
    sentences = _SENTENCE_SPLIT_RE.split(text)
    out = sentences[0]
    for sentence in sentences[1:]:
        if len(out) + 1 + len(sentence) > max_chars:
            break
        out += ' ' + sentence
    return out.strip()


def is_enabled():
    return get_config().docassist_active


def reset_conversation(session):
    """Forget the DocAssist conversation (new chat / cleared history)."""
    if session is not None:
        session.pop(SESSION_KEY, None)


def _request(method, path, payload=None, config=None):
    """Call DocAssist. Returns (status, parsed JSON dict) or (None, None) on failure.

    `config` defaults to the live configuration; the settings page passes the
    values being edited so they can be tested before saving.
    """
    config = config or get_config()
    url = config.docassist_base_url.rstrip('/') + path
    data = json.dumps(payload).encode('utf-8') if payload is not None else None
    req = urllib.request.Request(url, data=data, method=method, headers={
        'Authorization': f'Bearer {config.docassist_api_key}',
        'Accept': 'application/json',
        **({'Content-Type': 'application/json'} if data is not None else {}),
    })
    try:
        with urllib.request.urlopen(req, timeout=config.docassist_timeout_seconds) as resp:
            status, raw = resp.status, resp.read()
    except urllib.error.HTTPError as exc:
        status, raw = exc.code, exc.read()
    except (urllib.error.URLError, TimeoutError, OSError) as exc:
        logger.warning('docassist connection_error path=%s error=%s', path, exc)
        return None, None
    try:
        body = json.loads(raw or b'{}')
    except (json.JSONDecodeError, UnicodeDecodeError):
        logger.warning('docassist invalid_json path=%s status=%s', path, status)
        return status, None
    return status, body if isinstance(body, dict) else None


def format_citations(citations):
    """[{filename, page}, …] → '📄 Source: policy.pdf (p. 3); act.pdf'."""
    seen, parts = set(), []
    for c in citations if isinstance(citations, list) else []:
        if not isinstance(c, dict) or not isinstance(c.get('filename'), str):
            continue
        name = c['filename'].strip()
        page = c.get('page')
        label = f'{name} (p. {page})' if isinstance(page, int) else name
        if name and label not in seen:
            seen.add(label)
            parts.append(label)
        if len(parts) >= MAX_CITATIONS:
            break
    if not parts:
        return ''
    return ('📄 Sources: ' if len(parts) > 1 else '📄 Source: ') + '; '.join(parts)


def ask(question, session_id=None, config=None):
    """One /chat call. Returns the response dict on success, else None."""
    payload = {'question': question}
    if session_id:
        payload['session_id'] = session_id
    status, body = _request('POST', '/chat', payload, config)
    if status == 404 and session_id:
        # Expired / unknown conversation on the DocAssist side — start a new one.
        status, body = _request('POST', '/chat', {'question': question}, config)
    if status != 200 or not body or not body.get('success'):
        if status is not None:
            logger.warning('docassist chat_failed status=%s error=%s', status,
                           (body or {}).get('error', ''))
        return None
    if not isinstance(body.get('answer'), str) or not body['answer'].strip():
        return None
    # Tidy the wording for end users; keep the original if cleaning empties it.
    body['answer'] = clean_answer(body['answer']) or body['answer'].strip()
    return body


def answer(message, user, request_meta, session):
    """Return {'text', 'citations', 'session_id'} or None to fall back."""
    if not is_enabled() or not message:
        return None
    if not within_rate_limit(user, request_meta, prefix='docassist'):
        return None

    question = mask_personal_numbers(message)
    session_id = session.get(SESSION_KEY) if session is not None else None
    body = ask(question, session_id)
    if body is None:
        return None

    new_session_id = body.get('session_id')
    if session is not None and isinstance(new_session_id, str) and new_session_id:
        session[SESSION_KEY] = new_session_id[:100]

    citations = body.get('citations') or []
    text = body['answer'].strip()
    footer = format_citations(citations)
    logger.info('docassist answered citations=%d', len(citations) if isinstance(citations, list) else 0)
    return {
        'text': f'{text}\n\n{footer}' if footer else text,
        'citations': citations if isinstance(citations, list) else [],
        'session_id': new_session_id,
    }


def health(config=None):
    """GET /health, /validate-key and /documents — for the settings page and
    the `docassist_check` command. Each value is (status, body)."""
    return {
        'health': _request('GET', '/health', config=config),
        'validate_key': _request('GET', '/validate-key', config=config),
        'documents': _request('GET', '/documents', config=config),
    }
