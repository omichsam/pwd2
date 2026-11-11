from django.urls import path
from . import views

app_name = 'pwd'

urlpatterns = [
    path('dashboard/', views.dashboard, name='dashboard'),
    path('application/', views.application, name='application'),
    path('application/<int:pk>/', views.view_application, name='view_application'),
    path('application/<int:pk>/findings/', views.assessment_detail, name='assessment_detail'),
    path('hospitals/', views.hospitals, name='hospitals'),
    path('profile/', views.profile, name='profile'),
    path('profile/edit/', views.edit_profile, name='edit_profile'),
    path('profile/password/', views.change_password, name='change_password'),
    path('certificate/<int:pk>/print/', views.certificate_print, name='certificate_print'),
]
