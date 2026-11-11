"""Retrieval-augmented answers for the PWD Access assistant.

Pipeline for a free-text question:

    1. retrieve   BM25 over the knowledge base (`chatbot_kb.INTENTS` plus any
                  Markdown files in `core/knowledge/`), filtered by the user's role
    2. generate   Claude answers *only* from the retrieved passages and returns
                  JSON: {answer, sources, grounded}
    3. render     sources become quick-reply buttons back into the rule-based
                  intents, so the user can jump to the canonical answer

Personal data never reaches the model: live answers (application status, work
queues) stay in `chatbot_views`, long digit runs (ID / phone numbers) are
masked, and no user identifiers are sent — only the role, for tone.

`answer()` returns None whenever RAG is disabled, rate-limited, finds nothing
relevant or the API call fails; the caller then falls back to the rule-based
engine, so the chatbot keeps working without an API key or network.
"""

import json
import logging
import math
import re
from collections import Counter
from dataclasses import dataclass
from functools import lru_cache
from pathlib import Path

from django.conf import settings
from django.core.cache import cache

from .chatbot_config import get_config
from .chatbot_kb import INTENTS, SUGGESTION_LABELS

logger = logging.getLogger(__name__)

KNOWLEDGE_DIR = Path(__file__).resolve().parent / 'knowledge'

# Intents answered from the database or used for small talk — not retrieval material.
_SKIP_INTENTS = {'menu', 'greeting', 'thanks', 'bye'}

TOP_K = 4
MIN_SCORE = 1.0          # BM25 score below which a passage is treated as irrelevant
MAX_HISTORY_TURNS = 6
MAX_HISTORY_CHARS = 1000

_DIGITS_RE = re.compile(r'\d[\d\s-]{5,}\d')
_HEADING_RE = re.compile(r'^##\s+(.+)$', re.MULTILINE)

SYSTEM_PROMPT = """\
You are the PWD Assistant on PWD Access, Kenya's portal for persons with disabilities \
(PWDs) to register, book a disability assessment at a hospital, follow its review \
(medical officer → health officer → county officer) and receive a disability certificate.

Answer the user's question using ONLY the passages inside <sources>. They are the \
portal's official help content. If the sources do not contain the answer, say briefly \
that you don't have that information and suggest contacting support or talking to a \
person — never guess about procedures, fees, timelines, eligibility or the law.

Style:
- Reply in the language the user wrote in (English or Kiswahili).
- Plain, respectful, disability-inclusive wording; short sentences for easy reading.
- At most about 120 words. Use "•" bullets or numbered steps when listing.
- Emphasise a key phrase with *single asterisks*. No headings, tables, links or HTML.
- Never ask for or repeat ID numbers, phone numbers or medical details.
- You cannot see the user's own application; for status questions tell them to ask \
"check my status" or open their dashboard.

Return JSON: "answer" is the reply text; "sources" lists the ids of the passages you \
actually used; "grounded" is false when the sources did not answer the question."""

OUTPUT_SCHEMA = {
    'type': 'object',
    'properties': {
        'answer': {'type': 'string'},
        'sources': {'type': 'array', 'items': {'type': 'string'}},
        'grounded': {'type': 'boolean'},
    },
    'required': ['answer', 'sources', 'grounded'],
    'additionalProperties': False,
}


@dataclass(frozen=True)
class Passage:
    id: str              # intent id, or "doc:<file>#<n>" for Markdown sections
    title: str
    text: str
    roles: frozenset | None   # None = visible to everyone
    intent: str = ''          # intent to link back to, when the passage came from one


# ── Corpus ─────────────────────────────────────────────────────────────────────
def _intent_passages():
    for intent in INTENTS:
        if intent['id'] in _SKIP_INTENTS or intent.get('live'):
            continue
        text = intent.get('text')
        roles = frozenset(intent['roles']) if intent.get('roles') else None
        title = SUGGESTION_LABELS.get(intent['id'], intent['id'].replace('_', ' '))
        keywords = ', '.join(intent.get('keywords', []))
        if isinstance(text, dict):
            # Role-specific wording: one passage per role variant.
            for role_key, variant in text.items():
                if not variant:
                    continue
                variant_roles = roles if role_key == '*' else frozenset({role_key})
                suffix = '' if role_key == '*' else f'@{role_key}'
                yield Passage(f"{intent['id']}{suffix}", title,
                              f"{variant}\n\nRelated terms: {keywords}",
                              variant_roles, intent['id'])
        elif text:
            yield Passage(intent['id'], title, f"{text}\n\nRelated terms: {keywords}",
                          roles, intent['id'])


