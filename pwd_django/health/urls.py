from django.urls import path
from . import views

app_name = 'health'

urlpatterns = [
    path('dashboard/', views.dashboard, name='dashboard'),
    path('assessments/', views.assessment_list, name='assessment_list'),
    path('assessment/<int:pk>/', views.view_assessment, name='view_assessment'),
    path('profile/', views.profile, name='profile'),
    path('profile/password/', views.change_password, name='change_password'),
]
