from django.urls import path
from . import views

app_name = 'hospital_admin'

urlpatterns = [
    path('dashboard/', views.dashboard, name='dashboard'),
    path('officers/', views.officer_list, name='officer_list'),
    path('officers/<int:pk>/toggle/', views.toggle_officer, name='toggle_officer'),
    path('assessments/', views.assessment_summary, name='assessment_summary'),
    path('profile/', views.profile, name='profile'),
    path('profile/password/', views.change_password, name='change_password'),
]
