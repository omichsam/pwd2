from unittest import mock

from django.core.cache import cache
from django.core.files.base import ContentFile
from django.test import TestCase, override_settings

from core import doc_checker
from core.doc_checker_config import get_config, load_config
from core.models import Assessment, County, DocumentCheck, Document, Hospital
from accounts.models import CustomUser
from core.test_utils import TEST_PASSWORD

CHECKER = dict(DOC_CHECKER_ENABLED=True, DOC_CHECKER_API_KEY='sk_dc',
               DOC_CHECKER_BASE_URL='https://checker.test')


def _result(action='continue', level='low', quality='GOOD', advice=None, change=0.1):
    return 200, {
        'check_id': 'chk_' + '0' * 20, 'checked_at': '2026-11-11T08:00:00Z',
        'api_version': '1.1', 'action': action, 'level': level,
        'message': 'Looks consistent.',
        'quality': {'category': quality, 'score': 90, 'advice': advice},
        'change': {'score': change, 'warning_level': 0.5},
        'file': {'sha256': 'a' * 64, 'type': 'image', 'pages': 1},
        'page_count': 1, 'worst_page': 1, 'cached': False,
        'model': {'sha256': 'b' * 64}, 'note': 'Screening aid only.',
    }


@override_settings(**CHECKER)
class DocCheckerConfigTests(TestCase):
    def setUp(self):
        cache.clear()
        self.addCleanup(cache.clear)

    def test_active_when_key_and_url_present(self):
        self.assertTrue(load_config().active)

    @override_settings(DOC_CHECKER_API_KEY='')
    def test_inactive_without_key(self):
        self.assertFalse(load_config().active)


@override_settings(**CHECKER)
class CheckBytesTests(TestCase):
    def setUp(self):
        cache.clear()
        self.addCleanup(cache.clear)

    @mock.patch('core.doc_checker._request')
    def test_returns_result_on_success(self, req):
        req.return_value = _result()
        data = doc_checker.check_bytes(b'\xff\xd8stuff', 'form.jpg')
        self.assertEqual(data['action'], 'continue')
        # sent as multipart to /v1/check
        method, path = req.call_args.args[0], req.call_args.args[1]
        self.assertEqual((method, path), ('POST', '/v1/check'))
        self.assertIn('multipart/form-data', req.call_args.kwargs['content_type'])

    @mock.patch('core.doc_checker._request')
    def test_rejects_non_result_payload(self, req):
        req.return_value = (400, {'error': {'code': 'empty_file', 'message': 'x'}})
        self.assertIsNone(doc_checker.check_bytes(b'x', 'f.jpg'))

    @override_settings(DOC_CHECKER_ENABLED=False)
    @mock.patch('core.doc_checker._request')
    def test_disabled_does_not_call(self, req):
        self.assertIsNone(doc_checker.check_bytes(b'x', 'f.jpg'))
        req.assert_not_called()


@override_settings(**CHECKER, MEDIA_ROOT='/tmp/pwd-test-media')
class RunForDocumentTests(TestCase):
    @classmethod
    def setUpTestData(cls):
        county = County.objects.create(name='Testland')
        hospital = Hospital.objects.create(name='Test Hospital', county=county)
        user = CustomUser.objects.create_user(username='pwd1', password=TEST_PASSWORD,
                                               role='pwd', id_number='ID-1', county=county)
        from datetime import date
        cls.assessment = Assessment.objects.create(
            user=user, hospital=hospital, disability_type='physical', status='pending',
            assessment_date=date(2026, 11, 11))

    def setUp(self):
        cache.clear()
        self.addCleanup(cache.clear)
        self.document = Document.objects.create(
            assessment=self.assessment,
            file=ContentFile(b'\xff\xd8\xff image bytes', name='scan.jpg'),
            document_type='physical_assessment')

    @mock.patch('core.doc_checker.check_bytes')
    def test_stores_result_row(self, cb):
        cb.return_value = _result(action='request_rescan', level='high',
                                   quality='POOR', advice='Too blurry — rescan.')[1]
        check = doc_checker.run_for_document(self.document)
        self.assertEqual(check.status, 'done')
        self.assertEqual(check.action, 'request_rescan')
        self.assertTrue(check.needs_attention)
        self.assertEqual(check.quality_advice, 'Too blurry — rescan.')
        self.assertEqual(self.document.quality_check, check)

    @mock.patch('core.doc_checker.check_bytes', return_value=None)
    def test_records_error_when_api_fails(self, cb):
        check = doc_checker.run_for_document(self.document)
        self.assertEqual(check.status, 'error')
        self.assertFalse(check.needs_attention)

    @mock.patch('core.doc_checker.check_bytes', side_effect=RuntimeError('boom'))
    def test_never_raises(self, cb):
        check = doc_checker.run_for_document(self.document)  # must not raise
        self.assertEqual(check.status, 'error')
        self.assertIn('boom', check.error)

    @override_settings(DOC_CHECKER_ENABLED=False)
    def test_returns_none_when_disabled(self):
        self.assertIsNone(doc_checker.run_for_document(self.document))
        self.assertFalse(DocumentCheck.objects.exists())
