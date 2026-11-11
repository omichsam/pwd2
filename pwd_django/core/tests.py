from django.test import TestCase, override_settings
from django.core.exceptions import ValidationError
from django.core.files.uploadedfile import SimpleUploadedFile
from django.core.management import call_command
from django.core.management.base import CommandError
from django.core.exceptions import PermissionDenied
from django.core.exceptions import ValidationError as DjangoValidationError
from django.core.cache import cache
from django.db import IntegrityError, transaction
from django.db.models.deletion import ProtectedError
from io import StringIO
from unittest.mock import patch
from django.urls import reverse
from django.utils import timezone
from django.test import RequestFactory

from accounts.models import CustomUser
from core.models import Assessment, AuditEvent, Certificate, County, Hospital, Notification, OfficerDesignation
from core.certificates import CertificateError, reissue_certificate, revoke_certificate
from core.test_utils import TEST_PASSWORD, TestDataMixin
from core.uploads import validate_assessment_document
from core.disability_models import HearingAssessment, PhysicalAssessment
from core.workflow import (
    COUNTY_APPROVE,
    COUNTY_REJECT,
    HEALTH_APPROVE,
    HEALTH_REJECT,
    MEDICAL_COMPLETE,
    WorkflowTransitionError,
    transition_assessment,
)
from core.audit import record_audit_event
from core.pagination import paginate_queryset


