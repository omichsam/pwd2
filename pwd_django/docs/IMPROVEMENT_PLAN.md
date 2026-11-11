# PWD Access Kenya Improvement Plan

## 1. Purpose

This document defines a phased plan for improving the security, reliability,
maintainability, accessibility, and production readiness of the PWD Access
Kenya system.

The plan prioritizes protection of identity and medical information, correct
authorization, and reliable assessment and certification workflows before
visual enhancements or optional features.

## 2. Priority and change ratings

### Severity

| Rating | Meaning |
|---|---|
| **P0 — Critical** | Must be addressed before production use with real personal or medical data. |
| **P1 — High** | Required for a dependable and supportable initial release. |
| **P2 — Medium** | Improves maintainability, accessibility, and user experience. |
| **P3 — Low** | Optimization or enhancement after the system is stable. |

### Change size

| Size | Meaning |
|---|---|
| **Small** | Localized configuration or code change with limited impact. |
| **Medium** | Changes several files or one important subsystem. |
| **Large** | Cross-cutting change affecting workflows, data, or multiple applications. |

## 3. Current baseline

At the time this plan was created:

- `python manage.py check` passes.
- `python manage.py makemigrations --check --dry-run` reports no missing migrations.
- `python manage.py test` originally discovered zero tests; Phase 1 now provides six baseline tests.
- `python manage.py check --deploy` reports five production security warnings.
- All eight application test modules are still placeholders.
- Several views silently catch broad exceptions.
- The project does not have a main application README.

Before implementation begins, rerun these commands and record any changes:

```powershell
.\venv\Scripts\python.exe manage.py check
.\venv\Scripts\python.exe manage.py check --deploy
.\venv\Scripts\python.exe manage.py makemigrations --check --dry-run
.\venv\Scripts\python.exe manage.py test
```

## 4. Phased roadmap

### Phase 1 — Baseline, documentation, and test foundation

**Severity:** P0  
**Change size:** Small to medium  
**Implementation risk:** Low  
**Status:** Completed on 2026-08-27

#### Objectives

- Preserve current behavior before changing core workflows.
- Document user roles, data ownership, and the assessment lifecycle.
- Establish a repeatable test environment.

#### Work items

- Document all user roles and their permitted actions.
- Document the current assessment statuses and transitions.
- Create shared test factories or fixtures for users, hospitals, counties, and assessments.
- Add basic tests for registration, login, and dashboard access.
- Add a main README covering setup, architecture, testing, and deployment.
- Record the current Django check and migration results in the pull request.

#### Completion criteria

- Tests run consistently on a clean development environment.
- At least one successful authentication test exists for every role.
- The current assessment workflow is documented and reviewed.
- No unplanned database changes are produced.

#### Delivered

- Added a reusable organization and role test-data mixin.
- Added successful authentication coverage for all six roles.
- Added anonymous-access and dashboard-rendering smoke tests.
- Documented the current roles, ownership relationships, and assessment states.
- Added the main project README with setup and validation instructions.
- Fixed an existing invalid template filter that prevented the hospital administrator dashboard from rendering.
- Verified all six tests using the project's Python 3.11 Docker runtime.

---

### Phase 2 — Authorization and data-access protection

**Severity:** P0  
**Change size:** Medium  
**Implementation risk:** Medium  
**Status:** Completed on 2026-08-27

#### Objectives

- Ensure users can only access records within their assigned scope.
- Prevent URL manipulation and direct endpoint access from bypassing permissions.

#### Work items

- Test every role-protected view and endpoint.
- Add object-level authorization for assessments, hospitals, counties, and users.
- Restrict applicants to their own applications and assessments.
- Restrict medical officers to assigned work.
- Restrict hospital administrators to their hospital.
- Restrict supervisors and county officers to their geographic scope.
- Ensure all state-changing operations require POST and CSRF validation.
- Return appropriate `403` or `404` responses without exposing record existence.

#### Completion criteria

- Cross-user and cross-organization access tests pass.
- No state change is possible through a GET request.
- Role checks and object-level checks are applied consistently.
- Unauthorized requests cannot reveal sensitive personal or medical data.

