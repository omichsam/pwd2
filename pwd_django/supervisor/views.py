from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.db.models import Count, Avg, Q
from django.utils import timezone
from django.views.decorators.http import require_POST
from django.core.exceptions import ObjectDoesNotExist
from core.workflow import (
    COUNTY_APPROVE,
    COUNTY_REJECT,
    WorkflowTransitionError,
    transition_assessment,
)
from core.audit import record_audit_event

from core.decorators import supervisor_required
from core.models import Assessment, Hospital, County, SubCounty, OfficerDesignation, Certificate
from core.certificates import (
    CertificateError, build_certificate_context, get_active_certificate,
    reissue_certificate, revoke_certificate,
)
from accounts.models import CustomUser
from .forms import HospitalForm
from accounts.services import password_change_response
from core.pagination import paginate_queryset


@supervisor_required
def dashboard(request):
    import json
    from django.utils import timezone as tz
    from datetime import timedelta, date

    user = request.user
    county = user.county
    qs = Assessment.objects.filter(hospital__county=county) if county else Assessment.objects.none()

    total = qs.count()
    certified = qs.filter(status='approved_by_county_officer').count()
    rejected = qs.filter(status='rejected').count()
    pending = qs.filter(status='pending').count()
    medical_review = qs.filter(status='checked').count()
    health_approved = qs.filter(status='approved_by_health_officer').count()

    stats = {
        'total': total,
        'pending': pending,
        'medical_review': medical_review,
        'health_approved': health_approved,
        'fully_approved': certified,
        'rejected': rejected,
        'approval_rate': round(certified / total * 100) if total else 0,
        'rejection_rate': round(rejected / total * 100) if total else 0,
        'in_progress': medical_review + health_approved,
    }

    # Hospital performance with full breakdown
    hospitals = Hospital.objects.filter(county=county).annotate(
        total=Count('assessments'),
        approved=Count('assessments', filter=Q(assessments__status='approved_by_county_officer')),
        h_pending=Count('assessments', filter=Q(assessments__status='pending')),
        h_rejected=Count('assessments', filter=Q(assessments__status='rejected')),
        h_in_progress=Count('assessments', filter=Q(assessments__status__in=['checked', 'approved_by_health_officer'])),
    ).order_by('-total')

    for h in hospitals:
        h.approval_rate = round(h.approved / h.total * 100) if h.total else 0

    hospital_count = hospitals.count()
    medical_officers = CustomUser.objects.filter(role='medical_officer', county=county, is_approved=True).count()
    health_officers = CustomUser.objects.filter(role='health_officer', county=county, is_approved=True).count()

    # Disability breakdown
    disability_data = list(qs.values('disability_type').annotate(count=Count('id')).order_by('-count'))
    disability_labels = json.dumps([d['disability_type'].title() for d in disability_data])
    disability_counts = json.dumps([d['count'] for d in disability_data])

    # Monthly trend — last 6 months
    months, month_counts, cert_counts = [], [], []
    today = date.today()
    for i in range(5, -1, -1):
        m = (today.replace(day=1) - timedelta(days=1) * (i * 28)).replace(day=1)
        if i > 0:
            end = (m.replace(day=28) + timedelta(days=4)).replace(day=1)
        else:
            end = today + timedelta(days=1)
        m_total = qs.filter(created_at__date__gte=m, created_at__date__lt=end).count()
        m_cert = qs.filter(updated_at__date__gte=m, updated_at__date__lt=end,
                           status='approved_by_county_officer').count()
        months.append(m.strftime('%b %Y'))
        month_counts.append(m_total)
        cert_counts.append(m_cert)

    # Pending approvals queue
    pending_approval = qs.filter(
        status='approved_by_health_officer'
    ).select_related('user', 'hospital').order_by('updated_at')[:10]

    # Recent certifications (last 5)
    recent_certified = qs.filter(
        status='approved_by_county_officer'
    ).select_related('user', 'hospital').order_by('-updated_at')[:5]

    ctx = {
        'stats': stats,
        'hospitals': hospitals,
        'medical_officers': medical_officers,
        'health_officers': health_officers,
        'hospital_count': hospital_count,
        'disability_labels': disability_labels,
        'disability_counts': disability_counts,
        'monthly_labels': json.dumps(months),
        'monthly_counts': json.dumps(month_counts),
        'monthly_cert_counts': json.dumps(cert_counts),
        'pending_approval': pending_approval,
        'recent_certified': recent_certified,
        'county': county,
    }
    return render(request, 'supervisor/dashboard.html', ctx)


