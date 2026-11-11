from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.db.models import Count, Q
from django.core.exceptions import ObjectDoesNotExist
from accounts.services import password_change_response

from core.decorators import health_required
from core.models import Assessment
from core.permissions import assessments_visible_to
from core.workflow import (
    HEALTH_APPROVE,
    HEALTH_REJECT,
    WorkflowTransitionError,
    transition_assessment,
)
from core.pagination import paginate_queryset


@health_required
def dashboard(request):
    user = request.user
    hospital = user.hospital
    qs = Assessment.objects.filter(hospital=hospital) if hospital else Assessment.objects.none()

    stats = qs.aggregate(
        total=Count('id'), pending_review=Count('id', filter=Q(status='checked')),
        approved=Count('id', filter=Q(status__in=['approved_by_health_officer', 'approved_by_county_officer'])),
        rejected=Count('id', filter=Q(status='rejected')),
    )
    pending_list = qs.filter(status='checked').select_related('user', 'hospital')[:10]

    ctx = {'stats': stats, 'pending_list': pending_list, 'hospital': hospital}
    return render(request, 'health/dashboard.html', ctx)


@health_required
def assessment_list(request):
    base_qs = assessments_visible_to(request.user).select_related('user', 'hospital', 'medical_officer', 'health_officer')
    pending = base_qs.filter(status='checked').order_by('-updated_at')
    processed = base_qs.filter(
        status__in=['approved_by_health_officer', 'approved_by_county_officer', 'rejected']
    ).order_by('-updated_at')
    return render(request, 'health/assessment_list.html', {
        'assessments': paginate_queryset(request, pending, 'pending_page'),
        'processed': paginate_queryset(request, processed, 'processed_page'),
    })


@health_required
def view_assessment(request, pk):
    assessment = get_object_or_404(assessments_visible_to(request.user), pk=pk)

    if request.method == 'POST':
        action = request.POST.get('action')
        action_map = {'approve': HEALTH_APPROVE, 'reject': HEALTH_REJECT}
        try:
            assessment = transition_assessment(
                assessment.pk,
                action_map.get(action, action),
                request.user,
                request.POST.get('reason', ''),
            )
        except WorkflowTransitionError as exc:
            messages.error(request, str(exc))
            return redirect('health:view_assessment', pk=assessment.pk)

        from core.notifications import notify_and_email
        if action == 'approve':
            notify_and_email(
                assessment.user,
                'Health Officer Approved',
                f'Your {assessment.get_disability_type_display()} assessment has been approved by the health officer and is now pending final county certification.',
                'Health Officer Approved',
                'health_approved.html',
                {'assessment': assessment},
                notif_type='success',
                link=f'/pwd/application/{assessment.pk}/',
            )
            messages.success(request, 'Assessment approved.')
        else:
            notify_and_email(
                assessment.user,
                'Assessment Returned',
                f'Your {assessment.get_disability_type_display()} assessment was not approved at this stage. Please check your application for feedback.',
                'Assessment Update',
                'rejected.html',
                {'assessment': assessment},
                notif_type='error',
                link=f'/pwd/application/{assessment.pk}/',
            )
            messages.warning(request, 'Assessment rejected.')
        return redirect('health:assessment_list')

    ctx = {'assessment': assessment}

    if assessment.disability_type == 'visual':
        try:
            d = assessment.visual_detail
            ctx['ophthalmic_rows'] = [
                ('Present Eyeball', d.present_eyeball_right, d.present_eyeball_left),
                ('Squint',          d.squint_right,          d.squint_left),
                ('Nystagmus',       d.nystagmus_right,       d.nystagmus_left),
                ('Tearing',         d.tearing_right,         d.tearing_left),
                ('Lids',            d.lids_right,            d.lids_left),
                ('Conjunctiva',     d.conjunctiva_right,     d.conjunctiva_left),
                ('Cornea',          d.cornea_right,          d.cornea_left),
                ('Anterior Chamber',d.anterior_chamber_right,d.anterior_chamber_left),
                ('Iris',            d.iris_right,            d.iris_left),
                ('Pupil',           d.pupil_right,           d.pupil_left),
                ('Lens',            d.lens_right,            d.lens_left),
                ('Fundus',          d.fundus_right,          d.fundus_left),
            ]
        except ObjectDoesNotExist:
            ctx['ophthalmic_rows'] = []

    return render(request, 'health/view_assessment.html', ctx)


@health_required
def profile(request):
    total = Assessment.objects.filter(health_officer=request.user).count()
    return render(request, 'health/profile.html', {'user': request.user, 'total_reviews': total})


@health_required
def change_password(request):
    return password_change_response(request, 'health/change_password.html')
