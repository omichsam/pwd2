from django.test import TestCase, override_settings
from django.urls import reverse
from django.core.cache import cache
from unittest.mock import patch
from django.core.exceptions import ValidationError

from accounts.models import CustomUser
from core.models import AuditEvent
from core.test_utils import TEST_PASSWORD, TestDataMixin


@override_settings(
    EMAIL_BACKEND="django.core.mail.backends.locmem.EmailBackend",
    PASSWORD_HASHERS=["django.contrib.auth.hashers.MD5PasswordHasher"],
)
class PublicAuthenticationTests(TestDataMixin, TestCase):
    def test_public_authentication_pages_render(self):
        for route_name in (
            "accounts:login",
            "accounts:register",
            "accounts:official_register",
            "accounts:forgot_password",
        ):
            with self.subTest(route=route_name):
                response = self.client.get(reverse(route_name))
                self.assertEqual(response.status_code, 200)

    def test_pwd_can_login_with_identity_number(self):
        with patch("accounts.views.send_email"):
            response = self.client.post(
                reverse("accounts:login"),
                {
                    "role": "pwd",
                    "username": self.users["pwd"].id_number,
                    "password": TEST_PASSWORD,
                },
            )

        self.assertRedirects(response, reverse("pwd:dashboard"))
        self.assertEqual(int(self.client.session["_auth_user_id"]), self.users["pwd"].pk)

    def test_each_approved_official_can_login_with_license(self):
        expected_dashboards = {
            "medical_officer": "medical:dashboard",
            "health_officer": "health:dashboard",
            "county_officer": "supervisor:dashboard",
            "hospital_admin": "hospital_admin:dashboard",
            "national_admin": "national_admin:dashboard",
        }

        for role, dashboard_name in expected_dashboards.items():
            with self.subTest(role=role):
                self.client.logout()
                user = self.users[role]
                with patch("accounts.views.send_email"):
                    response = self.client.post(
                        reverse("accounts:login"),
                        {
                            "role": role,
                            "username": user.license_id,
                            "password": TEST_PASSWORD,
                        },
                    )
                self.assertRedirects(response, reverse(dashboard_name))
                self.assertEqual(int(self.client.session["_auth_user_id"]), user.pk)

    def test_logout_clears_authenticated_session(self):
        self.client.force_login(self.users["pwd"])

        response = self.client.post(reverse("accounts:logout"))

        self.assertRedirects(response, reverse("accounts:login"))
        self.assertNotIn("_auth_user_id", self.client.session)

    def test_registration_rejects_common_password(self):
        response = self.client.post(reverse('accounts:register'), {
            'first_name': 'Accessible', 'last_name': 'Applicant',
            'email': 'accessible@example.test', 'id_number': 'ACCESS-ID-1',
            'mobile_number': '0712345678', 'password': '12345678',
            'confirm_password': '12345678', 'county': self.county.pk,
        })
        self.assertEqual(response.status_code, 200)
        self.assertFalse(CustomUser.objects.filter(id_number='ACCESS-ID-1').exists())
        self.assertContains(response, 'password is entirely numeric')


@override_settings(PASSWORD_HASHERS=["django.contrib.auth.hashers.MD5PasswordHasher"])
class RoleDashboardSmokeTests(TestDataMixin, TestCase):
    dashboard_by_role = {
        "pwd": "pwd:dashboard",
        "medical_officer": "medical:dashboard",
        "health_officer": "health:dashboard",
        "county_officer": "supervisor:dashboard",
        "hospital_admin": "hospital_admin:dashboard",
        "national_admin": "national_admin:dashboard",
    }

    def test_anonymous_users_are_redirected_to_login(self):
        for dashboard_name in self.dashboard_by_role.values():
            with self.subTest(dashboard=dashboard_name):
                response = self.client.get(reverse(dashboard_name))
                self.assertRedirects(response, reverse("accounts:login"))

    def test_each_role_can_render_its_dashboard(self):
        for role, dashboard_name in self.dashboard_by_role.items():
            with self.subTest(role=role):
                self.client.force_login(self.users[role])
                response = self.client.get(reverse(dashboard_name))
                self.assertEqual(response.status_code, 200)
                self.client.logout()

    def test_dashboard_has_keyboard_skip_target(self):
        self.client.force_login(self.users['pwd'])
        response = self.client.get(reverse('pwd:dashboard'))
        self.assertContains(response, 'href="#main-content"')
        self.assertContains(response, 'id="main-content"')


@override_settings(PASSWORD_HASHERS=["django.contrib.auth.hashers.MD5PasswordHasher"])
class SharedPasswordChangeTests(TestDataMixin, TestCase):
    routes = {
        'pwd': 'pwd:change_password',
        'medical_officer': 'medical:change_password',
        'health_officer': 'health:change_password',
        'county_officer': 'supervisor:change_password',
        'hospital_admin': 'hospital_admin:change_password',
    }

    def test_every_portal_rejects_passwords_failing_django_validators(self):
        for role, route in self.routes.items():
            with self.subTest(role=role):
                self.client.force_login(self.users[role])
                response = self.client.post(reverse(route), {
                    'current_password': TEST_PASSWORD,
                    'new_password': '123456',
                    'confirm_password': '123456',
                })
                self.assertEqual(response.status_code, 200)
                self.users[role].refresh_from_db()
                self.assertTrue(self.users[role].check_password(TEST_PASSWORD))
                self.client.logout()

    def test_successful_change_is_shared_and_audited(self):
        user = self.users['pwd']
        self.client.force_login(user)
        new_password = 'Violet-Canyon-84!Orbit'
        response = self.client.post(reverse('pwd:change_password'), {
            'current_password': TEST_PASSWORD,
            'new_password': new_password,
            'confirm_password': new_password,
        })
        self.assertRedirects(response, reverse('accounts:login'))
        user.refresh_from_db()
        self.assertTrue(user.check_password(new_password))
        self.assertTrue(AuditEvent.objects.filter(
            action='user.password_changed', actor=user
        ).exists())

    def test_identity_and_phone_validators_reject_malformed_values(self):
        user = CustomUser(
            username='invalid-identifiers', role='pwd',
            id_number='bad<script>', mobile_number='phone',
        )
        with self.assertRaises(ValidationError):
            user.full_clean()


@override_settings(
    EMAIL_BACKEND="django.core.mail.backends.locmem.EmailBackend",
    PASSWORD_HASHERS=["django.contrib.auth.hashers.MD5PasswordHasher"],
    LOGIN_MAX_ATTEMPTS=2,
    LOGIN_LOCKOUT_SECONDS=60,
)
class LoginRateLimitTests(TestDataMixin, TestCase):
    def setUp(self):
        cache.clear()

    def tearDown(self):
        cache.clear()

    def test_repeated_failed_login_is_temporarily_limited(self):
        credentials = {
            "role": "pwd",
            "username": self.users["pwd"].id_number,
            "password": "incorrect-password",
        }

        self.assertEqual(self.client.post(reverse("accounts:login"), credentials).status_code, 200)
        self.assertEqual(self.client.post(reverse("accounts:login"), credentials).status_code, 200)
        self.assertEqual(self.client.post(reverse("accounts:login"), credentials).status_code, 429)
