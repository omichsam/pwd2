from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.db.models import Count, Q
from django.views.decorators.http import require_POST
import json
from datetime import date, timedelta

from core.decorators import national_admin_required
from core.models import Assessment, Hospital, County
from accounts.models import CustomUser
from core.audit import record_audit_event
from core.pagination import paginate_queryset


def _monthly_trend(qs, months_back=6):
    """Return (labels, submitted_counts, certified_counts) for the last N months."""
    today = date.today()
    labels, submitted, certified = [], [], []
    for i in range(months_back - 1, -1, -1):
        first = (today.replace(day=1) - timedelta(days=i * 28)).replace(day=1)
        if i == 0:
            last = today + timedelta(days=1)
        else:
            last = (first.replace(day=28) + timedelta(days=4)).replace(day=1)
        labels.append(first.strftime('%b %Y'))
        submitted.append(qs.filter(created_at__date__gte=first, created_at__date__lt=last).count())
        certified.append(qs.filter(
            updated_at__date__gte=first, updated_at__date__lt=last,
            status='approved_by_county_officer'
        ).count())
    return labels, submitted, certified


@national_admin_required
def dashboard(request):
    county_id = request.GET.get('county')
    selected_county = None
    if county_id:
        try:
            selected_county = County.objects.get(pk=county_id)
        except County.DoesNotExist:
            county_id = None

    qs = Assessment.objects.filter(hospital__county=selected_county) if selected_county else Assessment.objects.all()

    total = qs.count()
    certified = qs.filter(status='approved_by_county_officer').count()
    rejected = qs.filter(status='rejected').count()
    pending = qs.filter(status='pending').count()
    in_progress = qs.filter(status__in=['checked', 'approved_by_health_officer']).count()

    def _co(role, **kw):
        return CustomUser.objects.filter(role=role, is_approved=True, **kw).count()

    if selected_county:
        med = _co('medical_officer', county=selected_county)
        hlt = _co('health_officer', county=selected_county)
        hosp_count = Hospital.objects.filter(county=selected_county).count()
    else:
        med = _co('medical_officer')
        hlt = _co('health_officer')
        hosp_count = Hospital.objects.count()

    stats = {
        'total': total,
        'certified': certified,
        'pending': pending,
        'in_progress': in_progress,
        'rejected': rejected,
        'approval_rate': round(certified / total * 100) if total else 0,
        'rejection_rate': round(rejected / total * 100) if total else 0,
        'total_pwds': CustomUser.objects.filter(role='pwd').count(),
        'total_hospitals': hosp_count,
        'total_counties': County.objects.count(),
        'medical_officers': med,
        'health_officers': hlt,
        'county_officers': _co('county_officer'),
    }

    # Disability breakdown
    dis_data = list(qs.values('disability_type').annotate(count=Count('id')).order_by('-count'))

    # Top 10 counties
    county_qs = County.objects.annotate(
        total=Count('hospitals__assessments'),
        certified=Count('hospitals__assessments', filter=Q(hospitals__assessments__status='approved_by_county_officer')),
        c_pending=Count('hospitals__assessments', filter=Q(hospitals__assessments__status='pending')),
        c_rejected=Count('hospitals__assessments', filter=Q(hospitals__assessments__status='rejected')),
        hospital_count=Count('hospitals', distinct=True),
    ).order_by('-total')[:10]

    for c in county_qs:
        c.approval_rate = round(c.certified / c.total * 100) if c.total else 0

    # Monthly trend
    m_labels, m_submitted, m_certified = _monthly_trend(qs)

    all_counties = County.objects.order_by('name')

    return render(request, 'national/dashboard.html', {
        'stats': stats,
        'dis_labels': json.dumps([d['disability_type'].title() for d in dis_data]),
        'dis_counts': json.dumps([d['count'] for d in dis_data]),
        'county_data': county_qs,
        'monthly_labels': json.dumps(m_labels),
        'monthly_counts': json.dumps(m_submitted),
        'monthly_cert_counts': json.dumps(m_certified),
        'all_counties': all_counties,
        'selected_county': selected_county,
    })