#### Delivered

- Added centralized role-aware assessment visibility rules.
- Return `403 Forbidden` when an authenticated user attempts to enter another role's area.
- Preserve `404 Not Found` for records outside a user's ownership, hospital, or county scope.
- Limit completed medical and health reviews to the officer who performed them.
- Apply medical designation restrictions to direct assessment URLs as well as work queues.
- Prevent medical form URLs from changing an assessment's disability type.
- Require POST for logout, notification updates, activation toggles, sub-county creation, and designation deletion.
- Replaced notification mutation links with CSRF-protected POST forms.
- Added twelve negative authorization and HTTP-method tests, bringing the suite to eighteen passing tests.

---

### Phase 3 — Production security hardening

**Severity:** P0  
**Change size:** Small to medium  
**Implementation risk:** Medium  
**Status:** Completed on 2026-08-27

#### Objectives

- Remove insecure production defaults.
- Protect sessions, authentication, uploads, and network traffic.

#### Work items

- Require a strong environment-provided `SECRET_KEY` in production.
- Require `DEBUG=False` in production.
- Configure production `ALLOWED_HOSTS` and `CSRF_TRUSTED_ORIGINS`.
- Enable secure session and CSRF cookies.
- Configure HTTPS redirection and the reverse-proxy SSL header.
- Enable HSTS only after HTTPS is fully verified.
- Add login rate limiting and account lockout protections.
- Define upload type and size restrictions.
- Review security headers and Content Security Policy.
- Ensure secrets and `.env` files cannot be committed.

#### Completion criteria

- `python manage.py check --deploy` has no unexplained warnings.
- Production refuses to start without required secrets.
- Authentication and CSRF behavior are tested through the production proxy.
- HTTP-to-HTTPS behavior is verified before enabling long-lived HSTS.

#### Delivered

- Added strict boolean, integer, and list environment parsing.
- Production startup now rejects missing, insecure, or short Django secrets.
- Added production defaults for HTTPS redirection, secure cookies, HSTS, proxy handling, referrer policy, MIME sniffing protection, and clickjacking protection.
- Restored Django's full standard password-validator set.
- Added separate development and production environment templates.
- Added a TLS 1.2/1.3 Nginx production configuration and certificate mounts.
- Added per-identity/client login throttling with neutral failure messages.
- Added assessment attachment extension, declared content type, and size validation.
- Added a production security and HSTS rollout guide.
- Added nine security configuration, throttling, and upload tests, bringing the suite to twenty-seven passing tests.
- Reduced `check --deploy` from five warnings to two intentionally retained HSTS subdomain/preload warnings. These controls remain disabled until every production subdomain has been verified as HTTPS-only.

---

### Phase 4 — Assessment workflow integrity

**Severity:** P0  
**Change size:** Large  
**Implementation risk:** High  
**Status:** Completed on 2026-08-27; one historical record requires approved remediation

#### Objectives

- Make assessment status transitions explicit and enforceable.
- Prevent skipped stages, duplicate decisions, and invalid certification.

#### Work items

- Define an approved workflow and all rejection/reassignment paths.
- Introduce a centralized assessment transition service.
- Validate current status, actor permission, and required data on every transition.
- Require reasons for rejection and reassignment where appropriate.
- Prevent repeated or conflicting decisions.
- Add database transactions around multi-record changes.
- Add tests for every permitted and prohibited transition.
- Plan data migration for any existing inconsistent records.

#### Expected workflow

```text
submitted
  -> assigned
  -> medically reviewed
  -> health approved
  -> county approved
  -> certified
```

The final workflow must also define rejection, correction, reassignment,
revocation, and resubmission behavior.

#### Completion criteria

- All status changes pass through one validated transition layer.
- Invalid or skipped transitions are rejected.
- Concurrent requests cannot create contradictory outcomes.
- Tests cover the complete successful workflow and all rejection paths.

#### Delivered

