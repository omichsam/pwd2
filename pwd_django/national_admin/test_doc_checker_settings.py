from unittest import mock

from django.core.cache import cache
from django.test import TestCase, override_settings
from django.urls import reverse

from accounts.models import CustomUser
from core.doc_checker_config import load_config
from core.models import AuditEvent, DocumentCheckerSettings
from core.test_utils import TEST_PASSWORD

URL = reverse('national_admin:doc_checker_settings')
TRY_URL = reverse('national_admin:doc_checker_settings_try')

ENV = dict(DOC_CHECKER_ENABLED=False, DOC_CHECKER_API_KEY='', DOC_CHECKER_BASE_URL='')


def form_data(**overrides):
    data = {'action': 'save', 'enabled': 'on', 'base_url': 'https://checker.test',
            'api_key': '', 'timeout_seconds': '45'}
    data.update(overrides)
    return {k: v for k, v in data.items() if v is not None}


@override_settings(**ENV)
class DocCheckerSettingsPageTests(TestCase):
    @classmethod
    def setUpTestData(cls):
        cls.admin = CustomUser.objects.create_user(
            username='nat', password=TEST_PASSWORD, role='national_admin', is_approved=True)
        cls.officer = CustomUser.objects.create_user(
            username='co', password=TEST_PASSWORD, role='county_officer', is_approved=True)

    def setUp(self):
        cache.clear()
        self.addCleanup(cache.clear)
        self.client.force_login(self.admin)

    def test_only_national_admin(self):
        self.client.force_login(self.officer)
        self.assertEqual(self.client.get(URL).status_code, 403)

    def test_save_persists_and_activates(self):
        resp = self.client.post(URL, form_data(api_key='sk_dc_123456'))
        self.assertRedirects(resp, URL)
        row = DocumentCheckerSettings.objects.get()
        self.assertEqual(row.api_key, 'sk_dc_123456')
        self.assertTrue(load_config().active)
        event = AuditEvent.objects.get(action='doc_checker.settings_updated')
        self.assertEqual(event.metadata['api_key'], 'changed')
        self.assertNotIn('sk_dc_123456', str(event.metadata))

    def test_key_never_rendered_and_blank_keeps_it(self):
        self.client.post(URL, form_data(api_key='sk_dc_123456'))
        page = self.client.get(URL)
        self.assertNotContains(page, 'sk_dc_123456')
        self.assertContains(page, 'sk_…3456')
        self.client.post(URL, form_data(timeout_seconds='30'))
        self.assertEqual(DocumentCheckerSettings.objects.get().api_key, 'sk_dc_123456')

    def test_invalid_timeout_rejected(self):
        resp = self.client.post(URL, form_data(timeout_seconds='999'))
        self.assertContains(resp, 'between 1 and 120')
        self.assertFalse(DocumentCheckerSettings.objects.exists())

    @mock.patch('core.doc_checker._request')
    def test_ajax_test_connection(self, req):
        req.side_effect = [(200, {'status': 'ok'}),
                           (200, {'sha256': 'b' * 64, 'warning_level': 0.4})]
        data = self.client.post(TRY_URL, form_data(action='test', api_key='sk_k'),
                                HTTP_X_REQUESTED_WITH='XMLHttpRequest').json()
        self.assertTrue(data['ok'])
        self.assertIn('Connected. The API key is valid.', data['html'])
        # persisted so the key isn't asked again
        self.assertEqual(DocumentCheckerSettings.objects.get().api_key, 'sk_k')

    @mock.patch('core.doc_checker._request')
    def test_ajax_rejected_key(self, req):
        req.side_effect = [(200, {'status': 'ok'}), (401, {'error': {'code': 'invalid_api_key'}})]
        data = self.client.post(TRY_URL, form_data(action='test', api_key='sk_bad'),
                                HTTP_X_REQUESTED_WITH='XMLHttpRequest').json()
        self.assertFalse(data['ok'])
        self.assertIn('rejected the API key', data['html'])

    def test_ajax_requires_admin(self):
        self.client.force_login(self.officer)
        self.assertEqual(self.client.post(TRY_URL, form_data(action='test')).status_code, 403)
