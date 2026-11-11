# PWD Access Kenya

PWD Access Kenya is a Django application for managing disability assessment,
review, approval, certification, and public certificate verification.

## Main capabilities

- PWD citizen registration and applications
- Hospital selection and disability assessment booking
- Medical and health-officer review
- County-level approval and certification
- Hospital, county, and national administration dashboards
- Notifications and transactional email
- Printable certificates with public QR-based verification

The current roles and assessment lifecycle are documented in
[`docs/CURRENT_ROLES_AND_WORKFLOW.md`](docs/CURRENT_ROLES_AND_WORKFLOW.md).
Planned engineering improvements are tracked in
[`docs/IMPROVEMENT_PLAN.md`](docs/IMPROVEMENT_PLAN.md).
The consolidated record of delivered changes and remaining release gates is in
[`docs/changes/IMPLEMENTATION_RECORD.md`](docs/changes/IMPLEMENTATION_RECORD.md).

## Technology

- Python 3.11 and Django 4.2
- SQLite for simple local development or MySQL through environment configuration
- Django templates and Tailwind-based interfaces
- Docker Compose for containerized development and production deployment
- WhiteNoise for static files

## Local setup

### Python environment

```powershell
python -m venv venv
.\venv\Scripts\Activate.ps1
pip install -r requirements.txt
Copy-Item .env.example .env
python manage.py migrate
python manage.py runserver
```

On Linux or macOS, activate the environment with `source venv/bin/activate`
and copy the environment file with `cp .env.example .env`.

### Docker

```powershell
Copy-Item .env.example .env
docker compose up --build
```

The development application is exposed at `http://localhost:8000`. MailHog is
available at `http://localhost:8025` when using the development Compose stack.

Review every value in `.env` before starting the service. Never commit `.env`.

Use Python 3.11 for local development. The Docker image uses Python 3.11, and
Django 4.2's test tooling is not compatible with the Python 3.14 runtime.

## Validation and tests

Run the project checks and tests from the Django project directory:

```powershell
.\venv\Scripts\python.exe manage.py check
.\venv\Scripts\python.exe manage.py makemigrations --check --dry-run
.\venv\Scripts\python.exe manage.py test
```

Before a production release, also run:

```powershell
.\venv\Scripts\python.exe manage.py check --deploy
```

Production security warnings are tracked as critical work in Phase 3 of the
improvement plan.

## Project structure

| Path | Responsibility |
|---|---|
| `accounts/` | User model, authentication, registration, and account forms |
| `core/` | Counties, hospitals, assessments, notifications, and disability details |
| `pwd_portal/` | PWD citizen dashboard, applications, profiles, and certificates |
| `medical/` | Medical-officer assessment workflow |
| `health/` | Health-officer review workflow |
| `supervisor/` | County-officer administration and certification |
| `hospital_admin/` | Hospital administration |
| `national_admin/` | National reporting and county administration |
| `templates/` | Shared and role-specific Django templates |
| `pwd_system/` | Django settings and root URL configuration |

## Documentation

- [Current roles and workflow](docs/CURRENT_ROLES_AND_WORKFLOW.md)
- [Improvement plan](docs/IMPROVEMENT_PLAN.md)
- [System changes and updates](docs/changes/README.md)
- [Improvement implementation record](docs/changes/IMPLEMENTATION_RECORD.md)
- [Production security configuration](docs/PRODUCTION_SECURITY.md)
- [Workflow data remediation](docs/WORKFLOW_DATA_REMEDIATION.md)
- [Audit trail and monitoring](docs/AUDIT_AND_MONITORING.md)
- [Certificate security and operations](docs/CERTIFICATE_SECURITY.md)
- [Code quality and database integrity](docs/CODE_QUALITY_AND_INTEGRITY.md)
- [Deployment, backup, and recovery](docs/DEPLOYMENT_AND_RECOVERY.md)
- [Accessibility and user experience](docs/ACCESSIBILITY_AND_UX.md)
- [Performance and reporting](docs/PERFORMANCE_AND_REPORTING.md)
- [Contribution guide](../CONTRIBUTING.md)

## Important production note

The Phase 1–10 engineering changes are implemented, but production release still
depends on the validation and operational gates in the implementation record.
Production deployment requires real TLS certificates, review of every value in
`.env.production`, a verified backup, successful migrations, and staging
acceptance. Do not use real identity or medical data until those gates pass.