def _document_passages():
    """Markdown files in core/knowledge/, split into sections on `## ` headings."""
    if not KNOWLEDGE_DIR.is_dir():
        return
    for path in sorted(KNOWLEDGE_DIR.glob('*.md')):
        if path.name.lower() == 'readme.md':
            continue
        content = path.read_text(encoding='utf-8')
        headings = list(_HEADING_RE.finditer(content))
        if not headings:
            yield Passage(f'doc:{path.stem}', path.stem.replace('_', ' '), content.strip(), None)
            continue
        for n, match in enumerate(headings):
            end = headings[n + 1].start() if n + 1 < len(headings) else len(content)
            body = content[match.end():end].strip()
            if body:
                yield Passage(f'doc:{path.stem}#{n + 1}', match.group(1).strip(), body, None)


class _Index:
    """Okapi BM25 over the corpus, using the chatbot's own tokenizer so
    synonyms, Kiswahili terms and typos fold the same way as intent matching."""

    k1, b = 1.5, 0.75

    def __init__(self, passages):
        from .chatbot_views import tokenize  # local import: chatbot_views imports us
        self._tokenize = tokenize
        self.passages = passages
        self.term_freqs = [Counter(tokenize(f'{p.title} {p.text}')) for p in passages]
        self.lengths = [sum(tf.values()) for tf in self.term_freqs]
        self.avg_len = (sum(self.lengths) / len(self.lengths)) if self.lengths else 1.0
        doc_freq = Counter(term for tf in self.term_freqs for term in tf)
        n = len(passages)
        self.idf = {t: math.log(1 + (n - df + 0.5) / (df + 0.5)) for t, df in doc_freq.items()}

    def search(self, query, role, k=TOP_K):
        terms = set(self._tokenize(query))
        scored = []
        for i, passage in enumerate(self.passages):
            if passage.roles is not None and role not in passage.roles:
                continue
            tf, length = self.term_freqs[i], self.lengths[i]
            score = 0.0
            for term in terms:
                f = tf.get(term)
                if f:
                    norm = f + self.k1 * (1 - self.b + self.b * length / self.avg_len)
                    score += self.idf[term] * f * (self.k1 + 1) / norm
            if score >= MIN_SCORE:
                scored.append((score, passage))
        scored.sort(key=lambda pair: -pair[0])
        return [p for _, p in scored[:k]]


@lru_cache(maxsize=1)
def get_index():
    return _Index(list(_intent_passages()) + list(_document_passages()))


# ── Helpers ────────────────────────────────────────────────────────────────────
def is_enabled():
    return get_config().rag_enabled   # the page warns when no Anthropic key is visible


def mask_personal_numbers(text):
    """Hide ID / phone numbers before anything leaves the server."""
    return _DIGITS_RE.sub('[number]', text or '')


def within_rate_limit(user, request_meta, prefix='chatbot-rag'):
    """Per-user (or per-IP) budget shared by the external answer engines."""
    if getattr(user, 'is_authenticated', False):
        who = f'u{user.pk}'
    else:
        forwarded = request_meta.get('HTTP_X_FORWARDED_FOR', '')
        who = forwarded.split(',')[0].strip() if forwarded else request_meta.get('REMOTE_ADDR', '')
    key = f'{prefix}:{who}'
    config = get_config()
    used = cache.get(key, 0)
    if used >= config.max_per_window:
        return False
    cache.set(key, used + 1, config.window_seconds)
    return True


