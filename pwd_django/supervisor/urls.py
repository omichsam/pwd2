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
    path('certificates/', views.certified_list, name='certified_list'),
    path('hospital-admins/', views.hospital_admin_list, name='hospital_admin_list'),
    path('hospital-admins/create/', views.create_hospital_admin, name='create_hospital_admin'),
    path('hospital-admins/<int:pk>/toggle/', views.toggle_hospital_admin, name='toggle_hospital_admin'),
    path('certificate/<int:pk>/print/', views.certificate_print, name='certificate_print'),
    path('certificate/<int:pk>/revoke/', views.certificate_revoke, name='certificate_revoke'),
    path('certificate/<int:pk>/reissue/', views.certificate_reissue, name='certificate_reissue'),
    path('officers/<int:pk>/reassign/', views.reassign_officer, name='reassign_officer'),
    path('officers/<int:pk>/designate/', views.assign_designation, name='assign_designation'),
    path('designations/', views.designation_list, name='designation_list'),
    path('designations/create/', views.create_designation, name='create_designation'),
    path('designations/<int:pk>/edit/', views.edit_designation, name='edit_designation'),
    path('designations/<int:pk>/delete/', views.delete_designation, name='delete_designation'),
    path('profile/', views.profile, name='profile'),
    path('profile/password/', views.change_password, name='change_password'),
]
