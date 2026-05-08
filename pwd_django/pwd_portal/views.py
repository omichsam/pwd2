from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.utils import timezone

from core.decorators import pwd_required
from core.models import Assessment, Hospital, County
from .forms import ApplicationForm, ProfileForm, ChangePasswordForm


@pwd_required
def dashboard(request):
    user = request.user
    assessments = Assessment.objects.filter(user=user).select_related('hospital')
    latest = assessments.first()
    hospitals = Hospital.objects.filter(county=user.county).order_by('name')[:6]

    stats = {
        'total': assessments.count(),
        'approved': assessments.filter(status='approved_by_county_officer').count(),
        'in_progress': assessments.filter(
            status__in=['checked', 'approved_by_health_officer']
        ).count(),
        'pending': assessments.filter(status='pending').count(),
        'rejected': assessments.filter(status='rejected').count(),
    }

    ctx = {
        'user': user,
        'stats': stats,
        'latest': latest,
        'assessments': assessments[:10],
        'hospitals': hospitals,
        'points': user.get_points(),
        'level': user.get_level(),
    }
    return render(request, 'pwd/dashboard.html', ctx)


@pwd_required
def application(request):
    user = request.user
    existing_pending = Assessment.objects.filter(
        user=user, status__in=['pending', 'checked', 'approved_by_health_officer']
    ).first()

    if request.method == 'POST':
        form = ApplicationForm(request.POST, user=user)
        if form.is_valid():
            assessment = form.save(commit=False)
            assessment.user = user
            assessment.save()
            messages.success(request, 'Assessment appointment booked successfully!')
            return redirect('pwd:dashboard')
    else:
        form = ApplicationForm(user=user)

    counties = County.objects.all().order_by('name')
    return render(request, 'pwd/application.html', {
        'form': form,
        'counties': counties,
        'existing_pending': existing_pending,
        'today': timezone.now().date().isoformat(),
    })


@pwd_required
def view_application(request, pk):
    assessment = get_object_or_404(Assessment, pk=pk, user=request.user)
    return render(request, 'pwd/view_application.html', {'assessment': assessment})


@pwd_required
def hospitals(request):
    selected_county_id = request.GET.get('county')
    counties = County.objects.all().order_by('name')
    if selected_county_id:
        hospital_list = Hospital.objects.filter(county_id=selected_county_id).select_related('county')
    else:
        hospital_list = Hospital.objects.filter(county=request.user.county).select_related('county')
    return render(request, 'pwd/hospitals.html', {
        'hospitals': hospital_list,
        'counties': counties,
        'selected_county': int(selected_county_id) if selected_county_id else None,
    })


@pwd_required
def profile(request):
    return render(request, 'pwd/profile.html', {'user': request.user})


@pwd_required
def edit_profile(request):
    user = request.user
    if request.method == 'POST':
        form = ProfileForm(request.POST, instance=user)
        if form.is_valid():
            form.save()
            messages.success(request, 'Profile updated successfully.')
            return redirect('pwd:profile')
    else:
        form = ProfileForm(instance=user)
    return render(request, 'pwd/edit_profile.html', {
        'form': form,
        'user': user,
        'gender_choices': [('male', 'Male'), ('female', 'Female'), ('other', 'Other')],
        'marital_choices': [('single', 'Single'), ('married', 'Married'), ('divorced', 'Divorced'), ('widowed', 'Widowed')],
        'education_choices': [
            ('none', 'None'), ('primary', 'Primary'), ('secondary', 'Secondary'),
            ('certificate', 'Certificate'), ('diploma', 'Diploma'),
            ('degree', 'Bachelor\'s Degree'), ('postgraduate', 'Postgraduate'),
        ],
    })


@pwd_required
def change_password(request):
    user = request.user
    if request.method == 'POST':
        form = ChangePasswordForm(request.POST)
        if form.is_valid():
            if not user.check_password(form.cleaned_data['current_password']):
                messages.error(request, 'Current password is incorrect.')
            else:
                user.set_password(form.cleaned_data['new_password'])
                user.save()
                messages.success(request, 'Password changed. Please log in again.')
                return redirect('accounts:login')
    else:
        form = ChangePasswordForm()
    return render(request, 'pwd/change_password.html', {'form': form})


@pwd_required
def certificate_print(request, pk):
    assessment = get_object_or_404(
        Assessment, pk=pk, user=request.user, status='approved_by_county_officer'
    )
    return render(request, 'pwd/certificate_print.html', {'assessment': assessment, 'qr_code': None})
