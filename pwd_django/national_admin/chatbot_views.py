"""National admin page for the PWD Assistant (chatbot) settings."""

from django.contrib import messages
from django.http import JsonResponse
from django.shortcuts import redirect, render
from django.template.loader import render_to_string
from django.utils.html import escape
from django.views.decorators.http import require_POST

from core import docassist
from core.audit import record_audit_event
from core.chatbot_config import clear_cache, env_defaults, load_config
from core.decorators import national_admin_required
from core.models import ChatbotSettings

from .forms import ChatbotSettingsForm


def mask_key(key):
    if not key:
        return ''
    return f'{key[:3]}…{key[-4:]}' if len(key) > 10 else '••••'


def _settings_row():
    row = ChatbotSettings.objects.filter(pk=1).first()
    if row is not None:
        return row
    # First visit: start from the environment defaults (not saved until submitted).
    env = env_defaults()
    return ChatbotSettings(
        docassist_enabled=env.docassist_enabled,
        docassist_base_url=env.docassist_base_url,
        docassist_timeout_seconds=env.docassist_timeout_seconds,
        rag_enabled=env.rag_enabled,
        llm_model=env.llm_model,
        max_per_window=env.max_per_window,
        window_seconds=env.window_seconds,
    )


def _test_connection(config):
    """Run /health, /validate-key and /documents and summarise them for the template."""
    results = docassist.health(config)
    health_status, health_body = results['health']
    key_status, key_body = results['validate_key']
    docs_status, docs_body = results['documents']

    reachable = health_status == 200
    key_ok = key_status == 200 and bool((key_body or {}).get('success'))
    if health_status is None:
        summary = 'Could not reach the DocAssist server. Check the base URL and the network.'
    elif not key_ok:
        summary = ('The server is reachable but rejected the API key.'
                   if key_status == 401 else f'The key check failed (HTTP {key_status}).')
    else:
        summary = 'Connected. The API key is valid.'

    documents = (docs_body or {}).get('documents') if docs_status == 200 else None
    return {
        'ok': reachable and key_ok,
        'summary': summary,
        'version': (health_body or {}).get('version', ''),
        'organization': (key_body or {}).get('organization', ''),
        'documents': documents if isinstance(documents, list) else [],
    }


def _key_change(old_key, new_key):
    if old_key and not new_key:
        return 'removed'
    return 'changed' if new_key != old_key else 'unchanged'


@national_admin_required
def chatbot_settings(request):
    row = _settings_row()
    env = env_defaults()
    saved_key = row.docassist_api_key
    test_result = ask_result = None

    if request.method == 'POST':
        action = request.POST.get('action', 'save')
        form = ChatbotSettingsForm(request.POST, instance=row, saved_api_key=saved_key)
        if form.is_valid():
            # Every valid submission is persisted, so a key typed before testing is
            # kept and never has to be entered again. Test/Ask then run on the saved
            # config; only an explicit Save is announced and written to the audit log.
            old_key = saved_key
            obj = form.save(commit=False)
            obj.updated_by = request.user
            obj.save()
            clear_cache()
            row, saved_key = obj, obj.docassist_api_key

            if action == 'save':
                record_audit_event(
                    'chatbot.settings_updated',
                    actor=request.user,
                    entity_type='chatbot_settings',
                    entity_id=1,
                    metadata={
                        'changed_fields': [f for f in form.changed_data
                                           if f not in ('docassist_api_key', 'clear_api_key')],
                        'api_key': _key_change(old_key, obj.docassist_api_key),
                    },
                )
                messages.success(request, 'Chatbot settings saved. New chat messages use them within 30 seconds.')
                return redirect('national_admin:chatbot_settings')

            config = load_config()
            if not config.docassist_api_key:
                messages.error(request, 'Enter an API key first.')
            elif action == 'test':
                test_result = _test_connection(config)
            elif action == 'ask':
                question = request.POST.get('test_question', '').strip()[:500]
                if not question:
                    messages.error(request, 'Type a question to try.')
                else:
                    reply = docassist.ask(docassist.mask_personal_numbers(question), config=config)
                    ask_result = {
                        'ok': reply is not None,
                        'answer': (reply or {}).get('answer', ''),
                        'citations': docassist.format_citations((reply or {}).get('citations')),
                    }
            form = ChatbotSettingsForm(instance=row, saved_api_key=saved_key)
    else:
        form = ChatbotSettingsForm(instance=row, saved_api_key=saved_key)

    return render(request, 'national/chatbot_settings.html', {
        'form': form,
        'row': row if row.pk else None,
        'live': load_config(),
        'saved_key_masked': mask_key(saved_key),
        'env_key_masked': mask_key(env.docassist_api_key),
        'test_result': test_result,
        'ask_result': ask_result,
        'test_question': request.POST.get('test_question', ''),
    })


def _error_html(message):
    return (f'<p role="alert" class="mt-2 text-sm text-red-700">'
            f'<i class="fas fa-times-circle mr-1"></i> {escape(message)}</p>')


@national_admin_required
@require_POST
def chatbot_settings_try(request):
    """AJAX endpoint for 'Test connection' and 'Try a question'.

    Persists the submitted settings (so a typed key is kept), then runs the
    action against the saved config and returns just the rendered result
    fragment, so the page updates in place instead of reloading.
    """
    row = _settings_row()
    saved_key = row.docassist_api_key
    form = ChatbotSettingsForm(request.POST, instance=row, saved_api_key=saved_key)
    if not form.is_valid():
        first_error = next(iter(form.errors.values()))[0] if form.errors else 'Please check the settings.'
        return JsonResponse({'ok': False, 'html': _error_html(first_error)}, status=400)

    obj = form.save(commit=False)
    obj.updated_by = request.user
    obj.save()
    clear_cache()

    config = load_config()
    if not config.docassist_api_key:
        return JsonResponse({'ok': False, 'html': _error_html('Enter an API key first.')})

    if request.POST.get('action') == 'ask':
        question = request.POST.get('test_question', '').strip()[:500]
        if not question:
            return JsonResponse({'ok': False, 'html': _error_html('Type a question to try.')})
        reply = docassist.ask(docassist.mask_personal_numbers(question), config=config)
        ask_result = {
            'ok': reply is not None,
            'answer': (reply or {}).get('answer', ''),
            'citations': docassist.format_citations((reply or {}).get('citations')),
        }
        html = render_to_string('national/_chatbot_ask_result.html', {'ask_result': ask_result})
        return JsonResponse({'ok': ask_result['ok'], 'html': html})

    test_result = _test_connection(config)
    html = render_to_string('national/_chatbot_test_result.html', {'test_result': test_result})
    return JsonResponse({'ok': test_result['ok'], 'html': html})
