from functools import wraps
from django.shortcuts import redirect


def role_required(*roles):
    def decorator(view_func):
        @wraps(view_func)
        def _wrapped(request, *args, **kwargs):
            if not request.user.is_authenticated:
                return redirect('accounts:login')
            if request.user.role not in roles:
                return redirect('accounts:login')
            return view_func(request, *args, **kwargs)
        return _wrapped
    return decorator


def pwd_required(view_func):
    return role_required('pwd')(view_func)


def medical_required(view_func):
    return role_required('medical_officer')(view_func)


def health_required(view_func):
    return role_required('health_officer')(view_func)


def supervisor_required(view_func):
    return role_required('county_officer')(view_func)
