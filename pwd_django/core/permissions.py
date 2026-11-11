"""Object-level query scopes shared by role-specific views."""

from django.db.models import Q

from core.models import Assessment


def assessments_visible_to(user):
    """Return only assessments the authenticated user is allowed to view."""
    if not user.is_authenticated:
        return Assessment.objects.none()

    if user.role == "pwd":
        return Assessment.objects.filter(user=user)

    if user.role == "medical_officer":
        if not user.hospital_id:
            return Assessment.objects.none()
        pending = Q(status="pending")
        if user.designation_id:
            allowed_types = user.designation.get_allowed_types()
            if allowed_types:
                pending &= Q(disability_type__in=allowed_types)
        return Assessment.objects.filter(hospital_id=user.hospital_id).filter(
            pending | Q(medical_officer=user)
        )

    if user.role == "health_officer":
        if not user.hospital_id:
            return Assessment.objects.none()
        return Assessment.objects.filter(hospital_id=user.hospital_id).filter(
            Q(status="checked") | Q(health_officer=user)
        )

    if user.role == "county_officer":
        if not user.county_id:
            return Assessment.objects.none()
        return Assessment.objects.filter(hospital__county_id=user.county_id)

    if user.role == "hospital_admin":
        if not user.hospital_id:
            return Assessment.objects.none()
        return Assessment.objects.filter(hospital_id=user.hospital_id)

    if user.role == "national_admin":
        return Assessment.objects.all()

    return Assessment.objects.none()
