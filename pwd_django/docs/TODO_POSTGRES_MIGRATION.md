# TODO — Migrate the database to PostgreSQL

Switch the app's database from MySQL (server) / SQLite (local) to PostgreSQL.

**Current state:** `DB_ENGINE` env var drives the backend
([`pwd_system/settings.py`](../pwd_system/settings.py)). Server runs MySQL 8.0
(Docker); local dev falls back to SQLite. The code is standard Django ORM — **no raw
SQL, no MySQL-specific features** — so this is mostly config, not code.

**Estimate:** fresh install ≈ 30–60 min, low risk. Carrying over existing MySQL data
adds ≈ 2–4 hours.

---

## A. Config & driver (fresh install path)

- [ ] `requirements.txt`: replace `mysqlclient>=2.2.0` with `psycopg[binary]>=3.1`
      (keep `mysqlclient` only if MySQL must still be supported).
- [ ] `.env` (local) and `.env.production`: set
      `DB_ENGINE=django.db.backends.postgresql`, `DB_PORT=5432`,
      and add `POSTGRES_DB` / `POSTGRES_USER` / `POSTGRES_PASSWORD`.
- [ ] Update `.env.example` and `.env.production.example` to the Postgres values.
- [ ] Confirm **no** `settings.py` change is needed (it already reads `DB_ENGINE`).

## B. Docker / infrastructure

- [ ] `docker-compose.yml`: change the `db` service `image: mysql:8.0` → `postgres:16`.
- [ ] `docker-compose.prod.yml`: same image change.
- [ ] Swap the DB env vars (`MYSQL_ROOT_PASSWORD` / `MYSQL_DATABASE` / `MYSQL_USER` /
      `MYSQL_PASSWORD` → `POSTGRES_DB` / `POSTGRES_USER` / `POSTGRES_PASSWORD`).
- [ ] Update the volume (`mysql_data` → `pg_data`, mounted at
      `/var/lib/postgresql/data`).
- [ ] Update the healthcheck (`mysqladmin ping` → `pg_isready -U <user>`).
- [ ] Keep `DB_HOST=db` and `depends_on: db` as-is.

## C. Apply & verify

- [ ] Build/install: `pip install -r requirements.txt` (and rebuild the web image).
- [ ] `python manage.py migrate` against Postgres — the `RunPython` migrations
      (`core/0014`, `core/0016`) run unchanged; `0016`'s conditional `UniqueConstraint`
      maps to a Postgres partial unique index.
- [ ] `python manage.py seed_data` / `seed_test_users` as needed.
- [ ] Run the test suite (155 tests) against Postgres.
- [ ] Smoke-test: login per role, book + review an assessment, chatbot, document
      checker, certificate verify.

## D. Data migration (only if keeping existing MySQL data)

- [ ] Choose a method: `dumpdata` → `loaddata` (small/medium data) **or** `pgloader`
      (direct MySQL → Postgres, better for large data).
- [ ] Watch the known conversion points:
  - [ ] Booleans (MySQL `tinyint(1)` → Postgres `boolean`).
  - [ ] Datetimes / timezones.
  - [ ] Text encoding (ensure UTF-8).
  - [ ] Reset sequences after load (`sqlsequencereset` or pgloader handles it).
- [ ] Verify row counts and spot-check key tables (`accounts_customuser`,
      `core_assessment`, `core_certificate`).

## E. Review points (low risk, but test)

- [ ] **Case sensitivity:** Postgres string `=` is case-sensitive; MySQL's default
      collation is case-insensitive. Explicit `__iexact` lookups (e.g. county-name
      checks) are fine; review logins and any implicit case-insensitive equality.
- [ ] `JSONField` (`core.AuditEvent.metadata`, `core.DocumentCheck.raw`) → Postgres
      `jsonb`; confirm reads/writes behave.
- [ ] `db.sqlite3` is dev-only — no action; optionally untrack it and gitignore it.

## F. Docs

- [ ] Update `docs/DEPLOYMENT_AND_RECOVERY.md`, `README.md`, and the root `CLAUDE.md`
      database notes to say PostgreSQL.