def clean_history(raw_history, live_intents):
    """Client-sent transcript → alternating API messages, oldest first.

    Bot turns produced from the database (status, queues) are dropped so personal
    data is never forwarded; error bubbles are dropped too.
    """
    if not isinstance(raw_history, list):
        return []
    messages = []
    for item in raw_history[-MAX_HISTORY_TURNS:]:
        if not isinstance(item, dict):
            continue
        text = item.get('text')
        if not isinstance(text, str) or not text.strip() or item.get('error'):
            continue
        role = {'user': 'user', 'bot': 'assistant'}.get(item.get('from'))
        if role is None:
            continue
        if role == 'assistant' and item.get('intent') in live_intents:
            continue
        text = mask_personal_numbers(text.strip()[:MAX_HISTORY_CHARS])
        if messages and messages[-1]['role'] == role:
            messages[-1]['content'] += '\n\n' + text
        else:
            messages.append({'role': role, 'content': text})
    while messages and messages[0]['role'] != 'user':
        messages.pop(0)
    return messages


def _format_sources(passages):
    return '\n'.join(
        f'<source id="{p.id}" title="{p.title}">\n{p.text}\n</source>' for p in passages
    )


@lru_cache(maxsize=1)
def _client():
    import anthropic
    return anthropic.Anthropic(
        timeout=settings.CHATBOT_RAG_TIMEOUT_SECONDS, max_retries=1,
    )


def _call_llm(question, passages, role, history):
    """One Claude call. Returns the parsed JSON dict, or None."""
    import anthropic

    user_turn = (
        f"<sources>\n{_format_sources(passages)}\n</sources>\n\n"
        f"<user_role>{role or 'visitor (not logged in)'}</user_role>\n"
        f"<question>{question}</question>"
    )
    model = get_config().llm_model
    output_config = {'format': {'type': 'json_schema', 'schema': OUTPUT_SCHEMA}}
    if not model.startswith('claude-haiku'):
        output_config['effort'] = 'low'   # short grounded answers; Haiku rejects `effort`
    try:
        response = _client().beta.messages.create(
            model=model,
            max_tokens=4096,
            betas=['server-side-fallback-2026-07-01'],
            fallbacks='default',
            output_config=output_config,
            system=[{'type': 'text', 'text': SYSTEM_PROMPT,
                     'cache_control': {'type': 'ephemeral'}}],
            messages=history + [{'role': 'user', 'content': user_turn}],
        )
    except anthropic.RateLimitError:
        logger.warning('chatbot_rag rate_limited')
        return None
    except anthropic.APIStatusError as exc:
        logger.warning('chatbot_rag api_error status=%s request_id=%s',
                       exc.status_code, getattr(exc, 'request_id', ''))
        return None
    except anthropic.APIConnectionError:
        logger.warning('chatbot_rag connection_error')
        return None

    if response.stop_reason in ('refusal', 'max_tokens'):
        logger.info('chatbot_rag stop_reason=%s request_id=%s',
                    response.stop_reason, response._request_id)
        return None
    raw = ''.join(block.text for block in response.content if block.type == 'text')
    try:
        data = json.loads(raw)
    except json.JSONDecodeError:
        logger.warning('chatbot_rag invalid_json request_id=%s', response._request_id)
        return None
    if not isinstance(data, dict) or not isinstance(data.get('answer'), str):
        return None
    return data


# ── Entry point ────────────────────────────────────────────────────────────────
def answer(message, user, role, raw_history, request_meta, live_intents):
    """Return {'text', 'sources', 'grounded'} or None to fall back to rules."""
    if not is_enabled() or not message:
        return None

    question = mask_personal_numbers(message)
    passages = get_index().search(question, role)
    history = clean_history(raw_history, live_intents)
    if not passages and history:
        # Follow-ups ("how long does that take?") rarely name the topic;
        # retrieve with the previous user turn for context.
        last_user = next((m['content'] for m in reversed(history) if m['role'] == 'user'), '')
        passages = get_index().search(f'{last_user} {question}', role)
    if not passages:
        return None
    if not within_rate_limit(user, request_meta):
        return None

    data = _call_llm(question, passages, role, history)
    if not data or not data['answer'].strip():
        return None

    by_id = {p.id: p for p in passages}
    used = [by_id[s] for s in data.get('sources', []) if s in by_id]
    logger.info('chatbot_rag answered grounded=%s sources=%s role=%s',
                bool(data.get('grounded')), ','.join(p.id for p in used), role or 'anonymous')
    return {
        'text': data['answer'].strip(),
        'sources': used,
        'grounded': bool(data.get('grounded')),
    }
