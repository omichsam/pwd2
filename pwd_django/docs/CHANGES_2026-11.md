# Change log — November 2026

Branch: `claude/cranky-poincare-91c1df`. Covers the DocAssist chatbot integration,
the Document Checker, disability-type reclassification in medical review, a
responsive overhaul of the dashboards/forms/tables, and test-login tooling.

> **Migrations:** run `python manage.py migrate` — this branch adds
> `core/0017_chatbot_settings` and `core/0018_document_checker`.

---

## 1. PWD Assistant → DocAssist document Q&A

The chatbot now answers free-text questions from the organisation's documents via
the external **DocAssist** API, before falling back to the built-in engine.

- **Client:** [`core/docassist.py`](../core/docassist.py) — `POST /chat`, keeps the
  conversation `session_id` in the Django session (never the browser), masks ID/phone
  numbers, and shares the chatbot rate limit. Returns `None` on any failure so the
  chatbot keeps working.
- **Answer cleaning:** `clean_answer()` strips DocAssist's inline `[file.pdf, page 3]`
  markers (a clean `📄 Source:` line is shown instead), drops chatty filler, and trims
  to a short, whole-sentence answer (`MAX_ANSWER_CHARS = 450`).
- **Wiring:** [`core/chatbot_views.py`](../core/chatbot_views.py) tries DocAssist first,
  then Claude RAG, then rules.
- **Effective config:** [`core/chatbot_config.py`](../core/chatbot_config.py) merges the
  admin-edited row with environment defaults (cached 30 s).
- **Check command:** `python manage.py docassist_check [--ask "What is disability?"]`.

Full reference: [`docs/DOCASSIST.md`](DOCASSIST.md).

### Chatbot settings page (national admin)
`National admin → Chatbot Settings` (`/national/chatbot/`): toggle DocAssist/Claude,
set base URL, API key (write-only, masked), timeout, model and usage limit. **Test
connection** and **Try a question** run in place (AJAX, no reload) and persist the
settings first so the key is never asked for twice. Saves are audited
(`chatbot.settings_updated`) without logging the key.

Files: `national_admin/chatbot_views.py`, `national_admin/forms.py`,
`templates/national/chatbot_settings.html` (+ `_chatbot_test_result.html`,
`_chatbot_ask_result.html`).

---

## 2. Document Checker (medical review)

Documents uploaded during a medical assessment are screened for scan quality and
possible alteration via an external checker API. **Advisory only** — the review always
completes; officers see the result.

- **Client:** [`core/doc_checker.py`](../core/doc_checker.py) — `POST /v1/check`
  (multipart, stdlib only), plus `/health` + `/v1/model` for the settings test.
  `run_for_document()` never raises; failures are stored as an error result.
- **Config:** [`core/doc_checker_config.py`](../core/doc_checker_config.py) (same
  pattern as the chatbot config).
- **Models** ([`core/models.py`](../core/models.py), migration `0018`):
  - `DocumentCheckerSettings` — site-wide singleton.
  - `DocumentCheck` — one result per `Document`: `action`
    (`continue` / `request_rescan` / `manual_review`), `level`, `message`, quality,
    change score, raw JSON.
- **Medical integration** ([`medical/views.py`](../medical/views.py)): after a document
  is saved and the assessment submitted, the check runs **outside the DB transaction**;
  the officer gets a warning if a rescan or manual review is advised.
- **Result badge:** `templates/components/document_check_badge.html`, shown next to each
  document on the medical and county assessment views (Looks fine / Rescan needed /
  Manual review / Check unavailable).

### Document Checker settings page (national admin)
`National admin → Document Checker` (`/national/document-checker/`): enable, base URL,
API key (write-only), timeout, and a **Test connection** (AJAX) that reports the model
hash and warning level. Saves audited as `doc_checker.settings_updated`.

Files: `national_admin/doc_checker_views.py`, `doc_checker_forms.py`,
`templates/national/doc_checker_settings.html` (+ `_doc_checker_test_result.html`).

