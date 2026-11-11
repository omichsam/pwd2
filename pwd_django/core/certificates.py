"""Secure certificate issuance, lifecycle, and rendering helpers."""

import base64
import io
import secrets

import qrcode
from django.core.exceptions import ObjectDoesNotExist, PermissionDenied
from django.db import transaction
from django.urls import reverse
from django.utils import timezone

from core.audit import record_audit_event
from core.models import Assessment, Certificate


class CertificateError(ValueError):
    pass


FORM_CODES = {
    'physical': ('MOH 276A', 'Physical Disability'),
    'visual': ('MOH 276B', 'Visual Impairment'),
    'hearing': ('MOH 276C', 'Hearing Impairment'),
    'mental': ('MOH 276D', 'Mental Health Disability'),
    'maxillofacial': ('MOH 276E', 'Maxillofacial Disability'),
    'chronic': ('MOH 276F', 'Chronic Condition Disability'),
    'speech': ('MOH 276G', 'Speech, Language & Communication Disability'),
}

DETAIL_RELATIONS = {
    'hearing': 'hearing_detail', 'physical': 'physical_detail',
    'visual': 'visual_detail', 'mental': 'mental_detail',
    'maxillofacial': 'maxillofacial_detail', 'chronic': 'chronic_detail',
    'speech': 'speech_detail',
}


def _validate_county_actor(assessment, actor):
    if not getattr(actor, 'is_authenticated', False) or actor.role != 'county_officer':
        raise PermissionDenied
    if (
        not actor.county_id or not assessment.hospital_id
        or actor.county_id != assessment.hospital.county_id
    ):
        raise PermissionDenied


def _new_number():
    return f'PWD-{timezone.now():%Y}-{secrets.token_hex(6).upper()}'


def get_active_certificate(assessment):
    return assessment.certificates.filter(revoked_at__isnull=True).first()


def issue_certificate(assessment, actor):
    """Issue once after a complete county approval, inside the caller's transaction."""
    if assessment.status != 'approved_by_county_officer':
        raise CertificateError('Only fully certified assessments can receive a certificate.')
    if not all((assessment.medical_officer_id, assessment.health_officer_id, assessment.county_officer_id)):
        raise CertificateError('The complete reviewer chain is required for certificate issuance.')
    _validate_county_actor(assessment, actor)
    existing = get_active_certificate(assessment)
    if existing:
        return existing

    certificate = Certificate.objects.create(
        assessment=assessment, certificate_number=_new_number(), issued_by=actor
    )

    record_audit_event(
        'certificate.issued', actor=actor, entity_type='certificate',
        entity_id=certificate.pk, assessment=assessment,
        metadata={'certificate_number': certificate.certificate_number},
    )
    return certificate


@transaction.atomic
def revoke_certificate(certificate_id, actor, reason):
    certificate = Certificate.objects.select_for_update().select_related(
        'assessment__hospital__county'
    ).get(pk=certificate_id)
    _validate_county_actor(certificate.assessment, actor)
    normalized_reason = reason.strip()
    if not normalized_reason:
        raise CertificateError('A revocation reason is required.')
    if certificate.revoked_at:
        raise CertificateError('This certificate has already been revoked.')
    certificate.revoked_at = timezone.now()
    certificate.revoked_by = actor
    certificate.revocation_reason = normalized_reason
    certificate.save(update_fields=['revoked_at', 'revoked_by', 'revocation_reason'])
    record_audit_event(
        'certificate.revoked', actor=actor, entity_type='certificate',
        entity_id=certificate.pk, assessment=certificate.assessment,
        reason=normalized_reason,
        metadata={'certificate_number': certificate.certificate_number},
    )
    return certificate


@transaction.atomic
def reissue_certificate(certificate_id, actor, reason):
    old = Certificate.objects.select_for_update().select_related(
        'assessment__hospital__county'
    ).get(pk=certificate_id)
    _validate_county_actor(old.assessment, actor)
    normalized_reason = reason.strip()
    if not normalized_reason:
        raise CertificateError('A reissue reason is required.')
    if old.revoked_at:
        raise CertificateError('Only an active certificate can be reissued.')
    old.revoked_at = timezone.now()
    old.revoked_by = actor
    old.revocation_reason = f'Reissued: {normalized_reason}'
    old.save(update_fields=['revoked_at', 'revoked_by', 'revocation_reason'])
    replacement = Certificate.objects.create(
        assessment=old.assessment, certificate_number=_new_number(),
        issued_by=actor, supersedes=old,
    )
    record_audit_event(
        'certificate.reissued', actor=actor, entity_type='certificate',
        entity_id=replacement.pk, assessment=old.assessment, reason=normalized_reason,
        metadata={
            'certificate_number': replacement.certificate_number,
            'superseded_certificate_number': old.certificate_number,
        },
    )
    return replacement


def build_certificate_context(request, assessment, certificate):
    form_code, form_title = FORM_CODES.get(
        assessment.disability_type, ('MOH 276', 'Disability')
    )
    detail = None
    relation = DETAIL_RELATIONS.get(assessment.disability_type)
    if relation:
        try:
            detail = getattr(assessment, relation)
        except ObjectDoesNotExist:
            pass

    ophthalmic_rows = []
    if assessment.disability_type == 'visual' and detail:
        ophthalmic_rows = [
            ('Present Eyeball', detail.present_eyeball_right, detail.present_eyeball_left),
            ('Squint', detail.squint_right, detail.squint_left),
            ('Nystagmus', detail.nystagmus_right, detail.nystagmus_left),
            ('Tearing', detail.tearing_right, detail.tearing_left),
            ('Lids', detail.lids_right, detail.lids_left),
            ('Conjunctiva', detail.conjunctiva_right, detail.conjunctiva_left),
            ('Cornea', detail.cornea_right, detail.cornea_left),
            ('Anterior Chamber', detail.anterior_chamber_right, detail.anterior_chamber_left),
            ('Iris', detail.iris_right, detail.iris_left),
            ('Pupil', detail.pupil_right, detail.pupil_left),
            ('Lens', detail.lens_right, detail.lens_left),
            ('Fundus', detail.fundus_right, detail.fundus_left),
        ]

    verify_url = request.build_absolute_uri(
        reverse('verify_certificate', kwargs={'token': certificate.verification_token})
    )
    qr = qrcode.QRCode(
        version=1, error_correction=qrcode.constants.ERROR_CORRECT_H, box_size=6, border=2
    )
    qr.add_data(verify_url)
    qr.make(fit=True)
    image = qr.make_image(fill_color='black', back_color='white')
    buffer = io.BytesIO()
    image.save(buffer, format='PNG')

    return {
        'assessment': assessment, 'certificate': certificate, 'detail': detail,
        'form_code': form_code, 'form_title': form_title,
        'cert_id': certificate.certificate_number, 'verify_url': verify_url,
        'ophthalmic_rows': ophthalmic_rows,
        'qr_code': 'data:image/png;base64,' + base64.b64encode(buffer.getvalue()).decode(),
    }
