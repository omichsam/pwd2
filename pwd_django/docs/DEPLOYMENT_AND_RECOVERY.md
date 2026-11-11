# Deployment, Backup, and Recovery

## Production release sequence

1. Back up the database and media and copy the encrypted backup off-host.
2. Build an immutable image from the reviewed commit.
3. Run the Compose `release` service once. It waits at most
   `DB_WAIT_TIMEOUT`, migrates the database, and collects static files.
4. Start or replace the Gunicorn `web` service only after `release` succeeds.
5. Confirm `/health/live/` and `/health/ready/` through HTTPS.
6. Run a certificate verification and one authorized dashboard smoke test.

The web container runs as the unprivileged `app` user and never migrates the
database. Do not enable `RUN_MIGRATIONS` on horizontally scaled web services.

## Health semantics

- `/health/live/` confirms the Django process can answer HTTP. It deliberately
  does not test dependencies.
- `/health/ready/` executes a minimal database query and returns HTTP 503 when
  the instance should be removed from traffic.

Neither endpoint returns environment, exception, or database details.

## Backups

Install `mysqldump`, `mysql`, `openssl`, `gzip`, and `tar` on a dedicated backup
runner. Schedule `scripts/backup.sh` with secrets supplied by a protected secret
store. The script creates an AES-256 encrypted database dump and media archive,
plus SHA-256 checksums.

Recommended initial objectives:

- RPO: 24 hours, with more frequent database backups if transaction volume warrants it.
- RTO: 4 hours.
- Retention: 14 daily, 8 weekly, and 12 monthly encrypted backup sets.
- Store at least one copy in a different account or region with immutable retention.

Never store `BACKUP_ENCRYPTION_PASSWORD` beside the backups. Monitor job exit
status, backup age, and backup size; a successful scheduler invocation without
usable artifacts is not a successful backup.

## Restore drill

Restore only into an isolated database and empty media directory first. Set
`BACKUP_SET` to one exact directory and set `RESTORE_CONFIRM` to that directory's
base name. The restore script verifies checksums before writing.

After restoration:

```powershell
python manage.py check
python manage.py migrate --check
python manage.py audit_workflow_data --fail-on-error
python manage.py test
```

Record duration, restored record counts, certificate sampling results, operator,
backup timestamp, and any corrective actions. A production restore requires an
approved incident plan and a final backup of the affected target.

## Rollback

Application rollback means deploying the previous compatible image. Do not
automatically reverse migrations containing data changes. Review migration
reversibility and restore from the pre-release backup when a safe forward fix is
not possible.
