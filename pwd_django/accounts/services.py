"""Reusable account workflows shared by role-specific portals."""

from django.contrib import messages
from django.shortcuts import redirect, render

from accounts.forms import ChangePasswordForm
from core.audit import record_audit_event


def password_change_response(request, template_name):
    form = ChangePasswordForm(request.POST or None, user=request.user)
    if request.method == 'POST' and form.is_valid():
        request.user.set_password(form.cleaned_data['new_password'])
        request.user.save(update_fields=['password'])
        record_audit_event(
            'user.password_changed', actor=request.user,
            entity_type='user', entity_id=request.user.pk,
        )
        messages.success(request, 'Password changed. Please log in again.')
        return redirect('accounts:login')
    if request.method == 'POST':
        for errors in form.errors.values():
            for error in errors:
                messages.error(request, error)
    return render(request, template_name, {'form': form})
