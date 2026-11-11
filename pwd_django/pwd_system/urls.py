from django.contrib import admin
from django.urls import path, include
from django.conf import settings
from django.conf.urls.static import static
from django.shortcuts import render

from core.models import Hospital
from core import notification_views
from core.chatbot_views import chatbot_feedback, chatbot_response
from core import health_views
from pwd_portal.views import verify_certificate


def landing(request):
    if request.user.is_authenticated:
        from accounts.views import _redirect_by_role
        return _redirect_by_role(request.user)
    hospital_count = Hospital.objects.count()
    categories = [
        {'icon': 'fas fa-walking',        'name': 'Physical'},
        {'icon': 'fas fa-deaf',           'name': 'Hearing'},
        {'icon': 'fas fa-eye-slash',      'name': 'Visual'},
        {'icon': 'fas fa-brain',          'name': 'Mental'},
        {'icon': 'fas fa-smile-beam',     'name': 'Maxillofacial'},
        {'icon': 'fas fa-heartbeat',      'name': 'Chronic'},
        {'icon': 'fas fa-comment-medical','name': 'Speech, Language & Communication'},
    ]
    return render(request, 'public/landing.html', {
        'hospital_count': hospital_count,
        'categories': categories,
    })


urlpatterns = [
    path('admin/', admin.site.urls),
    path('', landing, name='landing'),
    path('', include('accounts.urls')),
    path('pwd/', include('pwd_portal.urls')),
    path('medical/', include('medical.urls')),
    path('health/', include('health.urls')),
    path('supervisor/', include('supervisor.urls')),
    path('hospital/', include('hospital_admin.urls')),
    path('national/', include('national_admin.urls')),
    path('api/', include('core.api_urls')),
    path('health/live/', health_views.live, name='health_live'),
    path('health/ready/', health_views.ready, name='health_ready'),
    path('verify/<uuid:token>/', verify_certificate, name='verify_certificate'),
    path('notifications/<int:pk>/read/', notification_views.mark_read, name='notification_read'),
    path('notifications/read-all/', notification_views.mark_all_read, name='notification_read_all'),
    path('notifications/count/', notification_views.unread_count, name='notification_count'),
    path('chatbot/', chatbot_response, name='chatbot'),
    path('chatbot/feedback/', chatbot_feedback, name='chatbot_feedback'),
] + static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