@supervisor_required
def assessment_list(request):
    county = request.user.county
    assessments = Assessment.objects.filter(
        hospital__county=county, status='approved_by_health_officer'
    ).select_related('user', 'hospital').order_by('-updated_at')
    return render(request, 'supervisor/assessment_list.html', {
        'assessments': paginate_queryset(request, assessments),
    })


@supervisor_required
def view_assessment(request, pk):
    county = request.user.county
    assessment = get_object_or_404(Assessment, pk=pk, hospital__county=county)

    if request.method == 'POST':
        action = request.POST.get('action')
        action_map = {'approve': COUNTY_APPROVE, 'reject': COUNTY_REJECT}
        try:
            assessment = transition_assessment(
                assessment.pk,
                action_map.get(action, action),
                request.user,
                request.POST.get('reason', ''),
            )
        except WorkflowTransitionError as exc:
            messages.error(request, str(exc))
            return redirect('supervisor:view_assessment', pk=assessment.pk)

        from core.notifications import notify_and_email
        if action == 'approve':
            notify_and_email(
                assessment.user,
                'Certificate Ready!',
                f'Congratulations! Your PWD certificate for {assessment.get_disability_type_display()} disability has been issued. You can now download and print it.',
                'Your PWD Certificate is Ready',
                'certified.html',
                {'assessment': assessment},
                notif_type='success',
                link=f'/pwd/certificate/{assessment.pk}/print/',
            )
            messages.success(request, 'Assessment fully certified!')
        else:
            notify_and_email(
                assessment.user,
                'Application Not Certified',
                f'Your {assessment.get_disability_type_display()} assessment was not certified at this time. Please review the feedback on your application.',
                'Application Update',
                'rejected.html',
                {'assessment': assessment},
                notif_type='error',
                link=f'/pwd/application/{assessment.pk}/',
            )
            messages.warning(request, 'Assessment rejected.')
        return redirect('supervisor:assessment_list')

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
    return render(request, 'supervisor/view_assessment.html', ctx)


@supervisor_required
def hospital_list(request):
    county = request.user.county
    hospitals = Hospital.objects.filter(county=county).annotate(total=Count('assessments'))
    return render(request, 'supervisor/hospitals.html', {'hospitals': hospitals, 'county': county})


@supervisor_required
def add_hospital(request):
    county = request.user.county
    subcounties = SubCounty.objects.filter(county=county)
    if request.method == 'POST':
        form = HospitalForm(request.POST)
        if form.is_valid():
            hospital = form.save(commit=False)
            hospital.county = county
            hospital.save()
            messages.success(request, f'Hospital "{hospital.name}" added successfully.')
            return redirect('supervisor:hospital_list')
    else:
        form = HospitalForm()
    return render(request, 'supervisor/add_hospital.html', {
        'county': county,
        'subcounties': subcounties,
        'form': form,
    })


@supervisor_required
def officer_list(request):
    county = request.user.county
    officers = CustomUser.objects.filter(
        role__in=['medical_officer', 'health_officer'],
        county=county
    ).select_related('hospital')
    return render(request, 'supervisor/officers.html', {
        'officers': paginate_queryset(request, officers),
    })


