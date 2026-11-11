# Test credentials (development / QA only)

These accounts are for local testing. **Do not create or use them in production.**

Create or refresh them with:

```bash
python manage.py seed_test_users
# optional: choose your own password
python manage.py seed_test_users --password "My-Test-Pass-1"
```

The command needs counties and hospitals to exist first (`python manage.py seed_data`).
It is safe to re-run: it updates the accounts and resets their passwords each time, and it
refuses to run when `DEBUG=False` unless you pass `--force`.

## Accounts

All accounts share the password **`Test@1234`** (unless you passed `--password`).

On the login page, pick the matching role. The **login ID** differs by role:
PWD citizens log in with their **ID number**; all officials log in with their **license ID**.

| Role | Login ID | Password | Dashboard |
|---|---|---|---|
| PWD Citizen | `99999999` | `Test@1234` | `/pwd/` |
| Medical Officer | `TEST-MED-01` | `Test@1234` | `/medical/` |
| Health Officer | `TEST-HLT-01` | `Test@1234` | `/health/` |
| County Officer | `TEST-CO-01` | `Test@1234` | `/supervisor/` |
| National Admin | `TEST-NAT-01` | `Test@1234` | `/national/` |

The chatbot settings page (National admin → Chatbot Settings) is reached with the
**National Admin** account.

## Other seeded accounts

`python manage.py seed_data --admin` also creates a superuser **`admin` / `admin1234`**
(county officer role). Use it for the Django admin at `/admin/`.

Passwords for any other pre-existing seeded accounts are not stored in the repo. Reset one with:

```bash
python manage.py changepassword <username>
```