@override_settings(PASSWORD_HASHERS=["django.contrib.auth.hashers.MD5PasswordHasher"])
class ObjectAuthorizationTests(TestDataMixin, TestCase):
    @classmethod
    def setUpTestData(cls):
        super().setUpTestData()
        cls.other_county = County.objects.create(name="Other County")
        cls.other_hospital = Hospital.objects.create(
            name="Other Hospital",
            county=cls.other_county,
        )
        cls.other_pwd = CustomUser.objects.create_user(
            username="other_pwd",
            password=TEST_PASSWORD,
            role="pwd",
            id_number="TEST-ID-002",
            county=cls.other_county,
            is_approved=True,
        )
        cls.other_medical = CustomUser.objects.create_user(
            username="other_medical",
            password=TEST_PASSWORD,
            role="medical_officer",
            license_id="TEST-MEDICAL-OTHER",
            county=cls.county,
            hospital=cls.hospital,
            is_approved=True,
        )
        cls.other_health = CustomUser.objects.create_user(
            username="other_health",
            password=TEST_PASSWORD,
            role="health_officer",
            license_id="TEST-HEALTH-OTHER",
            county=cls.county,
            hospital=cls.hospital,
            is_approved=True,
        )
        cls.medical_review_pwd = CustomUser.objects.create_user(
            username='medical_review_pwd', password=TEST_PASSWORD, role='pwd',
            id_number='TEST-ID-MEDICAL-REVIEW', county=cls.county, is_approved=True,
        )
        cls.health_review_pwd = CustomUser.objects.create_user(
            username='health_review_pwd', password=TEST_PASSWORD, role='pwd',
            id_number='TEST-ID-HEALTH-REVIEW', county=cls.county, is_approved=True,
        )
        cls.local_pending = Assessment.objects.create(
            user=cls.users["pwd"],
            hospital=cls.hospital,
            disability_type="physical",
            status="pending",
            assessment_date=timezone.localdate(),
        )
        cls.remote_pending = Assessment.objects.create(
            user=cls.other_pwd,
            hospital=cls.other_hospital,
            disability_type="physical",
            status="pending",
            assessment_date=timezone.localdate(),
        )
        cls.other_medical_review = Assessment.objects.create(
            user=cls.medical_review_pwd,
            hospital=cls.hospital,
            disability_type="hearing",
            status="checked",
            medical_officer=cls.other_medical,
            assessment_date=timezone.localdate(),
        )
        cls.other_health_review = Assessment.objects.create(
            user=cls.health_review_pwd,
            hospital=cls.hospital,
            disability_type="visual",
            status="approved_by_health_officer",
            medical_officer=cls.other_medical,
            health_officer=cls.other_health,
            assessment_date=timezone.localdate(),
        )

    def test_authenticated_wrong_role_receives_forbidden(self):
        self.client.force_login(self.users["pwd"])

        response = self.client.get(reverse("medical:dashboard"))

        self.assertEqual(response.status_code, 403)

    def test_pwd_cannot_open_another_users_application(self):
        self.client.force_login(self.users["pwd"])

        response = self.client.get(
            reverse("pwd:view_application", args=[self.remote_pending.pk])
        )

        self.assertEqual(response.status_code, 404)

    def test_medical_officer_cannot_open_other_hospital_assessment(self):
        self.client.force_login(self.users["medical_officer"])

        response = self.client.get(
            reverse("medical:view_assessment", args=[self.remote_pending.pk])
        )

        self.assertEqual(response.status_code, 404)

    def test_medical_officer_cannot_open_another_officers_completed_review(self):
        self.client.force_login(self.users["medical_officer"])

        response = self.client.get(
            reverse("medical:view_assessment", args=[self.other_medical_review.pk])
        )

        self.assertEqual(response.status_code, 404)

    def test_medical_officer_can_open_a_different_type_form_to_reclassify(self):
        # Reclassification is allowed: opening a different type's form works for a
        # visible assessment. The type only changes when the officer submits it.
        self.client.force_login(self.users["medical_officer"])

        response = self.client.get(
            reverse(
                "medical:assessment_form",
                args=[self.local_pending.pk, "hearing"],
            )
        )

        self.assertEqual(response.status_code, 200)
        self.local_pending.refresh_from_db()
        self.assertEqual(self.local_pending.disability_type, "physical")  # unchanged on GET

    def test_medical_officer_cannot_reclassify_assessment_from_other_hospital(self):
        # The visibility boundary still holds: a different type's form for an
        # assessment the officer cannot see is a 404, not a reclassification.
        self.client.force_login(self.users["medical_officer"])
        response = self.client.get(
            reverse("medical:assessment_form", args=[self.remote_pending.pk, "hearing"]))
        self.assertEqual(response.status_code, 404)

    def test_designation_limits_medical_pending_queue(self):
        designation = OfficerDesignation.objects.create(
            county=self.county,
            name="Hearing Specialist",
            allowed_disability_types="hearing",
        )
        officer = self.users["medical_officer"]
        officer.designation = designation
        officer.save(update_fields=["designation"])
        self.client.force_login(officer)

        response = self.client.get(
            reverse("medical:view_assessment", args=[self.local_pending.pk])
        )

        self.assertEqual(response.status_code, 404)

    def test_health_officer_cannot_open_another_officers_completed_review(self):
        self.client.force_login(self.users["health_officer"])

        response = self.client.get(
            reverse("health:view_assessment", args=[self.other_health_review.pk])
        )

        self.assertEqual(response.status_code, 404)

    def test_county_officer_cannot_open_other_county_assessment(self):
        self.client.force_login(self.users["county_officer"])

        response = self.client.get(
            reverse("supervisor:view_assessment", args=[self.remote_pending.pk])
        )

        self.assertEqual(response.status_code, 404)


