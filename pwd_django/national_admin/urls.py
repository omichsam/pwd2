from django.urls import path
from . import chatbot_views, doc_checker_views, views

app_name = 'national_admin'

urlpatterns = [
    path('dashboard/', views.dashboard, name='dashboard'),
    path('counties/', views.county_breakdown, name='county_breakdown'),
    path('counties/add/', views.add_county, name='add_county'),
    path('counties/<int:pk>/', views.county_detail, name='county_detail'),
    path('hospitals/', views.hospital_list, name='hospital_list'),
    path('officers/', views.officer_list, name='officer_list'),
    path('county-officers/', views.county_officer_list, name='county_officer_list'),
    path('county-officers/create/', views.create_county_officer, name='create_county_officer'),
    path('county-officers/<int:pk>/edit/', views.edit_county_officer, name='edit_county_officer'),
    path('county-officers/<int:pk>/toggle/', views.toggle_officer, name='toggle_officer'),
    path('chatbot/', chatbot_views.chatbot_settings, name='chatbot_settings'),
    path('chatbot/try/', chatbot_views.chatbot_settings_try, name='chatbot_settings_try'),
    path('document-checker/', doc_checker_views.doc_checker_settings, name='doc_checker_settings'),
    path('document-checker/try/', doc_checker_views.doc_checker_settings_try, name='doc_checker_settings_try'),
    path('profile/', views.profile, name='profile'),
]
