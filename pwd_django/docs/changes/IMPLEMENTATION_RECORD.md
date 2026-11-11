# Improvement Implementation Record

## Record status

- **Implementation period:** 2026-08-27
- **Phases covered:** 1 through 10
- **Engineering status:** Implemented
- **Release status:** Pending final environment and operational acceptance

This document is the consolidated record of changes delivered through the
project improvement plan. Detailed objectives, severity ratings, and completion
criteria remain in [`../IMPROVEMENT_PLAN.md`](../IMPROVEMENT_PLAN.md).

## Changes delivered

### 1. Baseline and test foundation

- Documented roles, ownership boundaries, and the assessment lifecycle.
- Added reusable test data and authentication/dashboard smoke tests for every role.
- Added project setup, testing, architecture, and deployment guidance.

### 2. Authorization and data protection

- Added consistent role and object-level authorization.
- Restricted applicant, hospital, officer, and county data to the correct scope.
- Converted state-changing links and endpoints to CSRF-protected POST requests.
- Added negative access-control and HTTP-method tests.

### 3. Production security

- Required safe production secrets and environment configuration.
- Added HTTPS, secure-cookie, proxy, security-header, and password policies.
- Added login throttling and upload type/size validation.
- Added production Nginx and security rollout documentation.

### 4. Workflow integrity

- Centralized assessment transitions in a transactional workflow service.
- Enforced valid states, actor permissions, reviewer scope, and required reasons.
- Added row locking and tests for allowed and prohibited transitions.
- Added a workflow-data audit command and documented historical-data remediation.

### 5. Audit trail and monitoring

- Added immutable audit events for workflow and sensitive account operations.
- Added request correlation identifiers and structured safe logging.
- Replaced silent broad exception handling in critical assessment paths.
- Documented log retention, access, alerting, and privacy requirements.

### 6. Certificate security

- Replaced predictable certificate identifiers with random UUID tokens.
- Centralized issuance, rendering, QR verification, revocation, and reissue.
- Reduced public verification output and added request throttling.
- Added certificate lifecycle and privacy regression tests.

### 7. Code quality and database integrity

- Centralized password changes and consistently applied password validators.
- Added identity, licence, telephone, and percentage validation.
- Added database constraints and indexes for critical rules and common queries.
- Protected assessment/certificate history from unsafe cascading deletion.
- Added Ruff and dependency-audit tooling.

### 8. Deployment and recovery

- Added bounded database startup waits and configurable entrypoint actions.
- Separated one-shot release tasks from scalable Gunicorn web processes.
- Changed the application container to run as a non-root user.
- Added liveness/readiness endpoints and Docker health checks.
- Added encrypted database/media backup and guarded restore scripts.
- Expanded CI with tests, migrations, linting, workflow auditing, dependency
  auditing, secret scanning, and an isolated restore exercise.

### 9. Accessibility and user experience

- Added skip links, landmarks, focus treatment, ARIA state, and reduced motion.
- Added accessible error summaries and error focus on important applicant forms.
- Added safe booking-draft recovery and duplicate-submit prevention.
- Enforced one active assessment per applicant at the database level.
- Added accessible server-side pagination and related regression tests.

### 10. Performance and continuous improvement

- Added bounded server-side pagination to high-growth operational lists.
- Preserved filters between result pages.
- Replaced repeated dashboard count queries with aggregate queries.
- Added response timing headers and privacy-safe duration logging.
- Defined initial service objectives and privacy rules for reporting.

## Database migrations introduced

The improvement work includes account and core migrations for validation,
indexes, audit events, certificate lifecycle data, hearing-percentage
constraints, and the one-active-assessment guarantee. Deployments must run all
pending migrations through the one-shot release process after taking a verified
backup.

## Verification recorded

- Django system checks pass.
- Migration drift check reports no uncommitted model changes.
- Updated templates compile successfully.
- Diff whitespace validation passes.
- The full suite passed under Python 3.11 before the final accessibility and
  performance additions.
- Targeted database-integrity and pagination tests pass after the final changes.

## Remaining release gates

These are validation and operational tasks, not known implementation failures:

1. Run the complete test suite under the supported Python 3.11 runtime when the
   Docker engine is available.
2. Apply all migrations, including `core.0016_one_active_assessment`, in staging.
3. Complete manual WCAG 2.2 AA checks with keyboard navigation, NVDA or
   VoiceOver, zoom, mobile layouts, contrast, reduced motion, and critical forms.
4. Perform and retain evidence from an isolated encrypted backup/restore drill.
5. Review CI lint, dependency, secret, migration, and MySQL results.
6. Validate staging HTTPS, health checks, email delivery, shared throttling cache,
   and certificate issue/revoke/reissue behavior.
7. Resolve historical assessment ID `2` through the approved remediation process.
8. Establish monitoring and collect a 30-day performance baseline against the
   documented service objectives.

## Release decision

Phases 1–10 are recorded as engineering-complete. Production acceptance should
only be granted after the release gates above have evidence and stakeholder
sign-off. Do not infer missing reviewer attribution when remediating historical
workflow data.