@national_admin_required
def county_detail(request, pk):
    county = get_object_or_404(County, pk=pk)
    qs = Assessment.objects.filter(hospital__county=county)

    total = qs.count()
    certified = qs.filter(status='approved_by_county_officer').count()
    rejected = qs.filter(status='rejected').count()

    stats = {
        'total': total,
        'certified': certified,
        'pending': qs.filter(status='pending').count(),
        'medical_review': qs.filter(status='checked').count(),
        'health_approved': qs.filter(status='approved_by_health_officer').count(),
        'in_progress': qs.filter(status__in=['checked', 'approved_by_health_officer']).count(),
        'rejected': rejected,
        'approval_rate': round(certified / total * 100) if total else 0,
        'rejection_rate': round(rejected / total * 100) if total else 0,
    }

    hospitals = list(Hospital.objects.filter(county=county).annotate(
        total=Count('assessments'),
        h_certified=Count('assessments', filter=Q(assessments__status='approved_by_county_officer')),
        h_pending=Count('assessments', filter=Q(assessments__status='pending')),
        h_in_prog=Count('assessments', filter=Q(assessments__status__in=['checked', 'approved_by_health_officer'])),
        h_rejected=Count('assessments', filter=Q(assessments__status='rejected')),
        med_count=Count('customuser', filter=Q(customuser__role='medical_officer', customuser__is_approved=True), distinct=True),
        hlt_count=Count('customuser', filter=Q(customuser__role='health_officer', customuser__is_approved=True), distinct=True),
    ).order_by('-total'))
    for h in hospitals:
        h.approval_rate = round(h.h_certified / h.total * 100) if h.total else 0

    officers = CustomUser.objects.filter(
        county=county,
        role__in=['medical_officer', 'health_officer', 'county_officer', 'hospital_admin']
    ).select_related('hospital').order_by('role', 'first_name')

    dis_data = list(qs.values('disability_type').annotate(count=Count('id')).order_by('-count'))
    m_labels, m_submitted, m_certified_counts = _monthly_trend(qs)

    return render(request, 'national/county_detail.html', {
        'county': county,
        'stats': stats,
        'hospitals': hospitals,
        'officers': officers,
        'dis_labels': json.dumps([d['disability_type'].title() for d in dis_data]),
        'dis_counts': json.dumps([d['count'] for d in dis_data]),
        'monthly_labels': json.dumps(m_labels),
        'monthly_counts': json.dumps(m_submitted),
        'monthly_cert_counts': json.dumps(m_certified_counts),
        'all_counties': County.objects.order_by('name'),
    })


@national_admin_required
def add_county(request):
    if request.method == 'POST':
        name = request.POST.get('name', '').strip()
        if not name:
            messages.error(request, 'County name is required.')
        elif County.objects.filter(name__iexact=name).exists():
            messages.error(request, f'County "{name}" already exists.')
        else:
            County.objects.create(name=name)
            messages.success(request, f'County "{name}" added successfully.')
            return redirect('national_admin:county_breakdown')
    return render(request, 'national/add_county.html')


@national_admin_required
def county_breakdown(request):
    counties = County.objects.annotate(
        total=Count('hospitals__assessments'),
        certified=Count('hospitals__assessments', filter=Q(hospitals__assessments__status='approved_by_county_officer')),
        pending=Count('hospitals__assessments', filter=Q(hospitals__assessments__status='pending')),
        rejected=Count('hospitals__assessments', filter=Q(hospitals__assessments__status='rejected')),
        in_progress=Count('hospitals__assessments', filter=Q(hospitals__assessments__status__in=['checked', 'approved_by_health_officer'])),
        hospital_count=Count('hospitals', distinct=True),
        officer_count=Count('customuser', filter=Q(
            customuser__role__in=['medical_officer', 'health_officer'],
            customuser__is_approved=True,
        ), distinct=True),
    ).order_by('-total')
    for c in counties:
        c.approval_rate = round(c.certified / c.total * 100) if c.total else 0
    return render(request, 'national/counties.html', {'counties': counties})


@national_admin_required
def hospital_list(request):
    county_id = request.GET.get('county')
    hospitals = Hospital.objects.select_related('county').annotate(
        total=Count('assessments'),
        certified=Count('assessments', filter=Q(assessments__status='approved_by_county_officer')),
        officer_count=Count('customuser', filter=Q(
            customuser__role__in=['medical_officer', 'health_officer'],
            customuser__is_approved=True,
        ), distinct=True),
    ).order_by('county__name', 'name')
    if county_id:
        hospitals = hospitals.filter(county_id=county_id)
    counties = County.objects.all().order_by('name')
    return render(request, 'national/hospitals.html', {
        'hospitals': paginate_queryset(request, hospitals),
        'counties': counties,
        'selected_county': int(county_id) if county_id else None,
    })


@national_admin_required
def officer_list(request):
    county_id = request.GET.get('county')
    role_filter = request.GET.get('role', '')
    officers = CustomUser.objects.filter(
        role__in=['medical_officer', 'health_officer', 'county_officer', 'hospital_admin']
    ).select_related('county', 'hospital').order_by('county__name', 'role', 'first_name')
    if county_id:
        officers = officers.filter(county_id=county_id)
    if role_filter:
        officers = officers.filter(role=role_filter)
    counties = County.objects.all().order_by('name')
    return render(request, 'national/officers.html', {
        'officers': paginate_queryset(request, officers),
        'counties': counties,
        'selected_county': int(county_id) if county_id else None,
        'role_filter': role_filter,
    })


