from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.db.models import Count, Avg, F, Q
from django.utils import timezone
from django.core.exceptions import ValidationError
from django.db import transaction
from accounts.services import password_change_response

from core.decorators import medical_required
from core.models import Assessment, Hospital, County
from core.notifications import notify_and_email
from core.permissions import assessments_visible_to
from core.uploads import validate_assessment_document
from core.workflow import MEDICAL_COMPLETE, WorkflowTransitionError, transition_assessment
from core.pagination import paginate_queryset
from core.disability_models import (
    HearingAssessment, PhysicalAssessment, VisualAssessment,
    MentalAssessment, MaxillofacialAssessment, ChronicAssessment,
    SpeechAssessment,
)
from .forms import (
    HearingForm, PhysicalForm, VisualForm, MentalForm,
    MaxillofacialForm, ChronicForm, SpeechForm,
)

DISABILITY_FORMS = {
    'hearing': (HearingAssessment, HearingForm),
    'physical': (PhysicalAssessment, PhysicalForm),
    'visual': (VisualAssessment, VisualForm),
    'mental': (MentalAssessment, MentalForm),
    'maxillofacial': (MaxillofacialAssessment, MaxillofacialForm),
    'chronic': (ChronicAssessment, ChronicForm),
    'speech': (SpeechAssessment, SpeechForm),
}


@medical_required
def dashboard(request):
    user = request.user
    hospital = user.hospital
    qs = Assessment.objects.filter(hospital=hospital) if hospital else Assessment.objects.none()

    disability_data = list(
        qs.values('disability_type').annotate(count=Count('id')).order_by('-count')
    )
    disability_labels = [d['disability_type'].title() for d in disability_data]
    disability_counts = [d['count'] for d in disability_data]

    stats = qs.aggregate(
        total=Count('id'), pending=Count('id', filter=Q(status='pending')),
        in_review=Count('id', filter=Q(status='checked')),
        approved=Count('id', filter=Q(status='approved_by_county_officer')),
    )
    pending_qs = qs.filter(status='pending')
    if user.designation and user.designation.get_allowed_types():
        pending_qs = pending_qs.filter(disability_type__in=user.designation.get_allowed_types())
    pending_list = pending_qs.select_related('user')[:10]

    ctx = {
        'stats': stats,
        'hospital': hospital,
        'pending_list': pending_list,
        'disability_labels': disability_labels,
        'disability_counts': disability_counts,
    }
    return render(request, 'medical/dashboard.html', ctx)


@medical_required
def assessment_list(request):
    base_qs = assessments_visible_to(request.user).select_related('user', 'health_officer', 'county_officer')
    pending = base_qs.filter(status='pending')
    user = request.user
    if user.designation and user.designation.get_allowed_types():
        pending = pending.filter(disability_type__in=user.designation.get_allowed_types())
    pending = pending.order_by('-created_at')
    processed = base_qs.filter(
        medical_officer=request.user
    ).exclude(status='pending').order_by('-updated_at')
    return render(request, 'medical/assessment_list.html', {
        'assessments': paginate_queryset(request, pending, 'pending_page'),
        'processed': paginate_queryset(request, processed, 'processed_page'),
    })


@medical_required
def view_assessment(request, pk):
    assessment = get_object_or_404(assessments_visible_to(request.user), pk=pk)
    detail = None
    if assessment.status != 'pending':
        detail_map = {
            'hearing': 'hearing_detail',
            'physical': 'physical_detail',
            'visual': 'visual_detail',
            'mental': 'mental_detail',
            'maxillofacial': 'maxillofacial_detail',
            'chronic': 'chronic_detail',
            'speech': 'speech_detail',
        }
        related = detail_map.get(assessment.disability_type)
        if related:
            detail = getattr(assessment, related, None)
    return render(request, 'medical/view_assessment.html', {
        'assessment': assessment,
        'detail': detail,
    })


