from django.urls import path
from django.contrib.auth import views as auth_views
from . import views

app_name = 'accounts'

urlpatterns = [
    path('login/', views.login_view, name='login'),
    path('logout/', views.logout_view, name='logout'),
    path('register/', views.register_view, name='register'),
    path('register/official/', views.official_register_view, name='official_register'),

    # Password reset flow (Django built-in + custom templates)
    path('forgot-password/', auth_views.PasswordResetView.as_view(
        template_name='public/forgot_password.html',
        email_template_name='emails/password_reset_email.html',
        subject_template_name='emails/password_reset_subject.txt',
        success_url='/forgot-password/sent/',
    ), name='forgot_password'),
    path('forgot-password/sent/', auth_views.PasswordResetDoneView.as_view(
        template_name='public/password_reset_done.html',
    ), name='password_reset_done'),
    path('reset/<uidb64>/<token>/', auth_views.PasswordResetConfirmView.as_view(
        template_name='public/password_reset_confirm.html',
        success_url='/reset/complete/',
    ), name='password_reset_confirm'),
    path('reset/complete/', auth_views.PasswordResetCompleteView.as_view(
        template_name='public/password_reset_complete.html',
    ), name='password_reset_complete'),
]
