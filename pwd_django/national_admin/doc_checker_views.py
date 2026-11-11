"""National admin page for the Document Checker API settings."""

from django.contrib import messages
from django.http import JsonResponse
from django.shortcuts import redirect, render
from django.template.loader import render_to_string
from django.utils.html import escape
from django.views.decorators.http import require_POST

from core import doc_checker
from core.audit import record_audit_event
from core.doc_checker_config import clear_cache, env_defaults, load_config
from core.decorators import national_admin_required
from core.models import DocumentCheckerSettings

from .doc_checker_forms import DocumentCheckerSettingsForm


def mask_key(key):
    if not key:
        return ''
    return f'{key[:3]}…{key[-4:]}' if len(key) > 10 else '••••'


def _settings_row():
    row = DocumentCheckerSettings.objects.filter(pk=1).first()
    if row is not None:
        return row
    env = env_defaults()
    return DocumentCheckerSettings(
        enabled=env.enabled, base_url=env.base_url, timeout_seconds=env.timeout_seconds)


def _test_connection(config):
    """GET /health and /v1/model; summarise for the template."""
    results = doc_checker.health(config)
    health_status, health_body = results['health']
    model_status, model_body = results['model']

    reachable = health_status == 200 and (health_body or {}).get('status') == 'ok'
    key_ok = model_status == 200 and isinstance((model_body or {}).get('sha256'), str)
    if health_status is None:
        summary = 'Could not reach the document checker. Check the base URL and the network.'
    elif not reachable:
        summary = f'The server answered but is not healthy (HTTP {health_status}).'
    elif not key_ok:
        summary = ('The server is reachable but rejected the API key.'
                   if model_status == 401 else f'The key check failed (HTTP {model_status}).')
    else:
        summary = 'Connected. The API key is valid.'

    warning = (model_body or {}).get('warning_level') if key_ok else None
    return {
        'ok': reachable and key_ok,
        'summary': summary,
        'model_sha': ((model_body or {}).get('sha256') or '')[:12] if key_ok else '',
        'warning_level': warning,
    }


def _key_change(old_key, new_key):
    if old_key and not new_key:
        return 'removed'
    return 'changed' if new_key != old_key else 'unchanged'


@national_admin_required
def doc_checker_settings(request):
    row = _settings_row()
    env = env_defaults()
    saved_key = row.api_key
    test_result = None

    if request.method == 'POST':
        form = DocumentCheckerSettingsForm(request.POST, instance=row, saved_api_key=saved_key)
        if form.is_valid():
            old_key = saved_key
            obj = form.save(commit=False)
            obj.updated_by = request.user
            obj.save()
            clear_cache()
            row, saved_key = obj, obj.api_key

            if request.POST.get('action', 'save') == 'save':
                record_audit_event(
                    'doc_checker.settings_updated',
                    actor=request.user,
                    entity_type='doc_checker_settings',
                    entity_id=1,
                    metadata={
                        'changed_fields': [f for f in form.changed_data
                                           if f not in ('api_key', 'clear_api_key')],
                        'api_key': _key_change(old_key, obj.api_key),
                    },
                )
                messages.success(request, 'Document checker settings saved. New uploads use them within 30 seconds.')
                return redirect('national_admin:doc_checker_settings')

            config = load_config()
            if not config.api_key:
                messages.error(request, 'Enter an API key first.')
            else:
                test_result = _test_connection(config)
            form = DocumentCheckerSettingsForm(instance=row, saved_api_key=saved_key)
    else:
        form = DocumentCheckerSettingsForm(instance=row, saved_api_key=saved_key)

    return render(request, 'national/doc_checker_settings.html', {
        'form': form,
        'row': row if row.pk else None,
        'live': load_config(),
        'saved_key_masked': mask_key(saved_key),
        'env_key_masked': mask_key(env.api_key),
        'test_result': test_result,
    })


def _error_html(message):
    return (f'<p role="alert" class="mt-2 text-sm text-red-700">'
            f'<i class="fas fa-times-circle mr-1"></i> {escape(message)}</p>')


@national_admin_required
@require_POST
def doc_checker_settings_try(request):
    """AJAX 'Test connection': persist settings (keeps the key), then test."""
    row = _settings_row()
    saved_key = row.api_key
    form = DocumentCheckerSettingsForm(request.POST, instance=row, saved_api_key=saved_key)
    if not form.is_valid():
        first_error = next(iter(form.errors.values()))[0] if form.errors else 'Please check the settings.'
        return JsonResponse({'ok': False, 'html': _error_html(first_error)}, status=400)

    obj = form.save(commit=False)
    obj.updated_by = request.user
    obj.save()
    clear_cache()

    config = load_config()
    if not config.api_key:
        return JsonResponse({'ok': False, 'html': _error_html('Enter an API key first.')})

    test_result = _test_connection(config)
    html = render_to_string('national/_doc_checker_test_result.html', {'test_result': test_result})
    return JsonResponse({'ok': test_result['ok'], 'html': html})
