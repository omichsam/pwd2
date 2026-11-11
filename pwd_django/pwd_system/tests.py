import os
from unittest.mock import patch

from django.core.exceptions import ImproperlyConfigured
from django.test import SimpleTestCase, TestCase
from django.urls import reverse

from pwd_system.env import env_bool, env_int, env_list


class EnvironmentParsingTests(SimpleTestCase):
    def test_boolean_values_are_parsed_case_insensitively(self):
        with patch.dict(os.environ, {"FEATURE_FLAG": "YeS"}):
            self.assertTrue(env_bool("FEATURE_FLAG"))
        with patch.dict(os.environ, {"FEATURE_FLAG": "OFF"}):
            self.assertFalse(env_bool("FEATURE_FLAG"))
        with patch.dict(os.environ, {"FEATURE_FLAG": "release"}):
            self.assertFalse(env_bool("FEATURE_FLAG"))

    def test_invalid_boolean_is_rejected(self):
        with patch.dict(os.environ, {"FEATURE_FLAG": "sometimes"}):
            with self.assertRaises(ImproperlyConfigured):
                env_bool("FEATURE_FLAG")

    def test_integer_minimum_is_enforced(self):
        with patch.dict(os.environ, {"UPLOAD_LIMIT": "0"}):
            with self.assertRaises(ImproperlyConfigured):
                env_int("UPLOAD_LIMIT", 10, minimum=1)

    def test_list_values_are_trimmed_and_empty_values_removed(self):
        with patch.dict(os.environ, {"HOSTS": "one.example, two.example, ,"}):
            self.assertEqual(env_list("HOSTS"), ["one.example", "two.example"])


class HealthEndpointTests(TestCase):
    def test_liveness_does_not_require_authentication(self):
        response = self.client.get(reverse('health_live'))
        self.assertEqual(response.status_code, 200)
        self.assertEqual(response.json(), {'status': 'ok'})
        self.assertIn('no-store', response['Cache-Control'])
        self.assertRegex(response['Server-Timing'], r'^app;dur=\d+\.\d$')

    def test_readiness_checks_database(self):
        response = self.client.get(reverse('health_ready'))
        self.assertEqual(response.status_code, 200)
        self.assertEqual(response.json(), {'status': 'ready'})

    @patch('core.health_views.connection.cursor', side_effect=RuntimeError('unavailable'))
    def test_readiness_returns_safe_failure(self, cursor_mock):
        response = self.client.get(reverse('health_ready'))
        self.assertEqual(response.status_code, 503)
        self.assertEqual(response.json(), {'status': 'unavailable'})
        self.assertNotContains(response, 'RuntimeError', status_code=503)