@override_settings(PASSWORD_HASHERS=["django.contrib.auth.hashers.MD5PasswordHasher"])
class StateChangingMethodTests(TestDataMixin, TestCase):
    def test_logout_rejects_get(self):
        self.client.force_login(self.users["pwd"])

        response = self.client.get(reverse("accounts:logout"))

        self.assertEqual(response.status_code, 405)
        self.assertIn("_auth_user_id", self.client.session)

    def test_hospital_admin_toggle_rejects_get_without_changing_officer(self):
        officer = self.users["medical_officer"]
        original_state = officer.is_approved
        self.client.force_login(self.users["hospital_admin"])

        response = self.client.get(
            reverse("hospital_admin:toggle_officer", args=[officer.pk])
        )

        self.assertEqual(response.status_code, 405)
        officer.refresh_from_db()
        self.assertEqual(officer.is_approved, original_state)

    def test_national_toggle_rejects_get_without_changing_officer(self):
        officer = self.users["county_officer"]
        original_state = officer.is_approved
        self.client.force_login(self.users["national_admin"])

        response = self.client.get(
            reverse("national_admin:toggle_officer", args=[officer.pk])
        )

        self.assertEqual(response.status_code, 405)
        officer.refresh_from_db()
        self.assertEqual(officer.is_approved, original_state)

    def test_notification_changes_require_post_and_owner(self):
        notification = Notification.objects.create(
            recipient=self.users["pwd"],
            title="Test notification",
            message="Permission boundary test",
        )
        self.client.force_login(self.users["pwd"])

        get_response = self.client.get(
            reverse("notification_read", args=[notification.pk])
        )
        self.assertEqual(get_response.status_code, 405)
        notification.refresh_from_db()
        self.assertFalse(notification.is_read)

        post_response = self.client.post(
            reverse("notification_read", args=[notification.pk])
        )
        self.assertEqual(post_response.status_code, 302)
        notification.refresh_from_db()
        self.assertTrue(notification.is_read)

        self.client.force_login(self.users["medical_officer"])
        owner_response = self.client.post(
            reverse("notification_read", args=[notification.pk])
        )
        self.assertEqual(owner_response.status_code, 404)


@override_settings(
    MAX_DOCUMENT_UPLOAD_SIZE=10,
    ALLOWED_DOCUMENT_EXTENSIONS=[".pdf", ".png", ".jpg", ".jpeg"],
    ALLOWED_DOCUMENT_CONTENT_TYPES=["application/pdf", "image/png", "image/jpeg"],
)
class DocumentUploadValidationTests(TestCase):
    def test_allowed_document_is_accepted(self):
        upload = SimpleUploadedFile(
            "assessment.pdf",
            b"pdf",
            content_type="application/pdf",
        )

        validate_assessment_document(upload)

    def test_disallowed_extension_is_rejected(self):
        upload = SimpleUploadedFile(
            "malware.exe",
            b"binary",
            content_type="application/octet-stream",
        )

        with self.assertRaises(ValidationError):
            validate_assessment_document(upload)

    def test_oversized_document_is_rejected(self):
        upload = SimpleUploadedFile(
            "assessment.pdf",
            b"more-than-ten-bytes",
            content_type="application/pdf",
        )

        with self.assertRaises(ValidationError):
            validate_assessment_document(upload)

    def test_disguised_content_type_is_rejected(self):
        upload = SimpleUploadedFile(
            "assessment.pdf",
            b"binary",
            content_type="application/x-msdownload",
        )

        with self.assertRaises(ValidationError):
            validate_assessment_document(upload)


