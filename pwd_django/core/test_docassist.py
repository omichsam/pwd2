import json
from unittest import mock

from django.core.cache import cache
from django.test import TestCase, override_settings

from core import docassist

DOCASSIST = dict(DOCASSIST_ENABLED=True, DOCASSIST_API_KEY='sk_test',
                 DOCASSIST_BASE_URL='http://docassist.test/api/v1',
                 CHATBOT_RAG_ENABLED=False)


def _ok(answer='Disability is a long-term impairment.', session_id='sess-1', citations=None):
    return 200, {'success': True, 'answer': answer, 'session_id': session_id,
                 'sources': ['policy.pdf'],
                 'citations': citations if citations is not None
                 else [{'document_id': 2, 'filename': 'policy.pdf', 'page': 3}]}


@override_settings(**DOCASSIST)
class DocAssistChatbotTests(TestCase):
    def setUp(self):
        cache.clear()
        self.addCleanup(cache.clear)   # cached chatbot config must not outlive the test DB

    def post(self, message='', intent=''):
        return self.client.post('/chatbot/', json.dumps({'message': message, 'intent': intent}),
                                content_type='application/json')

    @mock.patch('core.docassist._request')
    def test_free_text_answered_with_citations_and_session_kept(self, req):
        req.side_effect = [_ok(), _ok(answer='Simpler: a lasting condition.')]
        data = self.post('What is disability according to the policy document?').json()
        self.assertEqual(data['intent'], 'docassist')
        self.assertIn('long-term impairment', data['text'])
        self.assertIn('📄 Source: policy.pdf (p. 3)', data['text'])
        self.assertEqual(self.client.session[docassist.SESSION_KEY], 'sess-1')

        self.post('Can you explain that in simpler terms?')
        method, path, payload = req.call_args.args[:3]
        self.assertEqual((method, path), ('POST', '/chat'))
        self.assertEqual(payload['session_id'], 'sess-1')

    @mock.patch('core.docassist._request')
    def test_new_chat_resets_session(self, req):
        req.return_value = _ok()
        self.post('What does the policy say about disability?')
        self.post()   # opening / clearing the chat sends an empty message
        self.assertNotIn(docassist.SESSION_KEY, self.client.session)

    @mock.patch('core.docassist._request')
    def test_personal_numbers_masked(self, req):
        req.return_value = _ok()
        self.post('Policy question about ID 12345678 please')
        self.assertNotIn('12345678', req.call_args.args[2]['question'])

    @mock.patch('core.docassist._request', return_value=(None, None))
    def test_failure_falls_back_to_rules(self, req):
        data = self.post('How do I register?').json()
        self.assertNotEqual(data['intent'], 'docassist')
        self.assertTrue(data['text'])

    @mock.patch('core.docassist._request')
    def test_unknown_session_retries_without_it(self, req):
        req.side_effect = [(404, {'success': False, 'error': 'not found'}), _ok(session_id='sess-2')]
        result = docassist.ask('q', session_id='old')
        self.assertEqual(result['session_id'], 'sess-2')
        self.assertNotIn('session_id', req.call_args.args[2])

    @override_settings(DOCASSIST_API_KEY='')
    @mock.patch('core.docassist._request')
    def test_disabled_without_key(self, req):
        self.post('What is disability according to the policy?')
        req.assert_not_called()


class FormatCitationsTests(TestCase):
    def test_dedupes_and_limits(self):
        cites = [{'filename': 'a.pdf', 'page': 1}, {'filename': 'a.pdf', 'page': 1},
                 {'filename': 'b.pdf'}, {'filename': 'c.pdf', 'page': 2},
                 {'filename': 'd.pdf', 'page': 9}, 'junk']
        self.assertEqual(docassist.format_citations(cites),
                         '📄 Sources: a.pdf (p. 1); b.pdf; c.pdf (p. 2)')
        self.assertEqual(docassist.format_citations(None), '')


class CleanAnswerTests(TestCase):
    def test_strips_inline_source_markers(self):
        out = docassist.clean_answer('Disability is X [guide.md, page 1]. It helps [a.pdf].')
        self.assertEqual(out, 'Disability is X. It helps.')

    def test_strips_page_only_marker_and_fixes_spacing(self):
        self.assertEqual(docassist.clean_answer('The fee is free [page 2].'), 'The fee is free.')

    def test_removes_trailing_filler(self):
        out = docassist.clean_answer('You register online. Please let me know if you need more help!')
        self.assertEqual(out, 'You register online.')

    def test_keeps_non_citation_brackets(self):
        self.assertEqual(docassist.clean_answer('See item [note] here.'), 'See item [note] here.')

    def test_trims_long_answers_to_whole_sentences(self):
        text = 'One here. Two longer now. Three keeps going.'
        self.assertEqual(docassist.clean_answer(text, max_chars=26), 'One here. Two longer now.')
        self.assertEqual(docassist.clean_answer(text, max_chars=5), 'One here.')  # always ≥1 sentence

    def test_empty_stays_empty(self):
        self.assertEqual(docassist.clean_answer(''), '')


@override_settings(**DOCASSIST)
class AnswerCleaningIntegrationTests(TestCase):
    def setUp(self):
        cache.clear()
        self.addCleanup(cache.clear)

    @mock.patch('core.docassist._request')
    def test_ask_returns_cleaned_answer(self, req):
        req.return_value = _ok(answer='Disability is long-term [guide.md, page 1]. '
                                      'I hope this helps!')
        result = docassist.ask('q')
        self.assertEqual(result['answer'], 'Disability is long-term.')
