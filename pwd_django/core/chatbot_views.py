"""PWD Access assistant — intent matching and JSON endpoints.

Content lives in `core.chatbot_kb`; this module handles understanding the
message, generating live database answers and serving the two endpoints:

    POST /chatbot/           → {text, quick_replies, intent, confidence}
    POST /chatbot/feedback/  → {ok: true}

Free-text questions go to `core.docassist` (the organisation's document Q&A
service) first, then `core.chatbot_rag` (retrieval + Claude), each only when
configured; personal/live answers and small talk stay rule-based, and the
rule-based engine is the fallback whenever neither has an answer.
"""

import json
import logging
import re
import unicodedata
from difflib import SequenceMatcher
from functools import lru_cache

from django.db.models import Count
from django.http import JsonResponse
from django.utils import timezone
from django.views.decorators.http import require_POST

from .chatbot_kb import (
    INTENTS, INTENTS_BY_ID, OFFICER_ROLES, STATUS_FLOW, STATUS_LABELS,
    STATUS_NEXT_STEP, STOPWORDS, SUGGESTION_LABELS, SYNONYMS, URLS,
    WEAK_TOKENS, MENU_REPLY, menu_for,
)
from . import chatbot_rag, docassist
from .models import Assessment, County, Hospital

logger = logging.getLogger(__name__)

MAX_MESSAGE_LENGTH = 500

# Score thresholds — see `match_intent`.
CONFIDENT_SCORE = 3.5    # answer straight away
SUGGEST_SCORE = 1.5      # offer "did you mean?" instead of guessing
FUZZY_RATIO = 0.86       # typo tolerance for tokens of 5+ characters

_PUNCT_RE = re.compile(r'[^a-z0-9\s]+')
_WS_RE = re.compile(r'\s+')


# ── Text normalisation ─────────────────────────────────────────────────────────
def normalize(text):
    """Lowercase, strip accents and punctuation, collapse whitespace."""
    text = unicodedata.normalize('NFKD', text or '')
    text = ''.join(c for c in text if not unicodedata.combining(c))
    text = _PUNCT_RE.sub(' ', text.lower())
    return _WS_RE.sub(' ', text).strip()


_SYNONYM_KEYS = tuple(k for k in SYNONYMS if len(k) >= 6)


@lru_cache(maxsize=4096)
def _canonical(token):
    """Fold synonyms (typos included) and trim a trailing plural 's'."""
    if token in SYNONYMS:
        return SYNONYMS[token]
    if len(token) > 3 and token.endswith('s') and not token.endswith('ss'):
        stem = token[:-1]
        if stem in SYNONYMS:
            return SYNONYMS[stem]
        token = stem
    # "apointment" → "appointment" → "booking"
    if len(token) >= 6:
        for key in _SYNONYM_KEYS:
            if abs(len(key) - len(token)) <= 2 and \
                    SequenceMatcher(None, token, key).ratio() >= FUZZY_RATIO:
                return SYNONYMS[key]
    return token


def tokenize(text, keep_stopwords=False):
    """Normalised, synonym-folded, stopword-free token list."""
    tokens = []
    for raw in normalize(text).split():
        token = _canonical(raw)
        if keep_stopwords or token not in STOPWORDS:
            tokens.append(token)
    return tokens


@lru_cache(maxsize=2048)
def _keyword_parts(keyword):
    """Pre-chewed form of a knowledge-base keyword.

    Returns (n_words, full_phrase, content_tokens, content_phrase, total_weight).
    """
    words = tokenize(keyword, keep_stopwords=True)
    content = tuple(w for w in words if w not in STOPWORDS)
    return (
        len(words), ' '.join(words),
        content, ' '.join(content),
        sum(_token_weight(w) for w in content),
    )


def _token_weight(token):
    """Interrogatives and filler verbs contribute little on their own."""
    return 0.3 if token in WEAK_TOKENS else 1.0


def _fuzzy_hit(token, targets):
    """True when `token` is a near-miss (typo) for any target token."""
    if len(token) < 5:
        return False
    for target in targets:
        if abs(len(target) - len(token)) > 2 or len(target) < 5:
            continue
        if SequenceMatcher(None, token, target).ratio() >= FUZZY_RATIO:
            return True
    return False