@override_settings(PASSWORD_HASHERS=["django.contrib.auth.hashers.MD5PasswordHasher"])
class AssessmentWorkflowTests(TestDataMixin, TestCase):
    def create_assessment(self, status="pending", **kwargs):
        values = {
            "user": self.users["pwd"],
            "hospital": self.hospital,
            "disability_type": "physical",
            "status": status,
            "assessment_date": timezone.localdate(),
        }
        values.update(kwargs)
        return Assessment.objects.create(**values)

    def test_complete_approval_path(self):
        assessment = self.create_assessment()
        PhysicalAssessment.objects.create(assessment=assessment)

        assessment = transition_assessment(
            assessment.pk, MEDICAL_COMPLETE, self.users["medical_officer"]
        )
        self.assertEqual(assessment.status, "checked")
        self.assertEqual(assessment.medical_officer, self.users["medical_officer"])

        assessment = transition_assessment(
            assessment.pk, HEALTH_APPROVE, self.users["health_officer"]
        )
        self.assertEqual(assessment.status, "approved_by_health_officer")
        self.assertEqual(assessment.health_officer, self.users["health_officer"])

        assessment = transition_assessment(
            assessment.pk, COUNTY_APPROVE, self.users["county_officer"]
        )
        self.assertEqual(assessment.status, "approved_by_county_officer")
        self.assertEqual(assessment.county_officer, self.users["county_officer"])
        self.assertEqual(
            list(
                AuditEvent.objects.filter(assessment=assessment)
                .order_by("created_at")
                .values_list("action", flat=True)
            ),
            [
                "assessment.medical_complete",
                "assessment.health_approve",
                "certificate.issued",
                "assessment.county_approve",
            ],
        )

    def test_medical_completion_requires_saved_clinical_details(self):
        assessment = self.create_assessment()

        with self.assertRaisesMessage(
            WorkflowTransitionError, "Clinical assessment details"
        ):
            transition_assessment(
                assessment.pk, MEDICAL_COMPLETE, self.users["medical_officer"]
            )

        assessment.refresh_from_db()
        self.assertEqual(assessment.status, "pending")
        self.assertIsNone(assessment.medical_officer)

    def test_workflow_cannot_skip_health_review(self):
        assessment = self.create_assessment(
            status="checked",
            medical_officer=self.users["medical_officer"],
        )

        with self.assertRaises(WorkflowTransitionError):
            transition_assessment(
                assessment.pk, COUNTY_APPROVE, self.users["county_officer"]
            )

        assessment.refresh_from_db()
        self.assertEqual(assessment.status, "checked")

    def test_completed_transition_cannot_be_repeated(self):
        assessment = self.create_assessment(
            status="checked",
            medical_officer=self.users["medical_officer"],
        )
        transition_assessment(
            assessment.pk, HEALTH_APPROVE, self.users["health_officer"]
        )

        with self.assertRaises(WorkflowTransitionError):
            transition_assessment(
                assessment.pk, HEALTH_APPROVE, self.users["health_officer"]
            )

    def test_health_rejection_requires_reason(self):
        assessment = self.create_assessment(
            status="checked",
            medical_officer=self.users["medical_officer"],
        )

        with self.assertRaisesMessage(WorkflowTransitionError, "reason is required"):
            transition_assessment(
                assessment.pk, HEALTH_REJECT, self.users["health_officer"], "  "
            )

        assessment.refresh_from_db()
        self.assertEqual(assessment.status, "checked")

    def test_health_rejection_records_actor_and_reason(self):
        assessment = self.create_assessment(
            status="checked",
            medical_officer=self.users["medical_officer"],
        )

        assessment = transition_assessment(
            assessment.pk,
            HEALTH_REJECT,
            self.users["health_officer"],
            "Additional evidence is required.",
        )

        self.assertEqual(assessment.status, "rejected")
        self.assertEqual(assessment.health_officer, self.users["health_officer"])
        self.assertEqual(assessment.rejection_reason, "Additional evidence is required.")

    def test_county_rejection_requires_reason(self):
        assessment = self.create_assessment(
            status="approved_by_health_officer",
            medical_officer=self.users["medical_officer"],
            health_officer=self.users["health_officer"],
        )

        with self.assertRaises(WorkflowTransitionError):
            transition_assessment(
                assessment.pk, COUNTY_REJECT, self.users["county_officer"]
            )

    def test_wrong_role_cannot_perform_transition(self):
        assessment = self.create_assessment(
            status="checked",
            medical_officer=self.users["medical_officer"],
        )

        with self.assertRaises(PermissionDenied):
            transition_assessment(
                assessment.pk, HEALTH_APPROVE, self.users["county_officer"]
            )

    def test_officer_outside_assessment_scope_cannot_transition(self):
        other_county = County.objects.create(name="Workflow Other County")
        other_hospital = Hospital.objects.create(
            name="Workflow Other Hospital", county=other_county
        )
        other_health = CustomUser.objects.create_user(
            username="workflow_other_health",
            password=TEST_PASSWORD,
            role="health_officer",
            license_id="WORKFLOW-OTHER-HEALTH",
            county=other_county,
            hospital=other_hospital,
            is_approved=True,
        )
        assessment = self.create_assessment(
            status="checked",
            medical_officer=self.users["medical_officer"],
        )

        with self.assertRaises(PermissionDenied):
            transition_assessment(assessment.pk, HEALTH_APPROVE, other_health)


