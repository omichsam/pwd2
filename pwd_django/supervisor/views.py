from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.db.models import Count, Avg
from django.utils import timezone

from core.decorators import supervisor_required
from core.models import Assessment, Hospital, County, SubCounty
from accounts.models import CustomUser
from .forms import HospitalForm


@supervisor_required
def dashboard(request):
    user = request.user
    county = user.county
    qs = Assessment.objects.filter(hospital__county=county) if county else Assessment.objects.all()

    stats = {
        'total': qs.count(),
        'pending': qs.filter(status='pending').count(),
        'medical_review': qs.filter(status='checked').count(),
        'health_approved': qs.filter(status='approved_by_health_officer').count(),
        'fully_approved': qs.filter(status='approved_by_county_officer').count(),
        'rejected': qs.filter(status='rejected').count(),
    }

    hospitals = Hospital.objects.filter(county=county).annotate(
        total=Count('assessments'),
        approved=Count('assessments', filter=__import__('django.db.models', fromlist=['Q']).Q(
            assessments__status='approved_by_county_officer'
        ))
    ).order_by('-total')[:5]

    medical_officers = CustomUser.objects.filter(
        role='medical_officer', county=county, is_approved=True
    ).count()
    health_officers = CustomUser.objects.filter(
        role='health_officer', county=county, is_approved=True
    ).count()
    hospital_count = Hospital.objects.filter(county=county).count()

    disability_data = list(
        qs.values('disability_type').annotate(count=Count('id')).order_by('-count')
    )
    disability_labels = [d['disability_type'].title() for d in disability_data]
    disability_counts = [d['count'] for d in disability_data]

    pending_approval = qs.filter(
        status='approved_by_health_officer'
    ).select_related('user', 'hospital')[:10]

    ctx = {
        'stats': stats,
        'hospitals': hospitals,
        'medical_officers': medical_officers,
        'health_officers': health_officers,
        'hospital_count': hospital_count,
        'disability_labels': disability_labels,
        'disability_counts': disability_counts,
        'pending_approval': pending_approval,
        'county': county,
    }
    return render(request, 'supervisor/dashboard.html', ctx)


@supervisor_required
def assessment_list(request):
    county = request.user.county
    assessments = Assessment.objects.filter(
        hospital__county=county, status='approved_by_health_officer'
    ).select_related('user', 'hospital').order_by('-updated_at')
    return render(request, 'supervisor/assessment_list.html', {'assessments': assessments})


@supervisor_required
def view_assessment(request, pk):
    county = request.user.county
    assessment = get_object_or_404(Assessment, pk=pk, hospital__county=county)

    if request.method == 'POST':
        action = request.POST.get('action')
        if action == 'approve':
            assessment.status = 'approved_by_county_officer'
            assessment.county_officer = request.user
            assessment.save()
            messages.success(request, 'Assessment fully certified!')
        elif action == 'reject':
            assessment.status = 'rejected'
            assessment.county_officer = request.user
            assessment.rejection_reason = request.POST.get('reason', '')
            assessment.save()
            messages.warning(request, 'Assessment rejected.')
        return redirect('supervisor:assessment_list')

    return render(request, 'supervisor/view_assessment.html', {'assessment': assessment})


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
    return render(request, 'supervisor/officers.html', {'officers': officers})


@supervisor_required
def approve_officer(request, pk):
    officer = get_object_or_404(
        CustomUser, pk=pk,
        role__in=['medical_officer', 'health_officer'],
        county=request.user.county
    )
    if request.method == 'POST':
        action = request.POST.get('action')
        officer.is_approved = (action == 'activate')
        officer.save()
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
def add_subcounty(request):
    if request.method == 'POST':
        name = request.POST.get('name', '').strip()
        county = request.user.county
        if name and county:
            SubCounty.objects.get_or_create(name=name, county=county)
            messages.success(request, f'Sub-county "{name}" added.')
        return redirect('supervisor:geography')
    return redirect('supervisor:geography')


@supervisor_required
def profile(request):
    return render(request, 'supervisor/profile.html', {'user': request.user})


@supervisor_required
def change_password(request):
    user = request.user
    if request.method == 'POST':
        current = request.POST.get('current_password')
        new = request.POST.get('new_password')
        confirm = request.POST.get('confirm_password')
        if not user.check_password(current):
            messages.error(request, 'Current password incorrect.')
        elif new != confirm:
            messages.error(request, 'New passwords do not match.')
        else:
            user.set_password(new)
            user.save()
            messages.success(request, 'Password changed.')
            return redirect('accounts:login')
    return render(request, 'supervisor/change_password.html')