- Added one transactional workflow service for every persisted assessment status change.
- Lock assessment rows during transitions to prevent contradictory concurrent decisions.
- Enforce source state, target state, actor role, hospital/county scope, medical designation, and reviewer history.
- Require saved clinical details before medical completion.
- Require a clear reason for health- or county-level rejection.
- Prevent duplicate decisions and skipped medical, health, or county stages.
- Wrap clinical details, attachment creation, and medical completion in one database transaction.
- Added a read-only `audit_workflow_data` command with a CI/deployment failure mode.
- Added service and endpoint integration coverage, bringing the suite to forty-two passing tests.
- Documented controlled remediation for one pre-existing inconsistent certified record (assessment ID `2`). No historical attribution was guessed or automatically rewritten.

---

### Phase 5 — Audit trail, error handling, and monitoring

**Severity:** P0/P1  
**Change size:** Medium  
**Implementation risk:** Medium  
**Status:** Completed on 2026-08-27; external alert routing remains a deployment responsibility

#### Objectives

- Make sensitive actions traceable.
- Ensure failures are visible without leaking confidential data.

#### Work items

- Add an append-only audit-event model.
- Record the actor, action, target, old status, new status, reason, and timestamp.
- Record assignment, approval, rejection, certification, revocation, and login-security events.
- Replace broad `except Exception: pass` blocks with specific handling.
- Add structured application logging and request correlation IDs.
- Configure error monitoring and alerting.
- Define rules that prevent identity and medical data from entering logs.

#### Completion criteria

- Every important workflow action creates an audit event.
- Unexpected exceptions are logged and visible to maintainers.
- Users receive safe and helpful error messages.
- Logs do not contain passwords, tokens, full identity numbers, or medical details.

#### Delivered

- Added an append-only audit-event model and migration with actor-role snapshots.
- Write workflow audit events inside the same transaction as assessment status changes.
- Record successful/throttled authentication and sensitive user-administration changes.
- Add per-request UUID correlation to responses, audit records, and structured logs.
- Replace silent broad assessment-detail exception handlers with specific expected exceptions.
- Log unexpected request failures and email delivery failures without recipient or clinical data.
- Make audit events read-only in Django admin and immutable through model instances.
- Document log collection, access control, retention, backup, and recommended alert rules.
- Added four audit/correlation tests, bringing the suite to forty-six passing tests.

---

### Phase 6 — Certificate and verification security

**Severity:** P1  
**Change size:** Medium  
**Implementation risk:** Medium
**Status:** Completed on 2026-08-27

#### Objectives

- Make certificates difficult to predict, forge, or misuse.
- Centralize certificate creation and verification.

#### Work items

- Replace predictable MD5-derived identifiers with stored random tokens, UUIDs, or signed identifiers.
- Extract duplicated certificate-generation code into a service.
- Store certificate issue and revocation history.
- Add certificate revocation and reissue behavior.
- Rate-limit public verification requests.
- Ensure public verification exposes only the minimum necessary information.
- Add tests for valid, invalid, altered, revoked, and superseded certificates.

#### Completion criteria

- Certificate identifiers cannot be practically enumerated.
- Revoked certificates are clearly reported as invalid.
- Certificate generation has a single implementation.
- Verification behavior is covered by automated tests.

#### Delivered

- Replaced deterministic MD5 certificate IDs with stored random UUID verification tokens.
- Added a certificate model with issue, revocation, supersession, and responsible-officer history.
- Issue certificates transactionally during validated county approval.
- Centralized certificate rendering and QR generation for applicant and county views.
- Added county-scoped, reason-required revocation and reissue operations with audit events.
- Rate-limit public token verification and reduce its output to non-sensitive certificate metadata.
- Backfill only legacy certified assessments with a complete reviewer chain.
- Added certificate lifecycle, privacy, and rate-limit regression coverage.

---

### Phase 7 — Code quality and database integrity

**Severity:** P1  
**Change size:** Large  
**Implementation risk:** Medium
**Status:** Completed on 2026-08-27

#### Objectives

- Reduce duplicated code and make business rules easier to maintain.
- Enforce critical data rules at the model and database levels.