@override_settings(
    PASSWORD_HASHERS=["django.contrib.auth.hashers.MD5PasswordHasher"],
    CERTIFICATE_VERIFY_MAX_ATTEMPTS=2,
    CERTIFICATE_VERIFY_WINDOW_SECONDS=60,
)
class CertificateSecurityTests(TestDataMixin, TestCase):
    def setUp(self):
        cache.clear()
        self.assessment = Assessment.objects.create(
            user=self.users['pwd'], hospital=self.hospital,
            disability_type='physical', status='approved_by_health_officer',
            assessment_date=timezone.localdate(),
            medical_officer=self.users['medical_officer'],
            health_officer=self.users['health_officer'],
        )
        self.assessment = transition_assessment(
            self.assessment.pk, COUNTY_APPROVE, self.users['county_officer']
        )
        self.certificate = Certificate.objects.get(assessment=self.assessment)

    def tearDown(self):
        cache.clear()

    def test_county_approval_issues_an_opaque_stored_certificate(self):
        self.assertEqual(self.certificate.issued_by, self.users['county_officer'])
        self.assertEqual(self.certificate.verification_token.version, 4)
        self.assertTrue(self.certificate.certificate_number.startswith('PWD-'))
        self.assertNotIn(self.users['pwd'].id_number, self.certificate.certificate_number)
        self.assertTrue(
            AuditEvent.objects.filter(
                assessment=self.assessment, action='certificate.issued'
            ).exists()
        )

    def test_public_verification_discloses_no_holder_or_medical_data(self):
        response = self.client.get(
            reverse('verify_certificate', args=[self.certificate.verification_token])
        )
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, 'Certificate is Genuine')
        self.assertNotContains(response, self.users['pwd'].id_number)
        self.assertNotContains(response, self.assessment.get_disability_type_display())

    def test_revoked_certificate_is_invalid_and_audited(self):
        revoke_certificate(
            self.certificate.pk, self.users['county_officer'], 'Reported lost.'
        )
        response = self.client.get(
            reverse('verify_certificate', args=[self.certificate.verification_token])
        )
        self.assertContains(response, 'Certificate Not Verified')
        self.assertTrue(
            AuditEvent.objects.filter(action='certificate.revoked').exists()
        )

    def test_reissue_preserves_history_and_invalidates_old_token(self):
        replacement = reissue_certificate(
            self.certificate.pk, self.users['county_officer'], 'Damaged document.'
        )
        self.certificate.refresh_from_db()
        self.assertEqual(replacement.supersedes, self.certificate)
        self.assertNotEqual(replacement.verification_token, self.certificate.verification_token)
        self.assertIsNotNone(self.certificate.revoked_at)
        old_response = self.client.get(
            reverse('verify_certificate', args=[self.certificate.verification_token])
        )
        self.assertContains(old_response, 'Certificate Not Verified')
        new_response = self.client.get(
            reverse('verify_certificate', args=[replacement.verification_token])
        )
        self.assertContains(new_response, 'Certificate is Genuine')

    def test_public_verification_is_rate_limited(self):
        url = reverse('verify_certificate', args=[self.certificate.verification_token])
        self.client.get(url)
        self.client.get(url)
        response = self.client.get(url)
        self.assertEqual(response.status_code, 429)

    def test_revocation_requires_a_reason(self):
        with self.assertRaises(CertificateError):
            revoke_certificate(self.certificate.pk, self.users['county_officer'], '  ')


