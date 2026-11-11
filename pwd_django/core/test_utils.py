"""Shared factories for the project's Django tests."""

from accounts.models import CustomUser
from core.models import County, Hospital


TEST_PASSWORD = "Test-password-2026!"


class TestDataMixin:
    """Create a minimal organization and users for each supported role."""

    @classmethod
    def setUpTestData(cls):
        super().setUpTestData()
        cls.county = County.objects.create(name="Test County")
        cls.hospital = Hospital.objects.create(
            name="Test General Hospital",
            county=cls.county,
        )
        cls.users = {}

        for role, _label in CustomUser.ROLE_CHOICES:
            user_kwargs = {
                "username": f"{role}_user",
                "email": f"{role}@example.test",
                "role": role,
                "county": cls.county,
                "is_approved": True,
            }
            if role == "pwd":
                user_kwargs["id_number"] = "TEST-ID-001"
            else:
                user_kwargs["license_id"] = f"TEST-{role.upper()}"
            if role in {"medical_officer", "health_officer", "hospital_admin"}:
                user_kwargs["hospital"] = cls.hospital

            cls.users[role] = CustomUser.objects.create_user(
                password=TEST_PASSWORD,
                **user_kwargs,
            )