@medical_required
def assessment_form(request, pk, disability_type):
    # The disability type comes from the URL — the officer may pick a type that
    # differs from what the citizen booked (reclassification by clinical findings),
    # so it is not part of the lookup.
    if disability_type not in DISABILITY_FORMS:
        messages.error(request, 'Invalid disability type.')
        return redirect('medical:assessment_list')

    assessment = get_object_or_404(
        assessments_visible_to(request.user),
        pk=pk,
        status='pending',
    )
    booked_type = assessment.disability_type
    reclassified = disability_type != booked_type

    ModelClass, FormClass = DISABILITY_FORMS[disability_type]
    existing = ModelClass.objects.filter(assessment=assessment).first()

    if request.method == 'POST':
        form = FormClass(request.POST, instance=existing)
        doc_file = request.FILES.get('document')
        if doc_file:
            try:
                validate_assessment_document(doc_file)
            except ValidationError as exc:
                form.add_error(None, exc)
        if form.is_valid():
            document = None
            try:
                with transaction.atomic():
                    if reclassified:
                        # Officer's findings differ from the booking: switch the
                        # assessment to the type actually being assessed.
                        assessment.disability_type = disability_type
                        assessment.save(update_fields=['disability_type'])
                        from core.audit import record_audit_event
                        record_audit_event(
                            'assessment.reclassified',
                            actor=request.user,
                            entity_type='assessment',
                            entity_id=assessment.pk,
                            assessment=assessment,
                            metadata={'from': booked_type, 'to': disability_type},
                        )
                    detail = form.save(commit=False)
                    detail.assessment = assessment
                    detail.save()
                    if doc_file:
                        from core.models import Document
                        document = Document.objects.create(
                            assessment=assessment,
                            file=doc_file,
                            document_type=f'{disability_type}_assessment',
                        )
                    assessment = transition_assessment(
                        assessment.pk,
                        MEDICAL_COMPLETE,
                        request.user,
                    )
            except WorkflowTransitionError as exc:
                form.add_error(None, str(exc))
            else:
                # Screen the uploaded document (advisory). Runs outside the
                # transaction; failures never block the completed review.
                if document is not None:
                    from core import doc_checker
                    check = doc_checker.run_for_document(document)
                    if check is not None and check.needs_attention:
                        if check.action == check.ACTION_RESCAN:
                            messages.warning(
                                request,
                                'Document check: the scan looks unclear. '
                                + (check.quality_advice or 'A clearer rescan is recommended.'))
                        else:
                            messages.warning(
                                request,
                                'Document check: this document is flagged for a closer look '
                                '(possible alteration). The review still proceeded.')
                notify_and_email(
                    assessment.user,
                    'Medical Review Complete',
                    f'Your {assessment.get_disability_type_display()} assessment has been reviewed by a medical officer and is now awaiting health officer approval.',
                    'Medical Review Complete',
                    'medical_reviewed.html',
                    {'assessment': assessment},
                    notif_type='info',
                    link=f'/pwd/application/{assessment.pk}/',
                )
                if reclassified:
                    messages.success(
                        request,
                        f'Assessment completed and reclassified from '
                        f'{dict(Assessment.DISABILITY_CHOICES).get(booked_type, booked_type)} '
                        f'to {assessment.get_disability_type_display()}.')
                else:
                    messages.success(request, 'Assessment completed successfully.')
                return redirect('medical:dashboard')
    else:
        form = FormClass(instance=existing)

    template = f'medical/forms/{disability_type}_form.html'
    from django.template.loader import get_template
    from django.template import TemplateDoesNotExist
    try:
        get_template(template)
    except TemplateDoesNotExist:
        template = 'medical/forms/generic_form.html'

    return render(request, template, {
        'form': form,
        'assessment': assessment,
        'disability_type': disability_type,
    })


@medical_required
def profile(request):
    total = Assessment.objects.filter(medical_officer=request.user).count()
    return render(request, 'medical/profile.html', {'user': request.user, 'total_assessments': total})


@medical_required
def change_password(request):
    return password_change_response(request, 'medical/change_password.html')