# ── Intent matching ────────────────────────────────────────────────────────────
def _intent_available(intent, role):
    allowed = intent.get('roles')
    return allowed is None or role in allowed


def score_intent(intent, norm_message, content_phrase, token_set):
    """Weighted keyword score for one intent.

    Typing a keyword and nothing else is decisive; verbatim phrases beat
    scattered words; a keyword that is only one meaningful word inside a longer
    phrase (the "you" in "are you human") counts for much less, so filler words
    cannot carry an intent on their own.
    """
    score = 0.0
    padded_message = f' {content_phrase} '
    for keyword in intent['keywords']:
        n_words, full_phrase, content, phrase, full_weight = _keyword_parts(keyword)
        if not content:
            continue
        # Typing the keyword and nothing else is decisive — unless the keyword
        # boils down to a single filler word ("what" from "what are you").
        if full_phrase == norm_message:
            return 12.0
        if phrase == content_phrase and not (
            len(content) == 1 and content[0] in WEAK_TOKENS
        ):
            return 12.0

        if len(content) > 1:
            if f' {phrase} ' in padded_message:             # verbatim phrase
                score += 2.0 + 1.5 * full_weight
                continue
            hits, hit_weight = 0, 0.0
            for word in content:
                if word in token_set:
                    hits += 1
                    hit_weight += _token_weight(word)
            if hits == len(content):
                score += 1.5 * full_weight                  # all words, reordered
            elif hits:
                score += 0.5 * hit_weight                   # partial phrase
        else:
            token = content[0]
            weight = (2.0 if n_words == 1 else 1.0) * _token_weight(token)
            if token in token_set:
                score += weight
            elif _fuzzy_hit(token, token_set):
                score += weight * 0.75
    return score


def match_intent(message, role):
    """Return (intent_id or None, score, [runner-up ids])."""
    norm = ' '.join(tokenize(message, keep_stopwords=True))
    tokens = tokenize(message)
    if not tokens:
        return None, 0.0, []
    token_set = set(tokens)
    content_phrase = ' '.join(tokens)

    ranked = []
    for intent in INTENTS:
        if not _intent_available(intent, role):
            continue
        score = score_intent(intent, norm, content_phrase, token_set)
        if score > 0:
            ranked.append((score, intent['id']))
    if not ranked:
        return None, 0.0, []

    ranked.sort(key=lambda pair: (-pair[0], pair[1]))
    top_score, top_id = ranked[0]

    # A one- or two-word question can't accumulate much score, so scale down.
    short = len(tokens) <= 2
    confident = 2.0 if short else CONFIDENT_SCORE
    suggest = 1.0 if short else SUGGEST_SCORE

    if top_score >= confident:
        return top_id, top_score, []
    if top_score >= suggest:
        # A clear leader is good enough to answer directly.
        if len(ranked) == 1 or top_score >= 2 * ranked[1][0]:
            return top_id, top_score, []
        return None, top_score, [i for _, i in ranked[:3]]
    return None, top_score, []


# ── Live (database-backed) answers ─────────────────────────────────────────────
def _role_of(user):
    return getattr(user, 'role', '') if getattr(user, 'is_authenticated', False) else ''


def _login_prompt(what):
    return (
        f"You need to be logged in for me to {what}.\n\n"
        "Log in and I'll pull up your details right away."
    ), [
        {'label': '🔑 Log In →', 'intent': 'login', 'url': URLS['login']},
        {'label': '📝 Register →', 'intent': 'register', 'url': URLS['register']},
        MENU_REPLY,
    ]


