from django.urls import path
from . import views

app_name = 'supervisor'

urlpatterns = [
    path('dashboard/', views.dashboard, name='dashboard'),
    path('assessments/', views.assessment_list, name='assessment_list'),
    path('assessment/<int:pk>/', views.view_assessment, name='view_assessment'),
    path('hospitals/', views.hospital_list, name='hospital_list'),
    path('hospitals/add/', views.add_hospital, name='add_hospital'),
    path('officers/', views.officer_list, name='officer_list'),
    path('officers/<int:pk>/approve/', views.approve_officer, name='approve_officer'),
    path('geography/', views.geography, name='geography'),
    path('geography/subcounty/add/', views.add_subcounty, name='add_subcounty'),
    path('profile/', views.profile, name='profile'),
    path('profile/password/', views.change_password, name='change_password'),
]
