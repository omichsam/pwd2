from unittest import mock

from django.core.cache import cache
from django.test import TestCase, override_settings
from django.urls import reverse

from accounts.models import CustomUser
from core.chatbot_config import get_config
from core.models import AuditEvent, ChatbotSettings
from core.test_utils import TEST_PASSWORD

URL = reverse('national_admin:chatbot_settings')
TRY_URL = reverse('national_admin:chatbot_settings_try')

ENV = dict(DOCASSIST_ENABLED=False, DOCASSIST_API_KEY='', CHATBOT_RAG_ENABLED=False,
           DOCASSIST_BASE_URL='http://env.test/api/v1', CHATBOT_LLM_MODEL='claude-env')


def form_data(**overrides):
    data = {
        'action': 'save',
        'docassist_enabled': 'on',
        'docassist_base_url': 'https://docassist.test/api/v1',
        'docassist_api_key': '',
        'docassist_timeout_seconds': '20',
        'llm_model': 'claude-opus-5',
        'max_per_window': '20',
        'window_seconds': '300',
    }
    data.update(overrides)
    return {k: v for k, v in data.items() if v is not None}


@override_settings(**ENV)
class ChatbotSettingsPageTests(TestCase):
    @classmethod
    def setUpTestData(cls):
        cls.admin = CustomUser.objects.create_user(
            username='national', password=TEST_PASSWORD, role='national_admin', is_approved=True)
        cls.county_officer = CustomUser.objects.create_user(
            username='county', password=TEST_PASSWORD, role='county_officer', is_approved=True)

    def setUp(self):
        cache.clear()
        self.addCleanup(cache.clear)   # cached chatbot config must not outlive the test DB
        self.client.force_login(self.admin)

    def test_only_national_admin_can_open(self):
        self.client.force_login(self.county_officer)
        self.assertEqual(self.client.get(URL).status_code, 403)
        self.client.logout()
        self.assertEqual(self.client.get(URL).status_code, 302)

    def test_first_visit_shows_environment_defaults(self):
        response = self.client.get(URL)
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, 'http://env.test/api/v1')
        self.assertContains(response, 'Not saved yet')
        self.assertFalse(ChatbotSettings.objects.exists())

    def test_save_stores_settings_and_applies_to_chatbot(self):
        response = self.client.post(URL, form_data(docassist_api_key='sk_secret_12345'))
        self.assertRedirects(response, URL)
        row = ChatbotSettings.objects.get()
        self.assertEqual(row.docassist_api_key, 'sk_secret_12345')
        self.assertEqual(row.updated_by, self.admin)

        config = get_config()
        self.assertTrue(config.docassist_active)
        self.assertEqual(config.docassist_base_url, 'https://docassist.test/api/v1')

        event = AuditEvent.objects.get(action='chatbot.settings_updated')
        self.assertEqual(event.metadata['api_key'], 'changed')
        self.assertNotIn('sk_secret_12345', str(event.metadata))

    def test_api_key_is_never_rendered_and_blank_keeps_it(self):
        self.client.post(URL, form_data(docassist_api_key='sk_secret_12345'))
        page = self.client.get(URL)
        self.assertNotContains(page, 'sk_secret_12345')
        self.assertContains(page, 'sk_…2345')

        self.client.post(URL, form_data(docassist_timeout_seconds='30'))
        row = ChatbotSettings.objects.get()
        self.assertEqual(row.docassist_api_key, 'sk_secret_12345')
        self.assertEqual(row.docassist_timeout_seconds, 30)

    def test_clear_api_key(self):
        self.client.post(URL, form_data(docassist_api_key='sk_secret_12345'))
        self.client.post(URL, form_data(clear_api_key='on'))
        self.assertEqual(ChatbotSettings.objects.get().docassist_api_key, '')
        self.assertFalse(get_config().docassist_active)

    @override_settings(DOCASSIST_API_KEY='sk_env_key_9999')
    def test_blank_saved_key_falls_back_to_environment(self):
        self.client.post(URL, form_data())
        config = get_config()
        self.assertEqual(config.docassist_api_key, 'sk_env_key_9999')
        self.assertEqual(config.docassist_key_source, 'environment')

    def test_invalid_values_rejected(self):
        response = self.client.post(URL, form_data(docassist_timeout_seconds='500', docassist_base_url='not a url'))
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, 'between 1 and 60')
        self.assertFalse(ChatbotSettings.objects.exists())

    @mock.patch('core.docassist._request')
    def test_connection_saves_form_values_so_key_is_kept(self, req):
        req.side_effect = [
            (200, {'status': 'healthy', 'version': '3.4.1'}),
            (200, {'success': True, 'organization': 'Test Organization'}),
            (200, {'success': True, 'documents': [{'filename': 'policy.pdf', 'pages': 14}]}),
        ]
        response = self.client.post(URL, form_data(action='test', docassist_api_key='sk_new_key_0000'))
        self.assertContains(response, 'Connected. The API key is valid.')
        self.assertContains(response, 'Test Organization')
        self.assertContains(response, 'policy.pdf')
        config = req.call_args.kwargs['config']
        self.assertEqual(config.docassist_api_key, 'sk_new_key_0000')
        self.assertEqual(config.docassist_base_url, 'https://docassist.test/api/v1')
        # Testing persists the settings, so the key is not asked for again.
        self.assertEqual(ChatbotSettings.objects.get().docassist_api_key, 'sk_new_key_0000')
        self.assertNotContains(response, 'sk_new_key_0000')   # but is never rendered back

    @mock.patch('core.docassist._request')
    def test_test_without_key_prompts_for_one(self, req):
        response = self.client.post(URL, form_data(action='test'))
        self.assertContains(response, 'Enter an API key first.')
        req.assert_not_called()

    @mock.patch('core.docassist._request')
    def test_rejected_key_reported(self, req):
        req.side_effect = [(200, {'status': 'healthy'}), (401, {'success': False}), (401, {'success': False})]
        response = self.client.post(URL, form_data(action='test', docassist_api_key='sk_bad'))
        self.assertContains(response, 'rejected the API key')

    @mock.patch('core.docassist._request')
    def test_try_a_question(self, req):
        req.return_value = (200, {'success': True, 'answer': 'Disability is…', 'session_id': 's',
                                  'citations': [{'filename': 'policy.pdf', 'page': 3}]})
        response = self.client.post(URL, form_data(action='ask', docassist_api_key='sk_k',
                                                   test_question='What is disability?'))
        self.assertContains(response, 'Disability is…')
        self.assertContains(response, 'policy.pdf (p. 3)')

    # ── AJAX endpoint (no full-page reload) ──────────────────────────────────
    @mock.patch('core.docassist._request')
    def test_ajax_try_returns_html_fragment_and_saves_key(self, req):
        req.return_value = (200, {'success': True, 'answer': 'Disability is…',
                                  'citations': [{'filename': 'policy.pdf', 'page': 3}]})
        response = self.client.post(TRY_URL, form_data(action='ask', docassist_api_key='sk_ajax_123',
                                                       test_question='What is disability?'),
                                    HTTP_X_REQUESTED_WITH='XMLHttpRequest')
        self.assertEqual(response.status_code, 200)
        data = response.json()
        self.assertTrue(data['ok'])
        self.assertIn('Disability is', data['html'])
        self.assertIn('policy.pdf (p. 3)', data['html'])
        # Key persisted, so it isn't asked for again.
        self.assertEqual(ChatbotSettings.objects.get().docassist_api_key, 'sk_ajax_123')

    @mock.patch('core.docassist._request')
    def test_ajax_test_connection_returns_fragment(self, req):
        req.side_effect = [(200, {'status': 'healthy', 'version': '3.4.1'}),
                           (200, {'success': True, 'organization': 'Test Organization'}),
                           (200, {'success': True, 'documents': []})]
        data = self.client.post(TRY_URL, form_data(action='test', docassist_api_key='sk_k'),
                                HTTP_X_REQUESTED_WITH='XMLHttpRequest').json()
        self.assertTrue(data['ok'])
        self.assertIn('Connected. The API key is valid.', data['html'])

    def test_ajax_invalid_form_returns_400(self):
        resp = self.client.post(TRY_URL, form_data(action='test', docassist_timeout_seconds='999'),
                                HTTP_X_REQUESTED_WITH='XMLHttpRequest')
        self.assertEqual(resp.status_code, 400)
        self.assertFalse(resp.json()['ok'])

    def test_ajax_endpoint_requires_national_admin(self):
        self.client.force_login(self.county_officer)
        self.assertEqual(self.client.post(TRY_URL, form_data(action='test')).status_code, 403)
