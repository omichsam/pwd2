from django.urls import path
from . import views

app_name = 'medical'

urlpatterns = [
    path('dashboard/', views.dashboard, name='dashboard'),
    path('assessments/', views.assessment_list, name='assessment_list'),
    path('assessment/<int:pk>/', views.view_assessment, name='view_assessment'),
    path('assessment/<int:pk>/<str:disability_type>/form/', views.assessment_form, name='assessment_form'),
    path('profile/', views.profile, name='profile'),
    path('profile/password/', views.change_password, name='change_password'),
]