class DatabaseIntegrityTests(TestDataMixin, TestCase):
    def create_assessment(self):
        return Assessment.objects.create(
            user=self.users['pwd'], hospital=self.hospital,
            disability_type='hearing', assessment_date=timezone.localdate(),
        )

    def test_percentage_validator_rejects_out_of_range_value(self):
        detail = HearingAssessment(
            assessment=self.create_assessment(), binaural_percentage=101
        )
        with self.assertRaises(ValidationError):
            detail.full_clean()

    def test_database_constraint_rejects_out_of_range_value(self):
        with self.assertRaises(IntegrityError), transaction.atomic():
            HearingAssessment.objects.create(
                assessment=self.create_assessment(), monaural_percentage_left=-1
            )

    def test_applicant_deletion_cannot_destroy_assessment_history(self):
        self.create_assessment()
        with self.assertRaises(ProtectedError):
            self.users['pwd'].delete()

    def test_database_prevents_duplicate_active_assessments(self):
        self.create_assessment()
        with self.assertRaises(IntegrityError), transaction.atomic():
            self.create_assessment()


@override_settings(PASSWORD_HASHERS=["django.contrib.auth.hashers.MD5PasswordHasher"])
class ApplicantBookingUXTests(TestDataMixin, TestCase):
    def test_duplicate_booking_returns_helpful_error_without_new_record(self):
        Assessment.objects.create(
            user=self.users['pwd'], hospital=self.hospital,
            disability_type='physical', assessment_date=timezone.localdate(),
        )
        self.client.force_login(self.users['pwd'])
        response = self.client.post(reverse('pwd:application'), {
            'hospital': self.hospital.pk,
            'disability_type': 'hearing',
            'assessment_date': timezone.localdate().isoformat(),
            'notes': '',
        })
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, 'already have an active assessment')
        self.assertEqual(Assessment.objects.filter(user=self.users['pwd']).count(), 1)


class PaginationTests(TestCase):
    def test_pagination_is_bounded_and_handles_out_of_range_pages(self):
        County.objects.bulk_create([County(name=f'Page County {i}') for i in range(61)])
        request = RequestFactory().get('/counties/', {'page': '999'})
        page = paginate_queryset(request, County.objects.order_by('id'))
        self.assertEqual(page.number, 2)
        self.assertEqual(len(page.object_list), 11)

    def test_invalid_page_value_falls_back_safely(self):
        request = RequestFactory().get('/counties/', {'page': 'invalid'})
        page = paginate_queryset(request, County.objects.none())
        self.assertEqual(page.number, 1)


class WorkflowDataAuditTests(TestCase):
    def test_audit_passes_for_empty_database(self):
        output = StringIO()

        call_command("audit_workflow_data", "--fail-on-error", stdout=output)

        self.assertIn("No workflow inconsistencies found", output.getvalue())

    def test_audit_fails_for_inconsistent_record(self):
        county = County.objects.create(name="Audit County")
        hospital = Hospital.objects.create(name="Audit Hospital", county=county)
        user = CustomUser.objects.create_user(
            username="audit_pwd",
            role="pwd",
            id_number="AUDIT-PWD-1",
        )
        Assessment.objects.create(
            user=user,
            hospital=hospital,
            disability_type="physical",
            status="checked",
            assessment_date=timezone.localdate(),
        )

        with self.assertRaises(CommandError):
            call_command("audit_workflow_data", "--fail-on-error", stdout=StringIO())


