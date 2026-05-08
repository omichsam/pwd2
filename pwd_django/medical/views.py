from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.db.models import Count, Avg, F
from django.utils import timezone

from core.decorators import medical_required
from core.models import Assessment, Hospital, County
from core.disability_models import (
    HearingAssessment, PhysicalAssessment, VisualAssessment,
    MentalAssessment, MaxillofacialAssessment, ChronicAssessment
)
from .forms import (
    HearingForm, PhysicalForm, VisualForm, MentalForm,
    MaxillofacialForm, ChronicForm
)

DISABILITY_FORMS = {
    'hearing': (HearingAssessment, HearingForm),
    'physical': (PhysicalAssessment, PhysicalForm),
    'visual': (VisualAssessment, VisualForm),
    'mental': (MentalAssessment, MentalForm),
    'maxillofacial': (MaxillofacialAssessment, MaxillofacialForm),
    'chronic': (ChronicAssessment, ChronicForm),
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

    stats = {
        'total': qs.count(),
        'pending': qs.filter(status='pending').count(),
        'in_review': qs.filter(status='checked').count(),
        'approved': qs.filter(status='approved_by_county_officer').count(),
    }
    pending_list = qs.filter(status='pending').select_related('user')[:10]

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
    hospital = request.user.hospital
    assessments = Assessment.objects.filter(
        hospital=hospital, status='pending'
    ).select_related('user').order_by('-created_at')
    return render(request, 'medical/assessment_list.html', {'assessments': assessments})


@medical_required
def view_assessment(request, pk):
    assessment = get_object_or_404(Assessment, pk=pk, hospital=request.user.hospital)
    return render(request, 'medical/view_assessment.html', {'assessment': assessment})


@medical_required
def assessment_form(request, pk, disability_type):
    assessment = get_object_or_404(Assessment, pk=pk, hospital=request.user.hospital, status='pending')

    if disability_type not in DISABILITY_FORMS:
        messages.error(request, 'Invalid disability type.')
        return redirect('medical:assessment_list')

    ModelClass, FormClass = DISABILITY_FORMS[disability_type]
    existing = ModelClass.objects.filter(assessment=assessment).first()

    if request.method == 'POST':
        form = FormClass(request.POST, instance=existing)
        if form.is_valid():
            detail = form.save(commit=False)
            detail.assessment = assessment
            detail.save()
            assessment.status = 'checked'
            assessment.medical_officer = request.user
            assessment.save()
            messages.success(request, 'Assessment completed successfully.')
            return redirect('medical:dashboard')
    else:
        form = FormClass(instance=existing)

    return render(request, 'medical/forms/generic_form.html', {
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
            messages.success(request, 'Password changed. Please log in again.')
            return redirect('accounts:login')
    return render(request, 'medical/change_password.html')
