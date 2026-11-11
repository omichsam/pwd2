# DocAssist integration (PWD Assistant)

The PWD Assistant chatbot can answer free-text questions from the organisation's
documents through the **DocAssist API v1**.

## How a chatbot message is answered

1. **Rule-first intents** (greeting, "check my status", work queues, contact, talk to a
   person) are always answered by the rule engine, from the database. Personal data
   never leaves the server.
2. **DocAssist** (`core/docassist.py`) handles other free text when `DOCASSIST_API_KEY` is set.
   Citations are added under the answer, for example `📄 Source: policy.pdf (p. 3)`.
3. **Claude RAG** (`core/chatbot_rag.py`) is used if DocAssist is off or fails, when an
   Anthropic key is set.
4. The **rule-based engine** is the final fallback.

## Settings page

National admins manage the chatbot at **National admin → Chatbot Settings** (`/national/chatbot/`).
The page lets them:

- turn DocAssist and Claude answers on or off
- set the DocAssist base URL, API key and timeout, the Claude model, and the usage limit
- **Test connection**, which checks the server and key and lists the available documents
- **Try a question**, which works with unsaved values

The API key is write-only. It is never shown again, only masked (`sk_…1234`). Leaving the field
blank keeps the saved key, and a checkbox removes it. Each save is recorded in the audit trail.
The audit entry lists which fields changed and whether the key changed, but never the key itself.

Saved values are stored in `core.ChatbotSettings` (one row) and override the environment
variables below. Until the page is first saved, the environment values apply. If no key is saved
on the page, `DOCASSIST_API_KEY` is used. Changes reach every server process within 30 seconds.

## Configuration (`.env`)

| Variable | Default | Notes |
| --- | --- | --- |
| `DOCASSIST_API_KEY` | *(blank, so DocAssist is off)* | Organisation key from DocAssist's `/keys/` page (`sk_…`). |
| `DOCASSIST_BASE_URL` | `http://38.242.200.57:8001/api/v1` | The test server. |
| `DOCASSIST_ENABLED` | `true` when a key is set | Set `false` to turn DocAssist off and keep the key. |
| `DOCASSIST_TIMEOUT_SECONDS` | `20` | First answer after a DocAssist restart can be slow. |

DocAssist shares the chatbot rate limit settings (`CHATBOT_RAG_MAX_PER_WINDOW`
per `CHATBOT_RAG_WINDOW_SECONDS`) per user or IP. It has its own counter.

## Conversations

DocAssist keeps context by `session_id`. The id is stored in the Django session
(`docassist_session_id`), never in the browser, so follow-ups keep context. It is cleared
when the chat is opened fresh or the user clicks **Clear chat history**. If DocAssist
rejects an unknown or expired id with a 404, the client starts a new conversation.

## Privacy

- Long digit runs (ID and phone numbers) are masked before sending.
- No user identifiers, roles or database answers are sent, only the question text.
- The test server uses plain **HTTP**, so the API key and questions are not encrypted.
  Use an HTTPS base URL in production.

## Checking the connection

```bash
python manage.py docassist_check
python manage.py docassist_check --ask "What is disability?"
```

This calls `/health`, `/validate-key` and `/documents`, and with `--ask` it also calls `/chat`.

## Tests

`python manage.py test core.test_docassist`. The HTTP layer is mocked, so no network is needed.