@supervisor_required
@require_POST
def approve_officer(request, pk):
    officer = get_object_or_404(
        CustomUser, pk=pk,
        role__in=['medical_officer', 'health_officer'],
        county=request.user.county
    )
    action = request.POST.get('action')
    previous_state = officer.is_approved
    officer.is_approved = (action == 'activate')
    officer.save()
    record_audit_event(
        'user.activation_changed',
        actor=request.user,
        entity_type='user',
        entity_id=officer.pk,
        metadata={'old_active': previous_state, 'new_active': officer.is_approved, 'role': officer.role},
    )
    status = 'activated' if officer.is_approved else 'deactivated'
    messages.success(request, f'Officer {officer.get_full_name()} {status}.')
    return redirect('supervisor:officer_list')


@supervisor_required
def geography(request):
    county = request.user.county
    subcounties = SubCounty.objects.filter(county=county)
    return render(request, 'supervisor/geography.html', {
        'county': county,
        'subcounties': subcounties,
    })


@supervisor_required
@require_POST
def add_subcounty(request):
    name = request.POST.get('name', '').strip()
    county = request.user.county
    if name and county:
        SubCounty.objects.get_or_create(name=name, county=county)
        messages.success(request, f'Sub-county "{name}" added.')
    return redirect('supervisor:geography')


@supervisor_required
def certificate_print(request, pk):
    county = request.user.county
    assessment = get_object_or_404(
        Assessment, pk=pk, hospital__county=county, status='approved_by_county_officer'
    )
    certificate = get_active_certificate(assessment)
    if not certificate:
        messages.error(request, 'No active certificate is available for this assessment.')
        return redirect('supervisor:certified_list')
    context = build_certificate_context(request, assessment, certificate)
    context['can_manage_certificate'] = True
    return render(request, 'pwd/certificate_print.html', context)


@supervisor_required
@require_POST
def certificate_revoke(request, pk):
    certificate = get_object_or_404(
        Certificate, pk=pk, assessment__hospital__county=request.user.county
    )
    try:
        revoke_certificate(certificate.pk, request.user, request.POST.get('reason', ''))
    except CertificateError as exc:
        messages.error(request, str(exc))
    else:
        messages.success(request, 'Certificate revoked. Its verification token is no longer valid.')
    return redirect('supervisor:certified_list')


@supervisor_required
@require_POST
def certificate_reissue(request, pk):
    certificate = get_object_or_404(
        Certificate, pk=pk, assessment__hospital__county=request.user.county
    )
    try:
        replacement = reissue_certificate(
            certificate.pk, request.user, request.POST.get('reason', '')
        )
    except CertificateError as exc:
        messages.error(request, str(exc))
        return redirect('supervisor:certified_list')
    messages.success(request, 'Certificate reissued with a new verification token.')
    return redirect('supervisor:certificate_print', pk=replacement.assessment_id)


@supervisor_required
def certified_list(request):
    county = request.user.county
    hospital_filter = request.GET.get('hospital')

    base_qs = Assessment.objects.filter(
        hospital__county=county, status='approved_by_county_officer'
    ).select_related('user', 'hospital', 'medical_officer', 'health_officer', 'county_officer').prefetch_related('certificates')

    if hospital_filter:
        base_qs = base_qs.filter(hospital_id=hospital_filter)

    hospitals = Hospital.objects.filter(county=county).annotate(
        total_certified=Count('assessments', filter=Q(assessments__status='approved_by_county_officer')),
        cnt_physical=Count('assessments', filter=Q(assessments__status='approved_by_county_officer', assessments__disability_type='physical')),
        cnt_hearing=Count('assessments', filter=Q(assessments__status='approved_by_county_officer', assessments__disability_type='hearing')),
        cnt_visual=Count('assessments', filter=Q(assessments__status='approved_by_county_officer', assessments__disability_type='visual')),
        cnt_mental=Count('assessments', filter=Q(assessments__status='approved_by_county_officer', assessments__disability_type='mental')),
        cnt_maxillofacial=Count('assessments', filter=Q(assessments__status='approved_by_county_officer', assessments__disability_type='maxillofacial')),
        cnt_chronic=Count('assessments', filter=Q(assessments__status='approved_by_county_officer', assessments__disability_type='chronic')),
    ).order_by('-total_certified')

    return render(request, 'supervisor/certified_list.html', {
        'assessments': paginate_queryset(request, base_qs.order_by('-updated_at')),
        'hospitals': hospitals,
        'county': county,
        'selected_hospital': int(hospital_filter) if hospital_filter else None,
    })


