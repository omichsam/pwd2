from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.db.models import Count

from core.decorators import health_required
from core.models import Assessment


@health_required
def dashboard(request):
    user = request.user
    county = user.county
    qs = Assessment.objects.filter(hospital__county=county) if county else Assessment.objects.none()

    stats = {
        'total': qs.count(),
        'pending_review': qs.filter(status='checked').count(),
        'approved': qs.filter(status__in=['approved_by_health_officer', 'approved_by_county_officer']).count(),
        'rejected': qs.filter(status='rejected').count(),
    }
    pending_list = qs.filter(status='checked').select_related('user', 'hospital')[:10]

    ctx = {'stats': stats, 'pending_list': pending_list, 'county': county}
    return render(request, 'health/dashboard.html', ctx)


@health_required
def assessment_list(request):
    county = request.user.county
    assessments = Assessment.objects.filter(
        hospital__county=county, status='checked'
    ).select_related('user', 'hospital').order_by('-updated_at')
    return render(request, 'health/assessment_list.html', {'assessments': assessments})


@health_required
def view_assessment(request, pk):
    county = request.user.county
    assessment = get_object_or_404(Assessment, pk=pk, hospital__county=county)

    if request.method == 'POST':
        action = request.POST.get('action')
        if action == 'approve':
            assessment.status = 'approved_by_health_officer'
            assessment.health_officer = request.user
            assessment.save()
            messages.success(request, 'Assessment approved.')
        elif action == 'reject':
            assessment.status = 'rejected'
            assessment.health_officer = request.user
            assessment.rejection_reason = request.POST.get('reason', '')
            assessment.save()
            messages.warning(request, 'Assessment rejected.')
        return redirect('health:assessment_list')

    return render(request, 'health/view_assessment.html', {'assessment': assessment})


@health_required
def profile(request):
    total = Assessment.objects.filter(health_officer=request.user).count()
    return render(request, 'health/profile.html', {'user': request.user, 'total_reviews': total})


@health_required
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
    return render(request, 'health/change_password.html')
