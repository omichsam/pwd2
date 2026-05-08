from django.shortcuts import render, redirect
from django.contrib.auth import authenticate, login, logout
from django.contrib import messages
from django.views.decorators.http import require_POST

from core.models import County
from .models import CustomUser
from .forms import PWDRegistrationForm, OfficialRegistrationForm


def login_view(request):
    if request.user.is_authenticated:
        return _redirect_by_role(request.user)

    if request.method == 'POST':
        role = request.POST.get('role', 'pwd')
        username = request.POST.get('username', '').strip()
        password = request.POST.get('password', '')

        user = authenticate(request, username=username, password=password, role=role)
        if user:
            login(request, user)
            return _redirect_by_role(user)
        else:
            messages.error(request, 'Invalid credentials or account not activated.')

    return render(request, 'public/login.html', {'counties': County.objects.all()})


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


def forgot_password_view(request):
    return render(request, 'public/forgot_password.html')


def _redirect_by_role(user):
    role_urls = {
        'pwd': '/pwd/dashboard/',
        'medical_officer': '/medical/dashboard/',
        'health_officer': '/health/dashboard/',
        'county_officer': '/supervisor/dashboard/',
    }
    return redirect(role_urls.get(user.role, '/'))