@supervisor_required
def hospital_admin_list(request):
    county = request.user.county
    admins = CustomUser.objects.filter(
        role='hospital_admin', county=county
    ).select_related('hospital').order_by('first_name')
    hospitals = Hospital.objects.filter(county=county).order_by('name')
    return render(request, 'supervisor/hospital_admins.html', {
        'admins': paginate_queryset(request, admins),
        'hospitals': hospitals,
        'county': county,
    })


@supervisor_required
def create_hospital_admin(request):
    county = request.user.county
    hospitals = Hospital.objects.filter(county=county).order_by('name')
    if request.method == 'POST':
        first_name = request.POST.get('first_name', '').strip()
        last_name = request.POST.get('last_name', '').strip()
        license_id = request.POST.get('license_id', '').strip()
        email = request.POST.get('email', '').strip()
        mobile = request.POST.get('mobile_number', '').strip()
        hospital_id = request.POST.get('hospital_id')
        password = request.POST.get('password', '').strip()

        if not all([first_name, last_name, license_id, email, hospital_id, password]):
            messages.error(request, 'All required fields must be filled.')
        elif CustomUser.objects.filter(license_id=license_id).exists():
            messages.error(request, 'License ID already registered.')
        elif CustomUser.objects.filter(email=email).exists():
            messages.error(request, 'An account with this email already exists.')
        else:
            hospital = get_object_or_404(Hospital, pk=hospital_id, county=county)
            user = CustomUser(
                username=license_id,
                first_name=first_name,
                last_name=last_name,
                license_id=license_id,
                email=email,
                mobile_number=mobile,
                role='hospital_admin',
                hospital=hospital,
                county=county,
                is_approved=True,
            )
            user.set_password(password)
            user.save()
            record_audit_event(
                'user.hospital_admin_created',
                actor=request.user,
                entity_type='user',
                entity_id=user.pk,
                metadata={'role': user.role, 'hospital_id': hospital.pk, 'county_id': county.pk},
            )
            messages.success(request, f'Hospital admin account created for {user.get_full_name()}.')
            return redirect('supervisor:hospital_admin_list')

    return render(request, 'supervisor/create_hospital_admin.html', {
        'hospitals': hospitals,
        'county': county,
    })


@supervisor_required
@require_POST
def toggle_hospital_admin(request, pk):
    county = request.user.county
    admin = get_object_or_404(CustomUser, pk=pk, role='hospital_admin', county=county)
    previous_state = admin.is_approved
    admin.is_approved = not admin.is_approved
    admin.save()
    record_audit_event(
        'user.activation_changed',
        actor=request.user,
        entity_type='user',
        entity_id=admin.pk,
        metadata={'old_active': previous_state, 'new_active': admin.is_approved, 'role': admin.role},
    )
    status = 'activated' if admin.is_approved else 'deactivated'
    messages.success(request, f'{admin.get_full_name()} {status}.')
    return redirect('supervisor:hospital_admin_list')


@supervisor_required
def reassign_officer(request, pk):
    county = request.user.county
    officer = get_object_or_404(
        CustomUser, pk=pk,
        role__in=['medical_officer', 'health_officer'],
        county=county
    )
    hospitals = Hospital.objects.filter(county=county).order_by('name')
    if request.method == 'POST':
        hospital_id = request.POST.get('hospital_id')
        if hospital_id:
            previous_hospital_id = officer.hospital_id
            hospital = get_object_or_404(Hospital, pk=hospital_id, county=county)
            officer.hospital = hospital
            officer.save()
            record_audit_event(
                'user.hospital_reassigned',
                actor=request.user,
                entity_type='user',
                entity_id=officer.pk,
                metadata={
                    'role': officer.role,
                    'old_hospital_id': previous_hospital_id,
                    'new_hospital_id': hospital.pk,
                },
            )
            messages.success(request, f'{officer.get_full_name()} reassigned to {hospital.name}.')
        return redirect('supervisor:officer_list')
    return render(request, 'supervisor/reassign_officer.html', {
        'officer': officer,
        'hospitals': hospitals,
    })