def _describe_assessment(assessment, today):
    status = assessment.status
    label = STATUS_LABELS.get(status, assessment.get_status_display())
    hospital = assessment.hospital.name if assessment.hospital else 'hospital not set'
    lines = [f"*{assessment.get_disability_type_display()}* — {hospital}"]

    step = assessment.get_status_step()
    if step:
        lines.append(f"Progress: step {step} of 4")
    lines.append(f"Status: {label}")

    appointment = assessment.assessment_date
    if appointment:
        stamp = appointment.strftime('%d %b %Y')
        if status == 'pending' and appointment > today:
            days = (appointment - today).days
            when = 'tomorrow' if days == 1 else f"in {days} day{'s' if days != 1 else ''}"
            lines.append(f"Appointment: {stamp} ({when})")
        elif status == 'pending' and appointment == today:
            lines.append(f"Appointment: {stamp} (today)")
        else:
            lines.append(f"Appointment: {stamp}")

    if status == 'rejected' and assessment.rejection_reason:
        lines.append(f"Reason given: {assessment.rejection_reason}")

    if status == 'approved_by_county_officer':
        certificate = next(
            (c for c in assessment.certificates.all() if c.revoked_at is None), None
        )
        if certificate:
            lines.append(f"Certificate No: {certificate.certificate_number}")

    next_step = STATUS_NEXT_STEP.get(status)
    if next_step:
        lines.append(f"Next: {next_step}")
    return '\n'.join(lines)


def _status_answer(user):
    if not getattr(user, 'is_authenticated', False):
        return _login_prompt('check your application status')

    if _role_of(user) in OFFICER_ROLES:
        return _queue_answer(user)

    assessments = list(
        Assessment.objects
        .filter(user=user)
        .select_related('hospital')
        .prefetch_related('certificates')
        .order_by('-assessment_date', '-created_at')[:3]
    )
    if not assessments:
        return (
            "I couldn't find any assessments on your account yet.\n\n"
            "Booking one takes a couple of minutes and costs nothing — shall we start?"
        ), [
            {'label': '📅 Book Assessment →', 'intent': 'book', 'url': URLS['pwd_application']},
            {'label': '📄 What do I need?', 'intent': 'documents'},
            MENU_REPLY,
        ]

    today = timezone.localdate()
    blocks = [_describe_assessment(a, today) for a in assessments]
    heading = (
        'Here is your assessment:' if len(blocks) == 1
        else f'Here are your {len(blocks)} most recent assessments:'
    )
    text = heading + '\n\n' + '\n\n'.join(blocks)

    replies = [{'label': '📊 Open Dashboard →', 'intent': 'status', 'url': URLS['pwd_dashboard']}]
    if any(a.status == 'approved_by_county_officer' for a in assessments):
        replies.append({'label': '🏅 Print Certificate', 'intent': 'certificate'})
    if any(a.status == 'rejected' for a in assessments):
        replies.append({'label': '❌ What now?', 'intent': 'rejected'})
    if all(a.status not in ('approved_by_county_officer',) for a in assessments):
        replies.append({'label': '⏱️ How long?', 'intent': 'timeline'})
    replies.append(MENU_REPLY)
    return text, replies


def _queue_answer(user):
    """Workload summary for officer roles."""
    role = _role_of(user)
    if role not in OFFICER_ROLES:
        return _status_answer(user)

    qs = Assessment.objects.all()
    if role in ('medical_officer', 'health_officer', 'hospital_admin') and user.hospital_id:
        qs = qs.filter(hospital_id=user.hospital_id)
    elif role == 'county_officer' and user.county_id:
        qs = qs.filter(hospital__county_id=user.county_id)

    counts = {row['status']: row['n'] for row in qs.values('status').annotate(n=Count('id'))}
    waiting_on_me = {
        'medical_officer': ('pending', 'awaiting your medical assessment', URLS['medical_queue']),
        'health_officer': ('checked', 'awaiting your review', URLS['health_queue']),
        'county_officer': ('approved_by_health_officer', 'awaiting your certification', URLS['county_queue']),
    }.get(role)

    scope = (
        user.hospital.name if role in ('medical_officer', 'health_officer', 'hospital_admin')
        and user.hospital_id else
        f"{user.county.name} County" if user.county_id else 'your scope'
    )
    lines = [f"Assessment workload for *{scope}*:", '']
    for status in STATUS_FLOW + ['rejected']:
        n = counts.get(status, 0)
        if n:
            lines.append(f"• {STATUS_LABELS.get(status, status)} — *{n}*")
    if len(lines) == 2:
        lines.append('• Nothing recorded yet.')

    replies = [MENU_REPLY]
    if waiting_on_me:
        status, phrase, url = waiting_on_me
        pending = counts.get(status, 0)
        lines.insert(1, '')
        lines.insert(
            1,
            f"📥 *{pending}* {'case' if pending == 1 else 'cases'} {phrase}."
            if pending else f"✅ Nothing {phrase} — you're all caught up."
        )
        if pending:
            replies.insert(0, {'label': '📥 Open Queue →', 'intent': 'my_queue', 'url': url})
    elif role == 'hospital_admin':
        replies.insert(0, {'label': '👥 Manage Officers →', 'intent': 'my_queue',
                           'url': URLS['hospital_officers']})
    elif role == 'national_admin':
        replies.insert(0, {'label': '🗺️ County Breakdown →', 'intent': 'my_queue',
                           'url': URLS['national_counties']})
    replies.insert(len(replies) - 1, {'label': '🧭 My Role', 'intent': 'my_role'})
    return '\n'.join(lines), replies


