from django.urls import path
from . import views

app_name = 'accounts'

urlpatterns = [
    path('login/', views.login_view, name='login'),
    path('logout/', views.logout_view, name='logout'),
    path('register/', views.register_view, name='register'),
    path('register/official/', views.official_register_view, name='official_register'),
    path('forgot-password/', views.forgot_password_view, name='forgot_password'),
]