@supervisor_required
def designation_list(request):
    county = request.user.county
    designations = OfficerDesignation.objects.filter(county=county).prefetch_related('officers')
    return render(request, 'supervisor/designations.html', {
        'designations': designations,
        'county': county,
    })


@supervisor_required
def create_designation(request):
    county = request.user.county
    disability_choices = Assessment.DISABILITY_CHOICES
    if request.method == 'POST':
        name = request.POST.get('name', '').strip()
        description = request.POST.get('description', '').strip()
        selected_types = request.POST.getlist('disability_types')
        if not name:
            messages.error(request, 'Designation name is required.')
        else:
            allowed = ','.join(selected_types)
            OfficerDesignation.objects.create(
                county=county,
                name=name,
                description=description,
                allowed_disability_types=allowed,
                created_by=request.user,
            )
            messages.success(request, f'Designation "{name}" created.')
            return redirect('supervisor:designation_list')
    return render(request, 'supervisor/create_designation.html', {
        'disability_choices': disability_choices,
        'county': county,
    })


@supervisor_required
def edit_designation(request, pk):
    county = request.user.county
    designation = get_object_or_404(OfficerDesignation, pk=pk, county=county)
    disability_choices = Assessment.DISABILITY_CHOICES
    if request.method == 'POST':
        name = request.POST.get('name', '').strip()
        description = request.POST.get('description', '').strip()
        selected_types = request.POST.getlist('disability_types')
        if not name:
            messages.error(request, 'Designation name is required.')
        else:
            designation.name = name
            designation.description = description
            designation.allowed_disability_types = ','.join(selected_types)
            designation.save()
            messages.success(request, f'Designation "{name}" updated.')
            return redirect('supervisor:designation_list')
    return render(request, 'supervisor/create_designation.html', {
        'disability_choices': disability_choices,
        'designation': designation,
        'county': county,
    })


@supervisor_required
@require_POST
def delete_designation(request, pk):
    county = request.user.county
    designation = get_object_or_404(OfficerDesignation, pk=pk, county=county)
    name = designation.name
    designation_id = designation.pk
    designation.delete()
    record_audit_event(
        'designation.deleted',
        actor=request.user,
        entity_type='designation',
        entity_id=designation_id,
        metadata={'county_id': county.pk},
    )
    messages.success(request, f'Designation "{name}" deleted.')
    return redirect('supervisor:designation_list')


@supervisor_required
def assign_designation(request, pk):
    county = request.user.county
    officer = get_object_or_404(
        CustomUser, pk=pk,
        role__in=['medical_officer', 'health_officer'],
        county=county
    )
    designations = OfficerDesignation.objects.filter(county=county)
    if request.method == 'POST':
        previous_designation_id = officer.designation_id
        designation_id = request.POST.get('designation_id') or None
        if designation_id:
            designation = get_object_or_404(OfficerDesignation, pk=designation_id, county=county)
            officer.designation = designation
        else:
            officer.designation = None
        officer.save()
        record_audit_event(
            'user.designation_assigned',
            actor=request.user,
            entity_type='user',
            entity_id=officer.pk,
            metadata={
                'role': officer.role,
                'old_designation_id': previous_designation_id,
                'new_designation_id': officer.designation_id,
            },
        )
        msg = f'Designation assigned to {officer.get_full_name()}.' if designation_id else f'Designation cleared for {officer.get_full_name()}.'
        messages.success(request, msg)
        return redirect('supervisor:officer_list')
    return render(request, 'supervisor/assign_designation.html', {
        'officer': officer,
        'designations': designations,
    })


@supervisor_required
def profile(request):
    return render(request, 'supervisor/profile.html', {'user': request.user})


@supervisor_required
def change_password(request):
    return password_change_response(request, 'supervisor/change_password.html')
