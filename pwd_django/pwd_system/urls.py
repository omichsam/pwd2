from django.contrib import admin
from django.urls import path, include
from django.conf import settings
from django.conf.urls.static import static
from django.shortcuts import render

from core.models import Hospital


def landing(request):
    if request.user.is_authenticated:
        from accounts.views import _redirect_by_role
        return _redirect_by_role(request.user)
    hospital_count = Hospital.objects.count()
    return render(request, 'public/landing.html', {'hospital_count': hospital_count})


urlpatterns = [
    path('admin/', admin.site.urls),
    path('', landing, name='landing'),
    path('', include('accounts.urls')),
    path('pwd/', include('pwd_portal.urls')),
    path('medical/', include('medical.urls')),
    path('health/', include('health.urls')),
    path('supervisor/', include('supervisor.urls')),
    path('api/', include('core.api_urls')),
] + static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
