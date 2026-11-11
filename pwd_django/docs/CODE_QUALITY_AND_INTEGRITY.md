# Code Quality and Database Integrity

## Delivered in Phase 7

- One password-change workflow now serves the applicant, medical, health,
  county, and hospital-administrator portals.
- Password changes use all configured Django password validators and create an
  audit event after success.
- Identity, licence, and phone fields have server-side format validators.
- Hearing impairment percentages are constrained to `0` through `100` in both
  Django validation and the database.
- Composite indexes cover common assessment queue, dashboard, and officer-list
  filters.
- Assessments now protect their applicant record from deletion. Certificate
  records already protect assessments, while reviewer references remain
  nullable so staff departures do not destroy clinical history.

## Deletion policy

Do not delete a user who owns assessments. Define and approve a retention and
anonymization process that preserves required clinical, audit, and certificate
history before implementing account erasure. Related uploaded documents and
clinical-detail rows continue to follow their assessment lifecycle.

## Developer quality checks

Install development tools separately from production dependencies:

```powershell
pip install -r requirements-dev.txt
ruff check .
pip-audit -r requirements.txt
```

The initial Ruff configuration enables correctness-focused rules. Broader
formatting and style rules should be introduced incrementally so unrelated
legacy formatting does not obscure functional changes. Dependency audit
results are time-sensitive and must be reviewed during every release build.

## Migration rollout

Back up the production database, audit existing out-of-range percentage values,
and then apply migrations `accounts.0005` and `core.0015`. The current audited
development data contains no conflicting values. Rollback removes indexes and
constraints but does not rewrite application data.
