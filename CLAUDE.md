# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**PWD Access** — a Persons with Disabilities (PWD) assessment and certification management system for Kenyan counties. PWDs book hospital appointments, undergo structured disability assessments by medical officers, get reviewed by health officers, and are finally approved by county officers before receiving a disability certificate.

## Local Setup

**Prerequisites:** XAMPP (Apache + MySQL/MariaDB)

1. Start Apache and MySQL in the XAMPP Control Panel.
2. In phpMyAdmin (`http://localhost/phpmyadmin`), create a database named `pwd`.
3. Import `db/pwd.sql` into the `pwd` database.
4. Place the project folder inside `C:\xampp\htdocs\` (folder must be named `pwd2` to match existing paths).
5. Access the app at `http://localhost/pwd2/`.

**Database connection** is configured in `files/db_connect.php` (procedural `mysqli`) and `files/Database.php` (OOP class). Both hardcode `localhost / root / "" / pwd` — no `.env` file is used.

## User Roles & Login Flow

All roles share the single login page at `login.php`. After `password_verify()`, the session stores `$_SESSION['logged_in']`, `$_SESSION['type']`, and either `$_SESSION['pwd_user']` or `$_SESSION['official_user']`, then redirects:

| Role | `type` value | Dashboard |
|---|---|---|
| PWD (citizen) | `PWD` | `pwd/index.php` |
| Medical Officer | `medical_officer` | `medical/index.php` |
| Health Officer | `health_officer` | `health/index.php` |
| County Officer | `county_officer` | `supervisor/index.php` |

Medical and health officers require `active = 1` in the `officials` table (set by the county officer via `supervisor/Approve_Officer.php`).

## Architecture

### Directory-per-role pattern

Each role has its own self-contained directory with identical internal structure:

```
{role}/
  index.php           — dashboard
  assessment.php      — list/manage assessments
  complete_assessment.php
  view_assessment.php
  view_profile.php
  edit_profile.php
  change_password.php
  {disability}_print.php  — printable certificate per disability type
  files/              — shared includes: header.php, footer.php, nav.php, sidebar.php, logout.php
  partials/           — disability-specific view fragments (*_view.php)
  process/            — form handlers (*_process.php) — medical/ only
```

### Assessment lifecycle

```
PWD books appointment (pwd/application.php)
  → status: 'pending'
Medical Officer fills disability form (medical/{disability}_form.php → medical/process/{disability}_process.php)
  → status: 'checked'
Health Officer reviews (health/view_assessment.php)
  → status: 'approved_by_health_officer'
County Officer approves (supervisor/view_assessment.php)
  → status: 'approved_by_county_officer'
```

The `assessments` table is the central record. Disability-specific data goes into separate tables (e.g., `physical_disability_assessment`, `hearing_disability_assessments`).

### Disability types

Six disability types, each with its own form, process handler, partials view, and print page:
- Physical, Hearing, Visual, Mental, Maxillofacial, Chronic

### Session guard pattern

Every role's `files/header.php` checks `$_SESSION['logged_in']` and redirects to `../login.php` if not set. It also populates `$conn` (from `db_connect.php`) and `$pwdUser` (from the appropriate session key) for use across all pages in that role.

## Key Database Tables

| Table | Purpose |
|---|---|
| `users` | PWD citizens — login with `id_number` |
| `officials` | All staff roles — login with `license_id`; `type` determines role |
| `hospitals` | Facilities linked to counties and sub-counties |
| `counties` / `sub_counties` | Administrative geography |
| `assessments` | Central record per PWD per assessment cycle |
| `physical_disability_assessment` | Detail tables per disability type |
| `hearing_disability_assessments` | (and similarly for visual, mental, etc.) |
| `documents` | Uploaded supporting files linked to assessments |

The SQL schema and seed data is in `db/pwd.sql`.

## Frontend Stack

- Bootstrap 5 (both CDN and local copies in `assets/modules/bootstrap/`)
- Font Awesome 6 (CDN + local in `assets/modules/fontawesome/`)
- SweetAlert2 (CDN) — used for all user feedback modals
- DataTables (CDN) — paginated tables
- html2pdf.js (CDN) — client-side PDF generation for print pages
- QRCode.js (CDN) — QR codes on certificates
- jQuery (CDN)

Local asset copies live in `assets/modules/`. Pages load both CDN and local versions of some libraries (redundant includes exist in several headers).

## Coding Patterns

- **Procedural PHP + raw mysqli** throughout (not PDO, not an ORM). Use `mysqli_prepare` / `mysqli_stmt_bind_param` for all user-supplied values.
- **No URL rewriting for pages** — `.htaccess` strips `.php` extensions for cleaner URLs (e.g., `href="register"` resolves to `register.php`).
- **Inline PHP + HTML** — pages mix PHP logic and HTML in a single file; form processing happens at the top before HTML output.
- **`ob_start()` / `ob_end_flush()`** is used on some pages that need to `header()` redirect after output has started.
- **`$_SESSION['pwd_user']`** holds the full user array for PWD users; **`$_SESSION['official_user']`** holds it for all official roles. Always read from the session array, never re-query for the current user within a page.
- File uploads go to `medical/uploads/` (or the role equivalent); allowed extensions are `pdf`, `jpg`, `jpeg`, `png`.

## Supervisor-specific Features

The `supervisor/` (county officer) directory has additional management pages not present in other roles:
- `Add_Hospital.php` / `List_Hospitals.php` — manage hospital registry
- `List_Counties.php` / `List_subCounties.php` / `add_subcounty.php` — geography management
- `List_Officer.php` / `Approve_Officer.php` — activate/assign medical and health officers to hospitals
