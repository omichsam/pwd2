from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.utils import timezone
from django.core.exceptions import ObjectDoesNotExist
from django.db import IntegrityError, transaction
from django.conf import settings
from django.core.cache import cache

from core.decorators import pwd_required
from core.models import Assessment, Hospital, County, Certificate
from core.certificates import build_certificate_context, get_active_certificate
from .forms import ApplicationForm, ProfileForm
from accounts.services import password_change_response


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
            try:
                with transaction.atomic():
                    assessment = form.save(commit=False)
                    assessment.user = user
                    assessment.save()
            except IntegrityError:
                if Assessment.objects.filter(
                    user=user,
                    status__in=['pending', 'checked', 'approved_by_health_officer'],
                ).exists():
                    form.add_error(
                        None,
                        'You already have an active assessment. Review it before booking another.',
                    )
                else:
                    raise
            else:
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
def assessment_detail(request, pk):
    assessment = get_object_or_404(Assessment, pk=pk, user=request.user)
    ctx = {'assessment': assessment}

    detail_map = {
        'hearing': 'hearing_detail',
        'physical': 'physical_detail',
        'visual': 'visual_detail',
        'mental': 'mental_detail',
        'maxillofacial': 'maxillofacial_detail',
        'chronic': 'chronic_detail',
    }
    related = detail_map.get(assessment.disability_type)
    if related:
        ctx['detail'] = getattr(assessment, related, None)

    if assessment.disability_type == 'visual':
        try:
            d = assessment.visual_detail
            ctx['ophthalmic_rows'] = [
                ('Present Eyeball',  d.present_eyeball_right,  d.present_eyeball_left),
                ('Squint',           d.squint_right,           d.squint_left),
                ('Nystagmus',        d.nystagmus_right,        d.nystagmus_left),
                ('Tearing',          d.tearing_right,          d.tearing_left),
                ('Lids',             d.lids_right,             d.lids_left),
                ('Conjunctiva',      d.conjunctiva_right,      d.conjunctiva_left),
                ('Cornea',           d.cornea_right,           d.cornea_left),
                ('Anterior Chamber', d.anterior_chamber_right, d.anterior_chamber_left),
                ('Iris',             d.iris_right,             d.iris_left),
                ('Pupil',            d.pupil_right,            d.pupil_left),
                ('Lens',             d.lens_right,             d.lens_left),
                ('Fundus',           d.fundus_right,           d.fundus_left),
            ]
        except ObjectDoesNotExist:
            ctx['ophthalmic_rows'] = []

    return render(request, 'pwd/assessment_detail.html', ctx)


@pwd_required
def view_application(request, pk):
    assessment = get_object_or_404(Assessment, pk=pk, user=request.user)
    detail_attr_map = {
        'hearing': 'hearing_detail',
        'physical': 'physical_detail',
        'visual': 'visual_detail',
        'mental': 'mental_detail',
        'maxillofacial': 'maxillofacial_detail',
        'chronic': 'chronic_detail',
    }
    related_attr = detail_attr_map.get(assessment.disability_type)
    has_detail = False
    if related_attr:
        try:
            has_detail = getattr(assessment, related_attr, None) is not None
        except ObjectDoesNotExist:
            has_detail = False
    return render(request, 'pwd/view_application.html', {
        'assessment': assessment,
        'has_detail': has_detail,
    })


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
    return password_change_response(request, 'pwd/change_password.html')


@pwd_required
def certificate_print(request, pk):
    assessment = get_object_or_404(
        Assessment, pk=pk, user=request.user, status='approved_by_county_officer'
    )
    certificate = get_active_certificate(assessment)
    if not certificate:
        messages.error(request, 'This certificate is unavailable. Please contact the county office.')
        return redirect('pwd:view_application', pk=assessment.pk)
    return render(
        request, 'pwd/certificate_print.html',
        build_certificate_context(request, assessment, certificate),
    )


def verify_certificate(request, token):
    """Verify an opaque token with bounded requests and minimal public disclosure."""
    forwarded_for = request.META.get('HTTP_X_FORWARDED_FOR', '')
    client_ip = forwarded_for.split(',')[0].strip() if forwarded_for else request.META.get('REMOTE_ADDR', '')
    key = f'certificate-verify:{client_ip}'
    attempts = cache.get(key, 0)
    if attempts >= settings.CERTIFICATE_VERIFY_MAX_ATTEMPTS:
        return render(request, 'public/verify_certificate.html', {
            'valid': False, 'error': 'Too many verification attempts. Please try again shortly.',
            'cert_id': '',
        }, status=429)
    cache.set(key, attempts + 1, settings.CERTIFICATE_VERIFY_WINDOW_SECONDS)

    certificate = Certificate.objects.select_related(
        'assessment__hospital__county'
    ).filter(verification_token=token).first()
    valid = bool(certificate and certificate.is_active)
    return render(request, 'public/verify_certificate.html', {
        'certificate': certificate,
        'cert_id': certificate.certificate_number if certificate else '',
        'valid': valid,
        'error': None if valid else 'This certificate is invalid, revoked, or has been replaced.',
    })
