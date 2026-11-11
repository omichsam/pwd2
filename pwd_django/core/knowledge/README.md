# Chatbot knowledge documents

Markdown files in this folder are added to the PWD Assistant's retrieval index
(`core/chatbot_rag.py`) alongside the answers in `core/chatbot_kb.py`.

- One topic per file, e.g. `ncpwd_benefits.md`, `tax_exemption.md`.
- Split long files with `## Heading` lines; each section is retrieved on its own,
  so keep sections short (a few paragraphs) and self-contained.
- Write only verified, official information. The assistant is instructed to answer
  strictly from this content, so anything written here is what users will be told.
- Never include personal data.
- Restart the server after adding or editing a file (the index is built once per process).

This README is not indexed.

## Configuration

| Setting (env var)             | Default                           | Purpose |
|-------------------------------|-----------------------------------|---------|
| `CHATBOT_RAG_ENABLED`         | on if `ANTHROPIC_API_KEY` is set  | Turn RAG answers on/off |
| `ANTHROPIC_API_KEY`           | —                                 | Claude API credential |
| `CHATBOT_LLM_MODEL`           | `claude-opus-5`                   | Model used to write answers |
| `CHATBOT_RAG_TIMEOUT_SECONDS` | `15`                              | Per-request API timeout |
| `CHATBOT_RAG_MAX_PER_WINDOW`  | `20`                              | Model calls per user/IP per window |
| `CHATBOT_RAG_WINDOW_SECONDS`  | `300`                             | Rate-limit window |

Without a key, or when a call fails or is rate-limited, the assistant answers with
its rule-based engine exactly as before.
