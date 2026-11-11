"""Transactional assessment workflow rules.

All persisted assessment status changes must pass through this module.
"""

from django.core.exceptions import PermissionDenied
from django.db import transaction

from core.models import Assessment
from core.audit import record_audit_event


MEDICAL_COMPLETE = "medical_complete"
HEALTH_APPROVE = "health_approve"
HEALTH_REJECT = "health_reject"
COUNTY_APPROVE = "county_approve"
COUNTY_REJECT = "county_reject"


class WorkflowTransitionError(ValueError):
    """A requested transition violates the assessment workflow."""


TRANSITIONS = {
    MEDICAL_COMPLETE: {
        "source": "pending",
        "target": "checked",
        "role": "medical_officer",
        "actor_field": "medical_officer",
    },
    HEALTH_APPROVE: {
        "source": "checked",
        "target": "approved_by_health_officer",
        "role": "health_officer",
        "actor_field": "health_officer",
    },
    HEALTH_REJECT: {
        "source": "checked",
        "target": "rejected",
        "role": "health_officer",
        "actor_field": "health_officer",
        "reason_required": True,
    },
    COUNTY_APPROVE: {
        "source": "approved_by_health_officer",
        "target": "approved_by_county_officer",
        "role": "county_officer",
        "actor_field": "county_officer",
    },
    COUNTY_REJECT: {
        "source": "approved_by_health_officer",
        "target": "rejected",
        "role": "county_officer",
        "actor_field": "county_officer",
        "reason_required": True,
    },
}


DETAIL_RELATIONS = {
    "hearing": "hearing_detail",
    "physical": "physical_detail",
    "visual": "visual_detail",
    "mental": "mental_detail",
    "maxillofacial": "maxillofacial_detail",
    "chronic": "chronic_detail",
    "speech": "speech_detail",
}


def _validate_actor_scope(assessment, actor, required_role):
    if not actor.is_authenticated or actor.role != required_role:
        raise PermissionDenied

    if required_role in {"medical_officer", "health_officer"}:
        if not actor.hospital_id or actor.hospital_id != assessment.hospital_id:
            raise PermissionDenied

    if required_role == "medical_officer" and actor.designation_id:
        allowed_types = actor.designation.get_allowed_types()
        if allowed_types and assessment.disability_type not in allowed_types:
            raise PermissionDenied

    if required_role == "county_officer":
        if (
            not actor.county_id
            or not assessment.hospital_id
            or assessment.hospital.county_id != actor.county_id
        ):
            raise PermissionDenied


def _validate_required_history(assessment, action):
    if action == MEDICAL_COMPLETE:
        relation = DETAIL_RELATIONS.get(assessment.disability_type)
        if not relation or getattr(assessment, relation, None) is None:
            raise WorkflowTransitionError(
                "Clinical assessment details must be saved before medical completion."
            )
    elif action in {HEALTH_APPROVE, HEALTH_REJECT}:
        if not assessment.medical_officer_id:
            raise WorkflowTransitionError("A medical review is required before health review.")
    elif action in {COUNTY_APPROVE, COUNTY_REJECT}:
        if not assessment.medical_officer_id or not assessment.health_officer_id:
            raise WorkflowTransitionError(
                "Medical and health reviews are required before county review."
            )


@transaction.atomic
def transition_assessment(assessment_id, action, actor, reason=""):
    """Lock and perform one valid assessment transition."""
    rule = TRANSITIONS.get(action)
    if rule is None:
        raise WorkflowTransitionError("Unknown assessment workflow action.")

    assessment = (
        Assessment.objects.select_for_update()
        .select_related("hospital__county", "medical_officer", "health_officer")
        .get(pk=assessment_id)
    )
    _validate_actor_scope(assessment, actor, rule["role"])

    if assessment.status != rule["source"]:
        raise WorkflowTransitionError(
            f"This action requires status '{rule['source']}', not '{assessment.status}'."
        )

    normalized_reason = reason.strip()
    if rule.get("reason_required") and not normalized_reason:
        raise WorkflowTransitionError("A clear rejection reason is required.")

    _validate_required_history(assessment, action)

    assessment.status = rule["target"]
    setattr(assessment, rule["actor_field"], actor)
    assessment.rejection_reason = normalized_reason if rule.get("reason_required") else ""
    assessment.save(
        update_fields=[
            "status",
            rule["actor_field"],
            "rejection_reason",
            "updated_at",
        ]
    )
    if action == COUNTY_APPROVE:
        from core.certificates import issue_certificate
        issue_certificate(assessment, actor)
    record_audit_event(
        f'assessment.{action}',
        actor=actor,
        entity_type='assessment',
        entity_id=assessment.pk,
        assessment=assessment,
        old_status=rule['source'],
        new_status=rule['target'],
        reason=normalized_reason if rule.get('reason_required') else '',
        metadata={'disability_type': assessment.disability_type},
    )
    return assessment