ROLE_DUTIES = {
    'medical_officer': (
        "You are a *Medical Officer* — stage 2 of the assessment workflow.\n\n"
        "• You see assessments booked at your hospital only\n"
        "• Your designation controls which disability types you may assess\n"
        "• You examine the citizen, complete the disability-specific form and submit it\n"
        "• Submitting moves the case to *Medical Review Complete* for the Health Officer\n\n"
        "You cannot certify — that is the County Officer's step."
    ),
    'health_officer': (
        "You are a *Health Officer* — stage 3 of the workflow.\n\n"
        "• You review completed medical assessments at your hospital\n"
        "• You approve findings, or reject with a written reason the citizen will see\n"
        "• Approving moves the case to the County Officer for certification\n\n"
        "You do not fill clinical forms; that is the Medical Officer's step."
    ),
    'county_officer': (
        "You are a *County Officer* — the final authority in your county.\n\n"
        "• Certify assessments approved by health officers (this issues the certificate)\n"
        "• Revoke or re-issue certificates when needed\n"
        "• Register hospitals and sub-counties\n"
        "• Approve, reassign and designate medical and health officers\n"
        "• Create hospital admin accounts"
    ),
    'hospital_admin': (
        "You are a *Hospital Admin*.\n\n"
        "• Approve or deactivate medical and health officers at your facility\n"
        "• Monitor assessment volumes and turnaround\n\n"
        "You do not assess, review or certify — those stay with the clinical officers and the "
        "County Officer."
    ),
    'national_admin': (
        "You are a *National Admin*.\n\n"
        "• Create and manage counties and county-officer accounts\n"
        "• View hospitals and officers nationwide\n"
        "• Review national and per-county reporting\n\n"
        "You oversee the system; case-level decisions remain with county-level officers."
    ),
}


def _role_answer(user):
    role = _role_of(user)
    if not role:
        return _login_prompt('tell you about your role')

    text = ROLE_DUTIES.get(role)
    if not text:
        return (
            "You are signed in as a *PWD Citizen*. You can book assessments, follow their progress "
            "and print your certificate once it is issued."
        ), menu_for(role)

    extras = []
    if getattr(user, 'hospital_id', None):
        extras.append(f"Hospital: {user.hospital.name}")
    if getattr(user, 'county_id', None):
        extras.append(f"County: {user.county.name}")
    designation = getattr(user, 'designation', None)
    if designation:
        extras.append(f"Designation: {designation.name}")
    if not getattr(user, 'is_approved', True):
        extras.append('⚠️ Your account is not yet approved.')
    if extras:
        text = text + '\n\n' + '\n'.join(extras)

    return text, [
        {'label': '📥 My Work Queue', 'intent': 'my_queue'},
        {'label': '🔄 Full Process', 'intent': 'process'},
        MENU_REPLY,
    ]


