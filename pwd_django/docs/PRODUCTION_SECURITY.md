# Production Security Configuration

This guide covers the Phase 3 production controls. It does not replace an
infrastructure security review or the organization's privacy obligations for
identity and medical data.

## Required deployment files

1. Copy `.env.production.example` to `.env.production`.
2. Replace every `CHANGE_ME` value.
3. Set the real public hostname in `ALLOWED_HOSTS`, `CSRF_TRUSTED_ORIGINS`, and
   `nginx/nginx.prod.conf`.
4. Place the TLS certificate and private key at:

   - `nginx/certs/fullchain.pem`
   - `nginx/certs/privkey.pem`

The production environment file and certificate directory are ignored by Git.

Generate the Django secret with a cryptographically secure tool. For example:

```powershell
python -c "import secrets; print(secrets.token_urlsafe(64))"
```

Production startup fails if the Django secret is insecure or shorter than 50
characters.

## HTTPS rollout

The production Nginx configuration redirects HTTP to HTTPS, accepts TLS 1.2 or
TLS 1.3, and tells Django that proxied requests are secure.

Before enabling a long HSTS duration:

1. Verify that the hostname and certificate chain are correct.
2. Verify all pages, static files, media, callbacks, and email links over HTTPS.
3. Initially set `SECURE_HSTS_SECONDS=300`.
4. Increase it gradually after successful monitoring.
5. Enable subdomains or preload only after reviewing every affected subdomain.

HSTS can make a misconfigured site inaccessible until the browser policy
expires, so it should not be enabled blindly.

## Cookies and proxy trust

Production defaults require secure session and CSRF cookies. The application
trusts `X-Forwarded-Proto` in production because the bundled Nginx proxy
overwrites that header. Do not expose Gunicorn directly to the public network.

## Login throttling

The application temporarily limits repeated failures for the same role,
identifier, and client address. Configure:

- `LOGIN_MAX_ATTEMPTS`
- `LOGIN_LOCKOUT_SECONDS`

The default local-memory cache applies per Gunicorn worker. A shared cache or
edge rate limiter is required for consistent enforcement across multiple
workers or replicas. The response deliberately uses the same message for
unknown and inactive accounts.

## Upload controls

Assessment attachments are limited by extension, declared content type, and
size. Production should additionally use malware scanning and store uploads in
a private object store or protected download endpoint. Nginx must never execute
files from the media directory.

Default accepted types are PDF, PNG, and JPEG, with a 10 MB file limit.

## Content Security Policy

The templates currently depend on inline styles/scripts and external frontend
resources. Enabling a strict Content Security Policy immediately would break
existing pages. Introduce CSP in report-only mode, remove inline dependencies,
then enforce a nonce- or hash-based policy in a focused follow-up change.

## Release validation

Run the production security check with production-like environment variables:

```powershell
python manage.py check --deploy
```

Also verify HTTPS from outside the deployment network and confirm that HTTP,
cookies, CSRF protection, uploads, login throttling, and certificate downloads
behave as expected.
