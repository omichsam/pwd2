"""Document Checker API client.

Screens a document (scan quality + possible tampering) via an external REST API:

    GET  {base}/health            → {"status": "ok"}            (no key)
    GET  {base}/v1/model          → {"sha256", "warning_level"} (checks the key)
    POST {base}/v1/check          → the result (synchronous, ~5-40s)

The result is advisory ("Screening aid only"). `run_for_document()` records it on
a `DocumentCheck` row and never raises: a failed or disabled check just leaves the
document as it is, so the medical workflow is unaffected.

Config comes from `doc_checker_config` (national-admin settings, env fallback).
"""

import json
import logging
import mimetypes
import urllib.error
import urllib.request
import uuid

from .doc_checker_config import get_config

logger = logging.getLogger(__name__)

# Fields copied from a successful /v1/check result onto the DocumentCheck row.
_ACTIONS = {'continue', 'request_rescan', 'manual_review'}


def is_enabled():
    return get_config().active


def _request(method, path, config=None, body=None, content_type=None):
    """Plain GET/POST. Returns (status, parsed-json | None) or (None, None)."""
    config = config or get_config()
    url = config.base_url.rstrip('/') + path
    headers = {'Accept': 'application/json', 'X-API-Key': config.api_key}
    if content_type:
        headers['Content-Type'] = content_type
    req = urllib.request.Request(url, data=body, method=method, headers=headers)
    try:
        with urllib.request.urlopen(req, timeout=config.timeout_seconds) as resp:
            status, raw = resp.status, resp.read()
    except urllib.error.HTTPError as exc:
        status, raw = exc.code, exc.read()
    except (urllib.error.URLError, TimeoutError, OSError) as exc:
        logger.warning('doc_checker connection_error path=%s error=%s', path, exc)
        return None, None
    try:
        return status, json.loads(raw or b'{}')
    except (json.JSONDecodeError, UnicodeDecodeError):
        logger.warning('doc_checker invalid_json path=%s status=%s', path, status)
        return status, None


def _encode_multipart(field, filename, content, content_type):
    boundary = uuid.uuid4().hex
    pre = (f'--{boundary}\r\n'
           f'Content-Disposition: form-data; name="{field}"; filename="{filename}"\r\n'
           f'Content-Type: {content_type}\r\n\r\n').encode('utf-8')
    post = f'\r\n--{boundary}--\r\n'.encode('utf-8')
    return f'multipart/form-data; boundary={boundary}', pre + content + post


def health(config=None):
    """For the settings page: /health (reachable) and /v1/model (key valid)."""
    return {'health': _request('GET', '/health', config),
            'model': _request('GET', '/v1/model', config)}


def check_bytes(content, filename, config=None):
    """Send file bytes to /v1/check and return the parsed result dict, or None."""
    config = config or get_config()
    if not config.active or not content:
        return None
    content_type = mimetypes.guess_type(filename)[0] or 'application/octet-stream'
    ctype, body = _encode_multipart('file', filename or 'document', content, content_type)
    status, data = _request('POST', '/v1/check', config, body=body, content_type=ctype)
    if status != 200 or not isinstance(data, dict) or data.get('action') not in _ACTIONS:
        detail = (data or {}).get('error', {}).get('code', '') if isinstance(data, dict) else ''
        logger.warning('doc_checker check_failed status=%s error=%s', status, detail)
        return None
    return data


def _result_fields(data):
    """Map a /v1/check result onto DocumentCheck column values."""
    quality = data.get('quality') or {}
    change = data.get('change') or {}
    return dict(
        status='done',
        action=data.get('action', ''),
        level=data.get('level', ''),
        message=(data.get('message') or '')[:2000],
        quality_category=quality.get('category', '') or '',
        quality_advice=(quality.get('advice') or '')[:2000],
        change_score=change.get('score'),
        worst_page=data.get('worst_page'),
        page_count=data.get('page_count'),
        check_id=(data.get('check_id') or '')[:40],
        cached=bool(data.get('cached')),
        error='',
        raw=data,
    )


def run_for_document(document, config=None):
    """Check `document`'s file and store a DocumentCheck. Returns it, or None.

    Never raises — any failure is recorded as an error result so the caller's
    workflow is never interrupted.
    """
    from .models import DocumentCheck

    config = config or get_config()
    if not config.active:
        return None
    try:
        document.file.open('rb')
        try:
            content = document.file.read()
        finally:
            document.file.close()
        filename = document.file.name.rsplit('/', 1)[-1]
        data = check_bytes(content, filename, config)
        if data is None:
            fields = dict(status='error', error='The document checker did not return a result.', raw={})
        else:
            fields = _result_fields(data)
    except Exception as exc:                       # never break the upload
        logger.exception('doc_checker run_for_document failed document=%s', document.pk)
        fields = dict(status='error', error=str(exc)[:200], raw={})

    check, _ = DocumentCheck.objects.update_or_create(document=document, defaults=fields)
    logger.info('doc_checker recorded document=%s status=%s action=%s',
                document.pk, check.status, check.action)
    return check
