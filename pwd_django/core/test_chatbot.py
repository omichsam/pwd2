"""Tests for the PWD Access assistant (core.chatbot_views)."""

import json
from unittest import mock

from django.core.cache import cache
from django.test import TestCase, override_settings
from django.urls import reverse
from django.utils import timezone

from accounts.models import CustomUser
from core import chatbot_rag
from core.chatbot_views import match_intent
from core.models import Assessment, County, Hospital
from core.test_utils import TEST_PASSWORD, TestDataMixin


class ChatbotIntentTests(TestCase):
    """The understanding layer — no database access needed."""

    def assert_intent(self, message, expected):
        intent_id, score, suggestions = match_intent(message, '')
        self.assertEqual(
            intent_id, expected,
            f"{message!r} matched {intent_id!r} (score {score}, suggested {suggestions})",
        )

    def test_common_questions_route_to_the_right_intent(self):
        cases = {
            'how do i register': 'register',
            'what documents do i need': 'documents',
            'how much does it cost': 'cost',
            'is it free': 'cost',
            'how long will it take': 'timeline',
            'check my status': 'status',
            'how do i verify a certificate': 'verify',
            'i forgot my password': 'login',
            'can i reschedule': 'reschedule',
            'change my phone number': 'update_profile',
            'who are you': 'about',
            'i need bigger text': 'accessibility',
            'can my child be assessed': 'on_behalf',
            'who approves my application': 'process',
        }
        for message, expected in cases.items():
            with self.subTest(message=message):
                self.assert_intent(message, expected)

    def test_typos_still_match(self):
        for message, expected in (('regsiter', 'register'),
                                  ('certifikate', 'certificate'),
                                  ('hosptial', 'hospitals'),
                                  ('apointment', 'book')):
            with self.subTest(message=message):
                self.assert_intent(message, expected)

    def test_kiswahili_terms_are_understood(self):
        for message, expected in (('habari', 'greeting'),
                                  ('asante sana', 'thanks'),
                                  ('cheti changu', 'certificate'),
                                  ('msaada', 'menu')):
            with self.subTest(message=message):
                self.assert_intent(message, expected)

    def test_interrogative_alone_does_not_decide_an_intent(self):
        # "what" appears inside several keyword phrases; it must not win alone.
        self.assertIsNone(match_intent('what', '')[0])

    def test_unrelated_message_is_not_matched(self):
        for message in ('the weather is nice today', 'xyzzy', 'asdkjhasd'):
            with self.subTest(message=message):
                intent_id, _score, suggestions = match_intent(message, '')
                self.assertIsNone(intent_id)
                self.assertEqual(suggestions, [])

    def test_ambiguous_message_returns_suggestions_instead_of_guessing(self):
        intent_id, _score, suggestions = match_intent(
            'i want to register my son who is deaf', ''
        )
        self.assertIsNone(intent_id)
        self.assertTrue(suggestions)

    def test_officer_only_intents_are_hidden_from_citizens(self):
        self.assertIsNone(match_intent('what is my role', 'pwd')[0])
        self.assertEqual(match_intent('what is my role', 'medical_officer')[0], 'my_role')