@override_settings(PASSWORD_HASHERS=["django.contrib.auth.hashers.MD5PasswordHasher"])
class WorkflowViewIntegrationTests(TestDataMixin, TestCase):
    def create_assessment(self, status="pending", **kwargs):
        values = {
            "user": self.users["pwd"],
            "hospital": self.hospital,
            "disability_type": "physical",
            "status": status,
            "assessment_date": timezone.localdate(),
        }
        values.update(kwargs)
        return Assessment.objects.create(**values)

    @patch("medical.views.notify_and_email")
    def test_medical_form_uses_workflow_transition(self, notify_mock):
        assessment = self.create_assessment()
        self.client.force_login(self.users["medical_officer"])

        response = self.client.post(
            reverse("medical:assessment_form", args=[assessment.pk, "physical"]),
            {},
        )

        self.assertRedirects(response, reverse("medical:dashboard"))
        assessment.refresh_from_db()
        self.assertEqual(assessment.status, "checked")
        self.assertEqual(assessment.medical_officer, self.users["medical_officer"])
        self.assertTrue(PhysicalAssessment.objects.filter(assessment=assessment).exists())
        notify_mock.assert_called_once()

    @override_settings(DOC_CHECKER_ENABLED=True, DOC_CHECKER_API_KEY='sk_dc',
                       DOC_CHECKER_BASE_URL='https://checker.test')
    @patch("core.doc_checker.run_for_document")
    @patch("medical.views.notify_and_email")
    def test_uploaded_document_is_checked_on_submit(self, notify_mock, run_check):
        from core.models import DocumentCheck, Document

        cache.clear()
        self.addCleanup(cache.clear)

        def fake_check(document, config=None):
            return DocumentCheck.objects.create(
                document=document, status='done', action='request_rescan',
                level='high', quality_category='POOR', quality_advice='Too blurry — rescan.')
        run_check.side_effect = fake_check

        assessment = self.create_assessment()
        self.client.force_login(self.users["medical_officer"])
        upload = SimpleUploadedFile('scan.jpg', b'\xff\xd8\xff fake jpeg', content_type='image/jpeg')

        response = self.client.post(
            reverse("medical:assessment_form", args=[assessment.pk, "physical"]),
            {'document': upload}, follow=True)

        assessment.refresh_from_db()
        self.assertEqual(assessment.status, "checked")
        document = Document.objects.get(assessment=assessment)
        run_check.assert_called_once_with(document)
        self.assertTrue(document.quality_check.needs_attention)
        # The officer is warned about the rescan on the dashboard.
        messages = [m.message for m in response.context['messages']]
        self.assertTrue(any('rescan' in m.lower() for m in messages))

    @patch("medical.views.notify_and_email")
    def test_medical_officer_can_reclassify_disability_type(self, notify_mock):
        from core.disability_models import HearingAssessment, PhysicalAssessment

        assessment = self.create_assessment(disability_type="physical")
        self.client.force_login(self.users["medical_officer"])

        response = self.client.post(
            reverse("medical:assessment_form", args=[assessment.pk, "hearing"]),
            {}, follow=True)

        assessment.refresh_from_db()
        self.assertEqual(assessment.status, "checked")
        self.assertEqual(assessment.disability_type, "hearing")
        self.assertTrue(HearingAssessment.objects.filter(assessment=assessment).exists())
        self.assertFalse(PhysicalAssessment.objects.filter(assessment=assessment).exists())
        self.assertTrue(AuditEvent.objects.filter(
            action="assessment.reclassified", entity_id=str(assessment.pk)).exists())
        messages = [m.message for m in response.context['messages']]
        self.assertTrue(any('reclassified' in m.lower() for m in messages))

    def test_medical_forms_render_responsive_shell(self):
        assessment = self.create_assessment(disability_type="physical")
        self.client.force_login(self.users["medical_officer"])
        for dtype in ("speech", "physical", "visual", "hearing"):
            html = self.client.get(
                reverse("medical:assessment_form", args=[assessment.pk, dtype])
            ).content.decode()
            self.assertIn("flex flex-col lg:flex-row", html, dtype)   # panes stack on mobile
            self.assertIn("w-full lg:w-5", html, dtype)               # full-width nav on mobile
            self.assertIn("max-width: 1023px", html, dtype)           # mobile shell override

    def test_reclassify_rejects_unknown_type(self):
        assessment = self.create_assessment(disability_type="physical")
        self.client.force_login(self.users["medical_officer"])
        response = self.client.post(
            reverse("medical:assessment_form", args=[assessment.pk, "banana"]), {})
        self.assertRedirects(response, reverse("medical:assessment_list"))
        assessment.refresh_from_db()
        self.assertEqual(assessment.disability_type, "physical")

    @patch("core.notifications.notify_and_email")
    def test_health_approval_endpoint_uses_workflow_transition(self, notify_mock):
        assessment = self.create_assessment(
            status="checked",
            medical_officer=self.users["medical_officer"],
        )
        self.client.force_login(self.users["health_officer"])

        response = self.client.post(
            reverse("health:view_assessment", args=[assessment.pk]),
            {"action": "approve"},
        )

        self.assertRedirects(response, reverse("health:assessment_list"))
        assessment.refresh_from_db()
        self.assertEqual(assessment.status, "approved_by_health_officer")
        notify_mock.assert_called_once()

    def test_health_rejection_endpoint_does_not_accept_empty_reason(self):
        assessment = self.create_assessment(
            status="checked",
            medical_officer=self.users["medical_officer"],
        )
        self.client.force_login(self.users["health_officer"])

        response = self.client.post(
            reverse("health:view_assessment", args=[assessment.pk]),
            {"action": "reject", "reason": "  "},
        )

        self.assertRedirects(
            response, reverse("health:view_assessment", args=[assessment.pk])
        )
        assessment.refresh_from_db()
        self.assertEqual(assessment.status, "checked")
        self.assertIsNone(assessment.health_officer)

    def test_county_endpoint_cannot_skip_required_stages(self):
        assessment = self.create_assessment(status="pending")
        self.client.force_login(self.users["county_officer"])

        response = self.client.post(
            reverse("supervisor:view_assessment", args=[assessment.pk]),
            {"action": "approve"},
        )

        self.assertRedirects(
            response, reverse("supervisor:view_assessment", args=[assessment.pk])
        )
        assessment.refresh_from_db()
        self.assertEqual(assessment.status, "pending")
        self.assertIsNone(assessment.county_officer)


