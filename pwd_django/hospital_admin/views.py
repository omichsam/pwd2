from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.db.models import Count
from django.views.decorators.http import require_POST

from core.decorators import hospital_admin_required
from core.models import Assessment
from accounts.models import CustomUser
from core.audit import record_audit_event
from accounts.services import password_change_response
from core.pagination import paginate_queryset


@hospital_admin_required
def dashboard(request):
    hospital = request.user.hospital
    if not hospital:
        return render(request, 'hospital/dashboard.html', {'hospital': None})

    qs = Assessment.objects.filter(hospital=hospital)
    stats = {
        'total': qs.count(),
        'pending': qs.filter(status='pending').count(),
        'in_review': qs.filter(status__in=['checked', 'approved_by_health_officer']).count(),
        'certified': qs.filter(status='approved_by_county_officer').count(),
        'rejected': qs.filter(status='rejected').count(),
    }
    disability_data = list(
        qs.values('disability_type').annotate(count=Count('id')).order_by('-count')
    )
    medical_count = CustomUser.objects.filter(role='medical_officer', hospital=hospital).count()
    health_count = CustomUser.objects.filter(role='health_officer', hospital=hospital).count()
    recent = qs.select_related('user').order_by('-created_at')[:10]

    return render(request, 'hospital/dashboard.html', {
        'hospital': hospital,
        'stats': stats,
        'disability_data': disability_data,
        'medical_count': medical_count,
        'health_count': health_count,
        'recent': recent,
    })


@hospital_admin_required
def officer_list(request):
    hospital = request.user.hospital
    officers = CustomUser.objects.filter(
        role__in=['medical_officer', 'health_officer'],
        hospital=hospital,
    ).order_by('role', 'first_name')
    return render(request, 'hospital/officers.html', {
        'officers': paginate_queryset(request, officers),
        'hospital': hospital,
    })


@hospital_admin_required
@require_POST
def toggle_officer(request, pk):
    hospital = request.user.hospital
    officer = get_object_or_404(
        CustomUser, pk=pk,
        role__in=['medical_officer', 'health_officer'],
        hospital=hospital,
    )
    previous_state = officer.is_approved
    officer.is_approved = not officer.is_approved
    officer.save()
    record_audit_event(
        'user.activation_changed',
        actor=request.user,
        entity_type='user',
        entity_id=officer.pk,
        metadata={'old_active': previous_state, 'new_active': officer.is_approved, 'role': officer.role},
    )
    status = 'activated' if officer.is_approved else 'deactivated'
    messages.success(request, f'{officer.get_full_name()} {status}.')
    return redirect('hospital_admin:officer_list')


@hospital_admin_required
def assessment_summary(request):
    hospital = request.user.hospital
    qs = Assessment.objects.filter(hospital=hospital).select_related('user').order_by('-created_at')

    status_filter = request.GET.get('status', '')
    disability_filter = request.GET.get('disability', '')
    if status_filter:
        qs = qs.filter(status=status_filter)
    if disability_filter:
        qs = qs.filter(disability_type=disability_filter)

    return render(request, 'hospital/assessment_summary.html', {
        'assessments': paginate_queryset(request, qs),
        'hospital': hospital,
        'status_filter': status_filter,
        'disability_filter': disability_filter,
        'status_choices': Assessment.STATUS_CHOICES,
        'disability_choices': Assessment.DISABILITY_CHOICES,
    })


@hospital_admin_required
def profile(request):
    return render(request, 'hospital/profile.html', {'user': request.user})


@hospital_admin_required
def change_password(request):
    return password_change_response(request, 'hospital/change_password.html')