@override_settings(CHATBOT_RAG_ENABLED=False)
class ChatbotResponseTests(TestDataMixin, TestCase):
    """The /chatbot/ endpoint, including live database answers (rule-based engine)."""

    def post(self, **payload):
        response = self.client.post(
            reverse('chatbot'), data=json.dumps(payload), content_type='application/json'
        )
        self.assertEqual(response.status_code, 200)
        return response.json()

    def test_opening_the_chat_returns_a_greeting_and_menu(self):
        data = self.post()
        self.assertEqual(data['intent'], 'greeting')
        self.assertTrue(data['quick_replies'])

    def test_quick_reply_intent_is_honoured(self):
        data = self.post(message='📝 Register', intent='register')
        self.assertEqual(data['intent'], 'register')
        self.assertIn('Register', data['text'])

    def test_unknown_intent_falls_back_to_matching_the_message(self):
        data = self.post(message='how much does it cost', intent='not_a_real_intent')
        self.assertEqual(data['intent'], 'cost')

    def test_fallback_offers_a_human(self):
        data = self.post(message='qwertyuiop asdfgh')
        self.assertEqual(data['intent'], '')
        self.assertIn('human', [r.get('intent') for r in data['quick_replies']])

    def test_anonymous_status_request_asks_for_login(self):
        data = self.post(intent='status')
        self.assertIn('logged in', data['text'])
        self.assertIn(reverse('accounts:login'),
                      [r.get('url') for r in data['quick_replies']])

    def test_status_reports_the_applicants_own_assessments(self):
        user = self.users['pwd']
        Assessment.objects.create(
            user=user, hospital=self.hospital, disability_type='hearing',
            status='approved_by_health_officer', assessment_date=timezone.localdate(),
        )
        self.client.force_login(user)
        data = self.post(intent='status')

        self.assertIn('Hearing', data['text'])
        self.assertIn('step 3 of 4', data['text'])
        self.assertIn(self.hospital.name, data['text'])

    def test_rejected_status_shows_the_reason(self):
        user = self.users['pwd']
        Assessment.objects.create(
            user=user, hospital=self.hospital, disability_type='physical',
            status='rejected', rejection_reason='Missing audiology report',
            assessment_date=timezone.localdate(),
        )
        self.client.force_login(user)
        self.assertIn('Missing audiology report', self.post(intent='status')['text'])

    def test_applicant_without_assessments_is_nudged_to_book(self):
        self.client.force_login(self.users['pwd'])
        self.assertIn("couldn't find any assessments", self.post(intent='status')['text'])

    def test_officer_queue_counts_only_their_own_scope(self):
        other_hospital = Hospital.objects.create(name='Elsewhere Hospital', county=self.county)
        # One active assessment per applicant, so use a second applicant.
        other_applicant = CustomUser.objects.create_user(
            username='pwd_two', role='pwd', id_number='TEST-ID-002', county=self.county,
            password=TEST_PASSWORD,
        )
        for user, hospital, disability in (
            (self.users['pwd'], self.hospital, 'physical'),
            (other_applicant, other_hospital, 'visual'),
        ):
            Assessment.objects.create(
                user=user, hospital=hospital, disability_type=disability,
                status='pending', assessment_date=timezone.localdate(),
            )
        self.client.force_login(self.users['medical_officer'])
        data = self.post(intent='my_queue')

        self.assertIn('*1* case', data['text'])
        self.assertIn(self.hospital.name, data['text'])

    def test_hospitals_answer_narrows_to_a_named_county(self):
        Hospital.objects.create(
            name='Coastal Clinic', county=County.objects.create(name='Kilifi')
        )
        data = self.post(message='hospitals in kilifi')
        self.assertIn('Kilifi', data['text'])
        self.assertIn('Coastal Clinic', data['text'])
        self.assertNotIn(self.hospital.name, data['text'])

    def test_role_answer_describes_the_signed_in_officer(self):
        self.client.force_login(self.users['health_officer'])
        data = self.post(intent='my_role')
        self.assertIn('Health Officer', data['text'])
        self.assertIn(self.hospital.name, data['text'])

    def test_citizens_cannot_reach_officer_intents(self):
        self.client.force_login(self.users['pwd'])
        self.assertNotEqual(self.post(intent='my_queue')['intent'], 'my_queue')

    def test_malformed_and_oversized_bodies_are_handled(self):
        for body in ('not json', json.dumps([1, 2, 3]), json.dumps({'message': 'x' * 9000})):
            with self.subTest(body=body[:20]):
                response = self.client.post(
                    reverse('chatbot'), data=body, content_type='application/json'
                )
                self.assertEqual(response.status_code, 200)

    def test_get_requests_are_rejected(self):
        self.assertEqual(self.client.get(reverse('chatbot')).status_code, 405)

    def test_feedback_accepts_a_rating_and_rejects_junk(self):
        good = self.client.post(
            reverse('chatbot_feedback'),
            data=json.dumps({'rating': 'down', 'intent': 'cost', 'question': 'how much'}),
            content_type='application/json',
        )
        self.assertEqual(good.status_code, 200)
        self.assertTrue(good.json()['ok'])

        bad = self.client.post(
            reverse('chatbot_feedback'),
            data=json.dumps({'rating': 'sideways'}),
            content_type='application/json',
        )
        self.assertEqual(bad.status_code, 400)