@national_admin_required
def profile(request):
    return render(request, 'national/profile.html', {'user': request.user})


# ── County Officer Management ──────────────────────────────────────────────────

@national_admin_required
def county_officer_list(request):
    county_id = request.GET.get('county')
    officers = CustomUser.objects.filter(role='county_officer').select_related('county').order_by('county__name', 'first_name')
    if county_id:
        officers = officers.filter(county_id=county_id)
    counties = County.objects.order_by('name')
    return render(request, 'national/county_officers.html', {
        'officers': paginate_queryset(request, officers),
        'counties': counties,
        'selected_county': int(county_id) if county_id else None,
    })


@national_admin_required
def create_county_officer(request):
    counties = County.objects.order_by('name')
    if request.method == 'POST':
        first_name = request.POST.get('first_name', '').strip()
        last_name = request.POST.get('last_name', '').strip()
        license_id = request.POST.get('license_id', '').strip()
        email = request.POST.get('email', '').strip()
        mobile = request.POST.get('mobile_number', '').strip()
        county_id = request.POST.get('county_id', '').strip()
        password = request.POST.get('password', '')

        error = None
        if not all([first_name, last_name, license_id, email, county_id, password]):
            error = 'All required fields must be filled.'
        elif CustomUser.objects.filter(license_id=license_id).exists():
            error = f'License ID "{license_id}" is already in use.'
        elif CustomUser.objects.filter(email=email).exists():
            error = f'Email "{email}" is already registered.'

        if error:
            messages.error(request, error)
        else:
            county = get_object_or_404(County, pk=county_id)
            user = CustomUser(
                username=license_id,
                license_id=license_id,
                email=email,
                first_name=first_name,
                last_name=last_name,
                mobile_number=mobile,
                county=county,
                role='county_officer',
                is_approved=True,
            )
            user.set_password(password)
            user.save()
            record_audit_event(
                'user.county_officer_created',
                actor=request.user,
                entity_type='user',
                entity_id=user.pk,
                metadata={'role': user.role, 'county_id': county.pk},
            )
            messages.success(request, f'County officer {user.get_full_name()} created and activated for {county.name} County.')
            return redirect('national_admin:county_officer_list')

    return render(request, 'national/create_county_officer.html', {'counties': counties})


@national_admin_required
def edit_county_officer(request, pk):
    officer = get_object_or_404(CustomUser, pk=pk, role='county_officer')
    counties = County.objects.order_by('name')
    if request.method == 'POST':
        first_name = request.POST.get('first_name', '').strip()
        last_name = request.POST.get('last_name', '').strip()
        email = request.POST.get('email', '').strip()
        mobile = request.POST.get('mobile_number', '').strip()
        county_id = request.POST.get('county_id', '').strip()
        new_password = request.POST.get('password', '').strip()

        error = None
        if not all([first_name, last_name, email, county_id]):
            error = 'First name, last name, email, and county are required.'
        elif CustomUser.objects.filter(email=email).exclude(pk=pk).exists():
            error = f'Email "{email}" is already in use by another account.'

        if error:
            messages.error(request, error)
        else:
            previous_county_id = officer.county_id
            officer.first_name = first_name
            officer.last_name = last_name
            officer.email = email
            officer.mobile_number = mobile
            officer.county = get_object_or_404(County, pk=county_id)
            if new_password:
                officer.set_password(new_password)
                officer.username = officer.license_id
            officer.save()
            record_audit_event(
                'user.county_officer_updated',
                actor=request.user,
                entity_type='user',
                entity_id=officer.pk,
                metadata={
                    'role': officer.role,
                    'old_county_id': previous_county_id,
                    'new_county_id': officer.county_id,
                    'password_changed': bool(new_password),
                },
            )
            messages.success(request, f'{officer.get_full_name()} updated successfully.')
            return redirect('national_admin:county_officer_list')

    return render(request, 'national/edit_county_officer.html', {
        'officer': officer,
        'counties': counties,
    })


@national_admin_required
@require_POST
def toggle_officer(request, pk):
    officer = get_object_or_404(CustomUser, pk=pk, role='county_officer')
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
    action = 'activated' if officer.is_approved else 'deactivated'
    messages.success(request, f'{officer.get_full_name()} has been {action}.')
    return redirect('national_admin:county_officer_list')
