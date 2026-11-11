from django.shortcuts import render, redirect
from django.contrib.auth import authenticate, login, logout
from django.contrib import messages
from django.views.decorators.http import require_POST
from django.conf import settings
from django.core.cache import cache
import hashlib
import logging

from core.audit import record_audit_event


logger = logging.getLogger(__name__)

from core.models import County
from core.notifications import notify_and_email, send_email
from .models import CustomUser
from .forms import PWDRegistrationForm, OfficialRegistrationForm


def login_view(request):
    if request.user.is_authenticated:
        return _redirect_by_role(request.user)

    if request.method == 'POST':
        role = request.POST.get('role', 'pwd')
        username = request.POST.get('username', '').strip()
        password = request.POST.get('password', '')

        forwarded_for = request.META.get('HTTP_X_FORWARDED_FOR', '')
        client_ip = forwarded_for.split(',')[0].strip() if forwarded_for else request.META.get('REMOTE_ADDR', '')
        fingerprint = hashlib.sha256(f'{role}:{username.lower()}:{client_ip}'.encode()).hexdigest()
        attempts_key = f'login-attempts:{fingerprint}'
        attempts = cache.get(attempts_key, 0)
        if attempts >= settings.LOGIN_MAX_ATTEMPTS:
            record_audit_event(
                'authentication.login_throttled',
                entity_type='authentication',
                metadata={'role': role},
            )
            logger.warning('login_throttled', extra={'attempted_role': role})
            messages.error(request, 'Too many login attempts. Please try again later.')
            return render(request, 'public/login.html', {'counties': County.objects.all()}, status=429)

        user = authenticate(request, username=username, password=password, role=role)
        if user:
            cache.delete(attempts_key)
            login(request, user)
            record_audit_event(
                'authentication.login_succeeded',
                actor=user,
                entity_type='user',
                entity_id=user.pk,
                metadata={'role': user.role},
            )
            if user.email:
                send_email(user.email, 'New Login to Your Account', 'login_alert.html', {'user': user})
            return _redirect_by_role(user)
        else:
            cache.set(attempts_key, attempts + 1, settings.LOGIN_LOCKOUT_SECONDS)
            logger.warning('login_failed', extra={'attempted_role': role})
            messages.error(request, 'Invalid credentials or account not activated.')

    return render(request, 'public/login.html', {'counties': County.objects.all()})


@require_POST
def logout_view(request):
    logout(request)
    return redirect('accounts:login')


def register_view(request):
    if request.method == 'POST':
        form = PWDRegistrationForm(request.POST)
        if form.is_valid():
            user = form.save(commit=False)
            user.role = 'pwd'
            user.set_password(form.cleaned_data['password'])
            user.is_approved = True
            user.save()
            if user.email:
                send_email(user.email, 'Welcome to PWD Access Kenya', 'registration.html', {'user': user})
            messages.success(request, 'Registration successful! Please log in.')
            return redirect('accounts:login')
        else:
            for field, errors in form.errors.items():
                for error in errors:
                    messages.error(request, f"{field}: {error}")
    else:
        form = PWDRegistrationForm()

    counties = County.objects.all()
    return render(request, 'public/register.html', {'form': form, 'counties': counties})


def official_register_view(request):
    if request.method == 'POST':
        form = OfficialRegistrationForm(request.POST)
        if form.is_valid():
            user = form.save(commit=False)
            user.role = form.cleaned_data['role']
            user.set_password(form.cleaned_data['password'])
            user.is_approved = False
            user.save()
            if user.email:
                send_email(user.email, 'Registration Received – Pending Activation', 'official_registered.html', {'user': user})
            messages.success(request, 'Registration submitted! Await county officer activation.')
            return redirect('accounts:login')
        else:
            for field, errors in form.errors.items():
                for error in errors:
                    messages.error(request, f"{field}: {error}")
    else:
        form = OfficialRegistrationForm()

    counties = County.objects.all()
    from core.models import Hospital
    hospitals = Hospital.objects.all()
    return render(request, 'public/official_register.html', {
        'form': form, 'counties': counties, 'hospitals': hospitals
    })



def _redirect_by_role(user):
    role_urls = {
        'pwd': '/pwd/dashboard/',
        'medical_officer': '/medical/dashboard/',
        'health_officer': '/health/dashboard/',
        'county_officer': '/supervisor/dashboard/',
        'hospital_admin': '/hospital/dashboard/',
        'national_admin': '/national/dashboard/',
    }
    return redirect(role_urls.get(user.role, '/'))
