from django.contrib.auth.backends import ModelBackend
from .models import CustomUser


class PWDAuthBackend(ModelBackend):
    def authenticate(self, request, username=None, password=None, role=None, **kwargs):
        try:
            if role == 'pwd':
                user = CustomUser.objects.get(id_number=username, role='pwd')
            else:
                user = CustomUser.objects.get(license_id=username, is_approved=True)
                if user.role not in ('medical_officer', 'health_officer', 'county_officer', 'hospital_admin', 'national_admin'):
                    return None
        except CustomUser.DoesNotExist:
            CustomUser().set_password(password)
            return None

        if user.check_password(password) and self.user_can_authenticate(user):
            return user
        return None

    def get_user(self, user_id):
        try:
            return CustomUser.objects.get(pk=user_id)
        except CustomUser.DoesNotExist:
            return None