#### Work items

- Move workflow, notification, assignment, and certificate logic out of views.
- Introduce focused service modules and reusable permission functions.
- Consolidate duplicated profile and password-management behavior.
- Add model validation for identity numbers, licences, phone numbers, and percentages.
- Add uniqueness and check constraints where required.
- Add indexes for frequently filtered fields such as status, county, hospital, officer, and date.
- Review deletion behavior and protect important historical records.
- Add linting, formatting, type-checking where practical, and dependency scanning.

#### Completion criteria

- Views primarily validate requests, call services, and return responses.
- Critical rules cannot be bypassed by avoiding HTML forms.
- Duplicate business logic is removed or clearly justified.
- Query performance is measured for major dashboards and work queues.

#### Delivered

- Consolidated five role-specific password-change implementations into one audited account service.
- Enforce Django's configured password validators consistently across every supported password-change portal.
- Added server-side identity, licence, and telephone format validation.
- Enforced hearing disability percentages at both model and database levels.
- Added composite indexes for assessment queues, dashboards, ownership lookups, and officer filtering.
- Protect assessment and certificate history from cascading applicant-account deletion.
- Added focused Ruff correctness configuration and a separate dependency-audit development toolset.
- Added five validation, constraint, password-policy, and audit regression tests.
- Documented deletion policy, migration rollout, and incremental code-quality adoption.

---

### Phase 8 — Deployment, CI/CD, backup, and recovery

**Severity:** P1  
**Change size:** Medium  
**Implementation risk:** Medium
**Status:** Completed on 2026-08-27; production restore drills remain a recurring operational requirement

#### Objectives

- Make deployment repeatable, observable, and recoverable.

#### Work items

- Add a timeout and actionable errors to the database wait in `entrypoint.sh`.
- Separate development and production startup behavior.
- Run the production application with Gunicorn or an equivalent WSGI server.
- Run containers as a non-root user.
- Add health and readiness endpoints.
- Avoid running migrations concurrently from multiple replicas.
- Add CI for checks, migrations, tests, linting, secret scanning, and dependency scanning.
- Automate encrypted database and media backups.
- Define retention, recovery-point, and recovery-time objectives.
- Perform and document a test restoration.

#### Completion criteria

- A clean deployment can be reproduced from documentation.
- Failed dependencies cause a clear, bounded startup failure.
- CI blocks merges when required checks fail.
- A backup has been successfully restored in a test environment.

#### Delivered

- Added bounded database startup waits with actionable failures and environment validation.
- Split production release migrations/static collection from horizontally scalable Gunicorn startup.
- Run the application image as an unprivileged operating-system user.
- Added public, minimal liveness and database-readiness endpoints and container health checks.
- Gate Nginx startup on application readiness in the production Compose stack.
- Expanded CI with correctness linting, Django and migration checks, workflow auditing, dependency auditing, and repository secret scanning.
- Added encrypted database/media backup and guarded checksum-verifying restore scripts.
- Added an isolated encrypted backup/restore exercise to CI, including database and media verification.
- Documented release order, rollback policy, monitoring, retention, RPO/RTO, and restore-drill evidence.

---

### Phase 9 — Accessibility and user experience

**Severity:** P1/P2  
**Change size:** Large  
**Implementation risk:** Medium
**Status:** Engineering work completed on 2026-08-27; formal assistive-technology acceptance remains a pre-production gate

#### Objectives

- Make the service usable by people with a wide range of disabilities.
- Reduce errors and abandonment in long assessment workflows.

#### Work items

- Target WCAG 2.2 AA.
- Test keyboard-only navigation and common screen readers.
- Improve labels, error associations, focus states, contrast, headings, and landmarks.
- Add error summaries to long forms.
- Add draft saving and safe resume behavior.
- Add progress indicators and review-before-submit screens.
- Prevent accidental duplicate submissions.
- Add a clear status timeline and required next action.
- Test zoom, mobile layouts, reduced motion, and print output.

#### Completion criteria