class ChatbotRetrievalTests(TestCase):
    """BM25 retrieval and history cleaning — no model calls."""

    def test_questions_retrieve_the_matching_knowledge(self):
        index = chatbot_rag.get_index()
        for question, expected in (('how much does the assessment cost', 'cost'),
                                   ('what documents should i bring', 'documents'),
                                   ('cheti changu kiko wapi', 'certificate')):
            with self.subTest(question=question):
                ids = [p.intent for p in index.search(question, '')]
                self.assertIn(expected, ids)

    def test_live_and_small_talk_intents_are_not_indexed(self):
        ids = {p.intent for p in chatbot_rag.get_index().passages}
        self.assertTrue(ids)
        self.assertFalse(ids & {'status', 'my_queue', 'my_role', 'hospitals', 'greeting', 'menu'})

    def test_role_restricted_passages_are_hidden_from_other_roles(self):
        index = chatbot_rag._Index([
            chatbot_rag.Passage('public', 'Fees', 'assessment fees are waived', None),
            chatbot_rag.Passage('secret', 'Officer guide', 'officer workload review steps',
                                frozenset({'medical_officer'})),
        ])
        self.assertEqual(index.search('workload review', 'pwd'), [])
        self.assertEqual([p.id for p in index.search('workload review', 'medical_officer')],
                         ['secret'])

    def test_irrelevant_questions_retrieve_nothing(self):
        self.assertEqual(chatbot_rag.get_index().search('xyzzy qwerty', ''), [])

    def test_personal_numbers_are_masked(self):
        self.assertEqual(
            chatbot_rag.mask_personal_numbers('my id is 12345678 and phone 0712 345 678'),
            'my id is [number] and phone [number]',
        )

    def test_history_drops_live_answers_and_starts_with_the_user(self):
        history = chatbot_rag.clean_history([
            {'from': 'bot', 'text': 'Hello!', 'intent': 'greeting'},
            {'from': 'user', 'text': 'check my status'},
            {'from': 'bot', 'text': 'Hearing — Status: pending', 'intent': 'status'},
            {'from': 'user', 'text': 'what about fees?'},
            {'from': 'bot', 'text': 'It is free.', 'intent': 'rag'},
            {'from': 'bot', 'text': 'Server down', 'error': True},
        ], live_intents={'status'})
        self.assertEqual(history, [
            {'role': 'user', 'content': 'check my status\n\nwhat about fees?'},
            {'role': 'assistant', 'content': 'It is free.'},
        ])

    def test_malformed_history_is_ignored(self):
        self.assertEqual(chatbot_rag.clean_history('nope', set()), [])
        self.assertEqual(chatbot_rag.clean_history([1, None, {'from': 'x', 'text': 'y'}], set()), [])


@override_settings(CHATBOT_RAG_ENABLED=True, CHATBOT_RAG_MAX_PER_WINDOW=3)
class ChatbotRagResponseTests(TestDataMixin, TestCase):
    """The /chatbot/ endpoint with RAG on and the Claude call mocked."""

    def setUp(self):
        cache.clear()
        patcher = mock.patch.object(chatbot_rag, '_call_llm')
        self.llm = patcher.start()
        self.addCleanup(patcher.stop)
        self.llm.return_value = {
            'answer': 'Assessment is *free* of charge.', 'sources': ['cost'], 'grounded': True,
        }

    def post(self, **payload):
        response = self.client.post(
            reverse('chatbot'), data=json.dumps(payload), content_type='application/json'
        )
        self.assertEqual(response.status_code, 200)
        return response.json()

    def test_knowledge_question_is_answered_by_rag_with_source_links(self):
        data = self.post(message='how much does the assessment cost?')
        self.assertEqual(data['intent'], 'rag')
        self.assertIn('free', data['text'])
        self.assertEqual(data['sources'], ['cost'])
        self.assertIn('cost', [r.get('intent') for r in data['quick_replies']])

    def test_retrieved_passages_are_sent_to_the_model(self):
        self.post(message='how much does the assessment cost?')
        _question, passages, _role, _history = self.llm.call_args.args
        self.assertIn('cost', [p.id for p in passages])

    def test_ungrounded_answer_offers_a_human(self):
        self.llm.return_value = {'answer': "I don't have that information.",
                                 'sources': [], 'grounded': False}
        data = self.post(message='what documents do i need for the assessment')
        self.assertIn('human', [r.get('intent') for r in data['quick_replies']])

    def test_personal_status_never_reaches_the_model(self):
        self.client.force_login(self.users['pwd'])
        data = self.post(message='check my status')
        self.assertEqual(data['intent'], 'status')
        self.llm.assert_not_called()

    def test_quick_reply_buttons_stay_rule_based(self):
        self.assertEqual(self.post(message='💰 Any Fees?', intent='cost')['intent'], 'cost')
        self.llm.assert_not_called()

    def test_id_numbers_are_masked_before_the_model_call(self):
        self.post(message='my id 34567890 — what documents do i need?')
        question = self.llm.call_args.args[0]
        self.assertNotIn('34567890', question)

    def test_model_failure_falls_back_to_rules(self):
        self.llm.return_value = None
        self.assertEqual(self.post(message='how much does it cost')['intent'], 'cost')

    def test_off_topic_question_skips_the_model(self):
        data = self.post(message='xyzzy qwerty')
        self.assertEqual(data['intent'], '')
        self.llm.assert_not_called()

    def test_rate_limit_falls_back_to_rules(self):
        for _ in range(3):
            self.assertEqual(self.post(message='how much does it cost')['intent'], 'rag')
        self.assertEqual(self.post(message='how much does it cost')['intent'], 'cost')
        self.assertEqual(self.llm.call_count, 3)