### Environment variables
```
DOC_CHECKER_BASE_URL=          # e.g. https://checker.example.org
DOC_CHECKER_API_KEY=           # sent as the X-API-Key header
DOC_CHECKER_TIMEOUT_SECONDS=45
```
A key saved on the settings page overrides the environment; a blank saved key falls back
to `DOC_CHECKER_API_KEY`.

---

## 3. Reclassification in medical review

The medical officer can now assess a **different disability type** than the citizen
booked ("reclassify by clinical findings"), in [`medical/views.py`](../medical/views.py):

- The assessment-form view no longer filters by the URL's disability type, so any type's
  form opens for a visible, pending assessment (the hospital/visibility boundary is
  unchanged — other hospitals still 404).
- On submit, if the chosen type differs from the booked one, the assessment's
  `disability_type` is updated, the detail is saved in that type's table, and the change
  is recorded (`assessment.reclassified`, with `from`/`to`). The officer sees a
  confirmation message.
- The selection grid on `templates/medical/view_assessment.html` explains that choosing a
  non-booked type reclassifies on submit.

---

## 4. Responsive overhaul (dashboards, forms, tables)

The dashboard shell (`dashboard_layout.html`) was already responsive (off-canvas sidebar,
hamburger). These changes fix the **content**.

- **Page-width overflow fix (shared layout):** the main area is a flex child; it now has
  `min-w-0` (two places in `templates/base/dashboard_layout.html`). Without it, a wide
  table forced the whole page past the viewport on phones, pushing card numbers and the
  second column off-screen. This one fix covers every dashboard and page.
- **Tables:** all 20 data tables got `min-w-[700px]` + `whitespace-nowrap`, so columns
  keep their natural width and the `overflow-x-auto` wrapper scrolls left/right to show
  everything (instead of columns crushing and never scrolling).
- **Medical assessment forms** (all 7): the fixed desktop two-pane shell stacks on mobile
  (`flex-col lg:flex-row`, full-width left nav), the page scrolls (a mobile CSS block
  relaxes the `100vh`/`overflow-hidden` shell), and paired field grids stack.
- **Charts (Chart.js):** national / county / medical dashboards now set
  `maintainAspectRatio: false` with a fixed-height container (`h-56 sm:h-64`), so charts
  stay compact instead of ballooning; the chart sections are **hidden on phones**
  (`hidden sm:grid` / `hidden sm:block`).
- **Compact mobile dashboards:** the resource-total cards (PWD Citizens / Hospitals /
  Officers) are hidden on phones (`hidden sm:block`); the Rejected card spans full width
  there.
- **Field grids** (assessment detail panels, register form, profile/create forms): grids
  with `gap-4`+ stack to one column on phones and expand at `sm:`/`lg:`.
- **Chatbot launcher icon:** dashboards show a robot icon (`fa-robot`); public pages keep
  the chat-bubble icon — via a `{% block chatbot_icon %}` in `base.html`.

> **Note:** the dev server runs with `--noreload` and **caches templates in memory**, so
> template edits only appear after a server **restart**. Keep a single `runserver` on a
> port.

---

## 5. Test logins

`python manage.py seed_test_users [--password ...]` creates one account per role with a
known password (refuses to run when `DEBUG=False` unless `--force`). See
[`docs/TEST_CREDENTIALS.md`](TEST_CREDENTIALS.md). Default password `Test@1234`:

| Role | Login ID |
|---|---|
| PWD Citizen | `99999999` (ID number) |
| Medical Officer | `TEST-MED-01` (license ID) |
| Health Officer | `TEST-HLT-01` |
| County Officer | `TEST-CO-01` |
| National Admin | `TEST-NAT-01` |

---

## Tests

New: `core/test_docassist.py`, `core/test_doc_checker.py`,
`national_admin/tests.py`, `national_admin/test_doc_checker_settings.py`, plus
reclassification, document-check and responsive-render cases in `core/tests.py`.
All external HTTP is mocked. Full suite: **155 tests, passing** (run under Python 3.12;
Python 3.14 + Django 4.2 breaks the test client, unrelated to this work).