def _hospitals_answer(message, user):
    """Assessment-centre list, narrowed to a county named in the message."""
    tokens = set(tokenize(message))
    counties = list(County.objects.values('id', 'name'))
    matched = next(
        (c for c in counties if set(tokenize(c['name'])) & tokens),
        None,
    )
    # Fall back to the user's own county when they ask "near me".
    if not matched and getattr(user, 'county_id', None) and (
        tokens & {'near', 'me', 'my', 'close', 'around'}
    ):
        matched = {'id': user.county_id, 'name': user.county.name}

    qs = Hospital.objects.select_related('county').order_by('name')
    if matched:
        qs = qs.filter(county_id=matched['id'])

    total = qs.count()
    if total == 0:
        if matched:
            text = (
                f"I don't have any registered assessment centre in *{matched['name']}* yet.\n\n"
                "Centres are still being added across the 47 counties — check the full list from "
                "your dashboard, or ask about a neighbouring county."
            )
        else:
            text = (
                "Assessment centres are still being registered across the 47 counties.\n\n"
                "Log in and open *Assessment Centers* from your dashboard for the latest list."
            )
        return text, [
            {'label': '🏥 All Centres →', 'intent': 'hospitals', 'url': URLS['pwd_hospitals']},
            {'label': '📞 Contact Us', 'intent': 'contact'},
            MENU_REPLY,
        ]

    shown = list(qs[:5])
    lines = []
    for hospital in shown:
        detail = hospital.county.name if not matched else (hospital.subcounty or hospital.county.name)
        contact = f" · ☎ {hospital.phone}" if hospital.phone else ''
        lines.append(f"• *{hospital.name}* — {detail}{contact}")

    where = f"in *{matched['name']}*" if matched else 'registered nationally'
    more = f"\n…and {total - len(shown)} more" if total > len(shown) else ''
    text = (
        f"{total} assessment centre{'s' if total != 1 else ''} {where}:\n\n"
        + '\n'.join(lines) + more
        + "\n\nOpen *Assessment Centers* on your dashboard to see addresses and pick one when booking."
    )
    return text, [
        {'label': '🏥 Full List →', 'intent': 'hospitals', 'url': URLS['pwd_hospitals']},
        {'label': '📅 Book Assessment', 'intent': 'book'},
        MENU_REPLY,
    ]


LIVE_HANDLERS = {
    'status': lambda message, user: _status_answer(user),
    'my_queue': lambda message, user: _queue_answer(user),
    'my_role': lambda message, user: _role_answer(user),
    'hospitals': _hospitals_answer,
}

# Answered by rules even when RAG is on: live database answers (personal data
# must not go to the model), small talk, and the deterministic human hand-off.
RULE_FIRST_INTENTS = set(LIVE_HANDLERS) | {'menu', 'greeting', 'thanks', 'bye', 'human', 'contact'}


# ── Rendering ──────────────────────────────────────────────────────────────────
def _resolve_text(intent, role):
    text = intent.get('text')
    if isinstance(text, dict):
        return text.get(role) or text.get('*') or ''
    return text or ''


def _render(intent_id, message, user):
    """Build the (text, replies) pair for a resolved intent."""
    role = _role_of(user)
    intent = INTENTS_BY_ID.get(intent_id)
    if intent is None:
        return None

    handler = LIVE_HANDLERS.get(intent_id) if intent.get('live') else None
    if handler:
        return handler(message, user)

    text = _resolve_text(intent, role)
    if not text:
        return None
    replies = intent.get('replies')
    return text, (list(replies) if replies else menu_for(role))


def _suggestion_replies(intent_ids, role):
    replies = []
    for intent_id in intent_ids:
        intent = INTENTS_BY_ID.get(intent_id)
        if intent is None or not _intent_available(intent, role):
            continue
        label = SUGGESTION_LABELS.get(intent_id)
        if label:
            replies.append({'label': label, 'intent': intent_id})
    return replies


def _fallback(message, role, suggestions):
    if suggestions:
        return (
            "I'm not completely sure what you need. Did you mean one of these?"
        ), suggestions + [MENU_REPLY]

    logger.info('chatbot_unmatched message="%s" role=%s', message[:200], role or 'anonymous')
    return (
        "Sorry, I didn't catch that. 🤔\n\n"
        "I can help with registering, booking an assessment, required documents, fees, how long "
        "things take, your application status and certificates.\n\n"
        "Try rephrasing, pick a topic below, or ask to talk to a person."
    ), menu_for(role)[:5] + [{'label': '🙋 Talk to a person', 'intent': 'human'}]


def _rag_replies(result, role):
    """Quick replies for a RAG answer: jump to the intents it drew on."""
    replies, seen = [], set()
    for passage in result['sources']:
        intent_id = passage.intent
        label = SUGGESTION_LABELS.get(intent_id)
        if not label or intent_id in seen:
            continue
        seen.add(intent_id)
        replies.append({'label': label, 'intent': intent_id})
    replies = replies[:3]
    if not result['grounded']:
        replies.append({'label': '🙋 Talk to a person', 'intent': 'human'})
    replies.append(MENU_REPLY)
    return replies


