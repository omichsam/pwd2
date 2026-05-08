from django.contrib import admin
from django.contrib.auth.admin import UserAdmin
from .models import CustomUser


@admin.register(CustomUser)
class CustomUserAdmin(UserAdmin):
    list_display = ('username', 'email', 'first_name', 'last_name', 'role', 'is_approved')
    list_filter = ('role', 'is_approved', 'is_active')
    fieldsets = UserAdmin.fieldsets + (
        ('PWD Info', {'fields': ('role', 'id_number', 'license_id', 'mobile_number',
                                  'date_of_birth', 'gender', 'county', 'subcounty',
                                  'occupation', 'marital_status', 'education_level',
                                  'next_of_kin_name', 'next_of_kin_mobile',
                                  'next_of_kin_relationship', 'hospital', 'is_approved')}),
    )