- Critical user journeys work without a mouse.
- Accessibility testing reports no unresolved critical violations.
- Draft and recovery behavior is tested.
- Users can understand their current status and next required action.

#### Delivered

- Added a consistent keyboard skip link, main landmarks, visible focus treatment, and reduced-motion behavior.
- Improved accessible names and expanded-state semantics for shared dashboard controls.
- Added focusable, linked error summaries and return-to-error-step behavior on critical applicant forms.
- Applied the configured strong-password policy during both citizen and official registration.
- Added safe session-scoped booking draft recovery without storing clinical findings.
- Prevent repeated browser submissions and enforce one active assessment per applicant in the database.
- Added keyboard-operable server pagination for large operational lists.
- Added accessibility, password-policy, duplicate-submission, and landmark regression tests.
- Documented the manual WCAG 2.2 AA and assistive-technology release gate.

---

### Phase 10 — Performance, reporting, and continuous improvement

**Severity:** P2/P3  
**Change size:** Variable  
**Implementation risk:** Low to medium
**Status:** Initial optimization and continuous-improvement framework completed on 2026-08-27

#### Objectives

- Improve performance and operational decision-making after the core system is stable.

#### Work items

- Measure and optimize slow queries and dashboard response times.
- Add pagination to all potentially large lists.
- Review `select_related` and `prefetch_related` usage.
- Move slow email or document tasks to background jobs if needed.
- Add privacy-aware operational reporting.
- Collect user feedback and track support issues.
- Define service-level objectives and review them regularly.

#### Completion criteria

- Performance targets are documented and measured.
- Large datasets do not cause unbounded pages or queries.
- Reports do not expose data beyond the viewer's authorization scope.
- Future work is prioritized using measured problems and user feedback.

#### Delivered

- Added reusable 50-record server pagination for high-growth assessment, certificate, hospital, and officer lists.
- Preserve active filters across accessible previous/next page navigation.
- Replaced repeated medical and health dashboard count queries with filtered aggregate queries.
- Added per-request application timing headers and structured duration logging without logging token-bearing paths.
- Defined initial availability, latency, error-rate, and notification-freshness objectives.
- Documented privacy controls for operational reports and a measured backlog process.
- Added bounded pagination, invalid-page, and response-timing regression coverage.

## 5. Recommended delivery sequence

| Delivery period | Planned phases |
|---|---|
| Week 1 | Phase 1: baseline, documentation, and test foundation |
| Weeks 2–3 | Phase 2: authorization and data-access protection |
| Week 4 | Phase 3: production security hardening |
| Weeks 5–6 | Phase 4: assessment workflow integrity |
| Week 7 | Phases 5–6: audit trail, monitoring, and certificate security |
| Week 8 | Phases 7–8: refactoring, CI/CD, deployment, and recovery |
| Weeks 9–10 | Phase 9: accessibility and user experience |
| Ongoing | Phase 10: performance and continuous improvement |

The schedule is an initial estimate for a small team. Each phase should be
reviewed and re-estimated after the previous phase reveals the actual scope.

## 6. Delivery rules

- Use a separate branch and focused pull request for each coherent change.
- Add or update tests in the same pull request as behavior changes.
- Do not combine schema changes, workflow redesign, and visual redesign in one pull request.
- Include migration and rollback notes for database changes.
- Back up production data before migrations or workflow changes.
- Review authorization whenever a new endpoint, role, or model is introduced.
- Treat unresolved P0 issues as release blockers.
- Require stakeholder approval for changes to certification eligibility or workflow policy.

## 7. Definition of done for the improvement program

The initial improvement program is complete when:

- Critical workflows and permission boundaries have automated coverage.
- Production security checks have no unexplained warnings.
- Assessment transitions are centralized, validated, transactional, and audited.
- Certificates use secure identifiers and support revocation.
- CI reliably checks tests, migrations, security, and code quality.
- Production deployment, monitoring, backup, and restoration are documented and tested.
- Critical user journeys meet the agreed accessibility standard.
- Remaining P2 and P3 work is captured in a maintained backlog.