def _payload(text, replies, intent_id='', confidence=0.0, **extra):
    return JsonResponse({
        'text': text,
        'quick_replies': replies,
        'intent': intent_id,
        'confidence': round(confidence, 2),
        **extra,
    })


# ── Endpoints ──────────────────────────────────────────────────────────────────
# The body carries up to MAX_HISTORY_TURNS prior messages for RAG follow-ups.
MAX_BODY_BYTES = 4096 + chatbot_rag.MAX_HISTORY_TURNS * (chatbot_rag.MAX_HISTORY_CHARS + 200)


def _read_body(request):
    if len(request.body) > MAX_BODY_BYTES:
        return '', '', []
    try:
        body = json.loads(request.body or '{}')
    except (json.JSONDecodeError, UnicodeDecodeError):
        return '', '', []
    if not isinstance(body, dict):
        return '', '', []
    message = body.get('message')
    intent_id = body.get('intent')
    history = body.get('history')
    message = message.strip()[:MAX_MESSAGE_LENGTH] if isinstance(message, str) else ''
    intent_id = intent_id.strip()[:50] if isinstance(intent_id, str) else ''
    history = history if isinstance(history, list) else []
    return message, intent_id, history


@require_POST
def chatbot_response(request):
    message, requested_intent, history = _read_body(request)
    user = request.user
    role = _role_of(user)

    # Opening the chat, or an empty submission.
    if not message and not requested_intent:
        docassist.reset_conversation(request.session)   # new chat → new DocAssist session
        greeting = INTENTS_BY_ID['greeting']
        return _payload(_resolve_text(greeting, role), menu_for(role), 'greeting', 1.0)

    # Quick-reply buttons name their intent explicitly — trust it when valid.
    if requested_intent:
        intent = INTENTS_BY_ID.get(requested_intent)
        if intent is not None and _intent_available(intent, role):
            rendered = _render(requested_intent, message, user)
            if rendered:
                return _payload(*rendered, intent_id=requested_intent, confidence=1.0)
        # Unknown/blocked intent: fall through to matching on the label text.

    intent_id, score, suggestions = match_intent(message, role)

    if intent_id not in RULE_FIRST_INTENTS:
        doc_result = docassist.answer(message, user, request.META, request.session)
        if doc_result:
            replies = []
            if intent_id and intent_id in SUGGESTION_LABELS and _intent_available(INTENTS_BY_ID[intent_id], role):
                replies.append({'label': SUGGESTION_LABELS[intent_id], 'intent': intent_id})
            replies.append(MENU_REPLY)
            return _payload(
                doc_result['text'], replies, 'docassist', 1.0,
                citations=doc_result['citations'],
            )

        result = chatbot_rag.answer(
            message, user, role, history, request.META, live_intents=set(LIVE_HANDLERS),
        )
        if result:
            return _payload(
                result['text'], _rag_replies(result, role), 'rag',
                1.0 if result['grounded'] else 0.3,
                sources=[p.id for p in result['sources']],
            )

    if intent_id:
        rendered = _render(intent_id, message, user)
        if rendered:
            return _payload(*rendered, intent_id=intent_id, confidence=min(score / 12.0, 1.0))

    text, replies = _fallback(message, role, _suggestion_replies(suggestions, role))
    return _payload(text, replies, '', score / 12.0 if score else 0.0)


@require_POST
def chatbot_feedback(request):
    """Record a 👍 / 👎 on a bot answer so weak answers can be improved."""
    try:
        body = json.loads(request.body or '{}')
    except (json.JSONDecodeError, UnicodeDecodeError):
        body = {}
    if not isinstance(body, dict):
        body = {}

    rating = body.get('rating')
    if rating not in ('up', 'down'):
        return JsonResponse({'ok': False, 'error': 'invalid rating'}, status=400)

    logger.info(
        'chatbot_feedback rating=%s intent=%s question="%s" role=%s',
        rating,
        str(body.get('intent') or '')[:50],
        str(body.get('question') or '')[:200],
        _role_of(request.user) or 'anonymous',
    )
    return JsonResponse({'ok': True})
