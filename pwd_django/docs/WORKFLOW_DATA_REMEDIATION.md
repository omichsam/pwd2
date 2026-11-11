# Workflow Data Remediation

Phase 4 introduced enforced reviewer history and a read-only consistency audit:

```powershell
python manage.py audit_workflow_data
python manage.py audit_workflow_data --fail-on-error
```

The first command reports issues. The second is intended for CI or deployment
gates and exits unsuccessfully when issues exist.

## Current audit result

The local development database contains one pre-existing certified assessment,
ID `2`, without all required medical, health, and county reviewer references.
No automatic correction was made because reviewer attribution is a historical
medical and certification fact that must not be guessed.

## Required remediation process

1. Back up the database.
2. Restrict access to the affected certificate while it is investigated.
3. Review authoritative hospital and county records for assessment ID `2`.
4. Identify the actual medical, health, and county reviewers.
5. If the decision is valid, update only the missing reviewer references using
   an approved, logged administrative data migration.
6. If the decision cannot be substantiated, revoke or return the assessment
   through the approved operational process once revocation is implemented.
7. Rerun `audit_workflow_data --fail-on-error`.
8. Record who approved and executed the remediation and preserve the backup.

Do not assign placeholder officers merely to make the audit pass.

## New workflow rules

The successful path is now enforced as:

```text
pending
  -> checked
  -> approved_by_health_officer
  -> approved_by_county_officer
```

Health rejection is allowed only from `checked`, and county rejection is
allowed only from `approved_by_health_officer`. Both require a non-empty reason.
Each action verifies the actor's role and hospital or county scope while holding
a database row lock inside a transaction.

Correction, resubmission, revocation, and certificate reissue are not silently
inferred from the existing states. They require approved policy and will be
implemented in the later certificate/audit phases.
