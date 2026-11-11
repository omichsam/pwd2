# Audit Trail and Monitoring

Phase 5 adds an application audit trail and request-correlated structured logs.
These serve different purposes:

- Audit events preserve who performed a sensitive business or security action.
- Operational logs help maintainers diagnose failures and abnormal behavior.

Neither system should contain passwords, authentication tokens, full identity
numbers, email addresses, or clinical answers.

## Audit events

`AuditEvent` stores:

- A unique event ID and timestamp
- The actor reference and a snapshot of the actor's role
- The action and target type/ID
- The related assessment when applicable
- Previous and new workflow states
- A rejection reason when the workflow requires one
- The originating request ID
- Minimal non-clinical metadata

Recorded actions currently include:

- Medical completion, health approval/rejection, and county approval/rejection
- Successful and throttled login activity
- Officer activation and deactivation
- Hospital administrator and county officer creation
- County officer changes
- Hospital reassignment
- Designation assignment and deletion

Audit events cannot be changed or deleted through their model instance or the
Django admin. Database administrators remain technically capable of changing
database rows, so production database permissions and independent backups must
also protect the audit table.

## Request correlation

Every HTTP response includes an `X-Request-ID` UUID. The same ID is attached to
logs and audit events created during that request. Support staff can use it to
trace one failing request without searching for personal information.

Logs are emitted in a key/value format suitable for container log collectors:

```text
timestamp=... level=... logger=... request_id=... message=...
```

## Expected exception handling

Missing optional assessment-detail records are handled specifically as
`ObjectDoesNotExist`. Unexpected request exceptions are logged with a request
ID and then re-raised so Django returns the configured error response. Email
failures record the template and exception type without logging the recipient.

## Production operations

Forward stdout/stderr from the web container to a protected centralized logging
service. Configure alerts for at least:

- Repeated `login_throttled` events
- `unhandled_request_exception`
- Sustained `email_delivery_failed` events
- Unexpected spikes in `403`, `404`, `429`, or `500` responses
- Audit gaps or failed database writes

Restrict access to logs and audit events to specifically authorized staff.
Define and approve retention periods based on legal, medical, certification,
and privacy requirements. Backups must include the audit table.

An external monitoring provider is intentionally not hard-coded into the
application. The deployment owner must connect the structured output to the
approved organizational logging and alerting platform.
