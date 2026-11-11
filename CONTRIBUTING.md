# Contributing to PWD Access Kenya

## Branch Strategy

| Branch | Purpose | Protected |
|--------|---------|-----------|
| `main` | Production — always deployable | Yes |
| `develop` | Integration — merged features ready for release | Yes |
| `feature/<name>` | New feature development | No |
| `fix/<name>` | Bug fixes for `develop` | No |
| `hotfix/<name>` | Emergency fixes branched from `main` | No |

### Rules
- Never commit directly to `main` or `develop`.
- All work goes through a Pull Request with at least one review.
- CI must pass before merge.
- `hotfix/*` branches are merged into **both** `main` and `develop`.

---

## Starting a New Feature

```bash
git checkout develop
git pull origin develop
git checkout -b feature/my-feature-name
```

When done:

```bash
git push origin feature/my-feature-name
# Open a PR targeting develop on GitHub
```

---

## Local Development Setup

### Option A — Docker (recommended)

```bash
cd pwd_django
cp .env.example .env        # fill in your values
docker compose up --build
```

- App: http://localhost:8000
- MailHog (email preview): http://localhost:8025

### Option B — Local Python

```bash
cd pwd_django
python -m venv venv
source venv/bin/activate     # Windows: venv\Scripts\activate
pip install -r requirements.txt
cp .env.example .env         # fill in your values
python manage.py migrate
python manage.py runserver
```

---

## Environment Variables

Copy `pwd_django/.env.example` to `pwd_django/.env` and fill in:

| Variable | Description |
|----------|-------------|
| `SECRET_KEY` | Django secret key (50+ random chars) |
| `DEBUG` | `True` for dev, `False` for production |
| `DB_ENGINE` | Leave blank for SQLite; set to `django.db.backends.mysql` for MySQL |
| `EMAIL_HOST_USER` | Gmail address |
| `EMAIL_HOST_PASSWORD` | Gmail App Password (not your account password) |

**Never commit `.env` to git.**

---

## Running Tests

```bash
cd pwd_django
python manage.py test
```

---

## Pull Request Checklist

- [ ] Branch is up to date with `develop`
- [ ] `python manage.py migrate --check` passes (no missing migrations)
- [ ] `python manage.py test` passes
- [ ] Templates use teal colour scheme consistently
- [ ] No secrets or `.env` values committed
- [ ] PR description explains *what* and *why*