@override_settings(PASSWORD_HASHERS=["django.contrib.auth.hashers.MD5PasswordHasher"])
class AuditTrailTests(TestDataMixin, TestCase):
    def test_audit_event_captures_actor_snapshot_and_request_id(self):
        self.client.force_login(self.users["hospital_admin"])
        officer = self.users["medical_officer"]

        response = self.client.post(
            reverse("hospital_admin:toggle_officer", args=[officer.pk])
        )

        self.assertEqual(response.status_code, 302)
        event = AuditEvent.objects.get(action="user.activation_changed")
        self.assertEqual(event.actor, self.users["hospital_admin"])
        self.assertEqual(event.actor_role, "hospital_admin")
        self.assertEqual(event.entity_id, str(officer.pk))
        self.assertIsNotNone(event.request_id)
        self.assertEqual(response.headers["X-Request-ID"], str(event.request_id))

    def test_audit_event_instance_cannot_be_changed_or_deleted(self):
        event = record_audit_event(
            "test.append_only",
            actor=self.users["national_admin"],
            entity_type="test",
            entity_id="1",
        )

        event.action = "test.changed"
        with self.assertRaises(DjangoValidationError):
            event.save()
        with self.assertRaises(DjangoValidationError):
            event.delete()

        event.refresh_from_db()
        self.assertEqual(event.action, "test.append_only")

    def test_login_success_creates_audit_event_without_credentials(self):
        user = self.users["pwd"]

        response = self.client.post(
            reverse("accounts:login"),
            {
                "role": "pwd",
                "username": user.id_number,
                "password": TEST_PASSWORD,
            },
        )

        self.assertEqual(response.status_code, 302)
        event = AuditEvent.objects.get(action="authentication.login_succeeded")
        self.assertEqual(event.actor, user)
        self.assertNotIn("password", event.metadata)
        self.assertNotIn(user.id_number, str(event.metadata))

    def test_request_id_is_returned_on_public_response(self):
        response = self.client.get(reverse("accounts:login"))

        self.assertIn("X-Request-ID", response.headers)
