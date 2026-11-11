# Certificate Security and Operations

## Lifecycle

A certificate is issued transactionally when a county officer completes the
final assessment approval. Issuance requires recorded medical, health, and
county reviewers. Each certificate has a public certificate number and an
independent random UUID verification token; neither is derived from applicant
identity or an assessment ID.

Only one non-revoked certificate may exist for an assessment. Reissue revokes
the current certificate, creates a new token, and links the replacement to the
superseded record. Revocation and reissue require a county officer in the
assessment's county and a written reason. Both operations create audit events.

## Public verification

QR codes point to the random token, not to an applicant or assessment ID. The
public result discloses only validity, certificate number, issuing county, and
issue date. It does not expose holder identity, identification numbers,
disability category, clinical findings, facilities, or reviewer names.

Verification requests are limited per client address. Configure
`CERTIFICATE_VERIFY_MAX_ATTEMPTS` and
`CERTIFICATE_VERIFY_WINDOW_SECONDS` according to the capacity and traffic
profile of the production cache. A shared cache such as Redis is required when
running multiple application instances.

## Migration and recovery

Migration `0014_certificate` backfills only historically certified assessments
that have all three reviewer references. Incomplete legacy records are skipped
instead of inventing attribution. Complete the controlled workflow remediation
before issuing a certificate for any skipped record.

Before applying the migration in production, back up the database. Rollback
removes the new certificate records, so export certificate and audit history
before reversing it. Old deterministic certificate URLs are intentionally no
longer accepted.
