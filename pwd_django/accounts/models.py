from django.contrib.auth.models import AbstractUser
from django.db import models
from django.core.validators import RegexValidator


phone_validator = RegexValidator(
    regex=r'^\+?[0-9][0-9 -]{6,14}$',
    message='Enter a valid phone number containing 7 to 15 digits.',
)
identifier_validator = RegexValidator(
    regex=r'^[A-Za-z0-9][A-Za-z0-9 /-]*$',
    message='Use only letters, numbers, spaces, hyphens, and slashes.',
)


class CustomUser(AbstractUser):
    ROLE_CHOICES = [
        ('pwd', 'PWD Citizen'),
        ('medical_officer', 'Medical Officer'),
        ('health_officer', 'Health Officer'),
        ('county_officer', 'County Officer'),
        ('hospital_admin', 'Hospital Admin'),
        ('national_admin', 'National Admin'),
    ]
    GENDER_CHOICES = [('male', 'Male'), ('female', 'Female'), ('other', 'Other')]
    MARITAL_CHOICES = [('single', 'Single'), ('married', 'Married'), ('divorced', 'Divorced'), ('widowed', 'Widowed')]
    EDUCATION_CHOICES = [
        ('none', 'None'), ('primary', 'Primary'), ('secondary', 'Secondary'),
        ('diploma', 'Diploma'), ('degree', 'Degree'), ('postgraduate', 'Postgraduate'),
    ]

    role = models.CharField(max_length=20, choices=ROLE_CHOICES, default='pwd')
    mobile_number = models.CharField(max_length=15, blank=True, validators=[phone_validator])
    date_of_birth = models.DateField(null=True, blank=True)
    gender = models.CharField(max_length=10, choices=GENDER_CHOICES, blank=True)
    county = models.ForeignKey('core.County', on_delete=models.SET_NULL, null=True, blank=True)
    subcounty = models.CharField(max_length=100, blank=True)
    occupation = models.CharField(max_length=100, blank=True)
    marital_status = models.CharField(max_length=20, choices=MARITAL_CHOICES, blank=True)
    education_level = models.CharField(max_length=20, choices=EDUCATION_CHOICES, blank=True)
    next_of_kin_name = models.CharField(max_length=100, blank=True)
    next_of_kin_mobile = models.CharField(max_length=15, blank=True, validators=[phone_validator])
    next_of_kin_relationship = models.CharField(max_length=50, blank=True)

    # PWD citizen field
    id_number = models.CharField(
        max_length=20, unique=True, null=True, blank=True,
        validators=[identifier_validator],
    )

    # Officials fields
    license_id = models.CharField(
        max_length=50, unique=True, null=True, blank=True,
        validators=[identifier_validator],
    )
    hospital = models.ForeignKey('core.Hospital', on_delete=models.SET_NULL, null=True, blank=True)
    designation = models.ForeignKey(
        'core.OfficerDesignation', on_delete=models.SET_NULL,
        null=True, blank=True, related_name='officers'
    )
    is_approved = models.BooleanField(default=False)

    class Meta:
        verbose_name = 'User'
        verbose_name_plural = 'Users'
        indexes = [
            models.Index(fields=['role', 'is_approved'], name='user_role_active_idx'),
            models.Index(fields=['county', 'role'], name='user_county_role_idx'),
            models.Index(fields=['hospital', 'role'], name='user_hospital_role_idx'),
        ]

    def __str__(self):
        return f"{self.get_full_name() or self.username} ({self.get_role_display()})"

    @property
    def is_pwd(self):
        return self.role == 'pwd'

    @property
    def is_medical_officer(self):
        return self.role == 'medical_officer'

    @property
    def is_health_officer(self):
        return self.role == 'health_officer'

    @property
    def is_county_officer(self):
        return self.role == 'county_officer'

    @property
    def is_hospital_admin(self):
        return self.role == 'hospital_admin'

    @property
    def is_national_admin(self):
        return self.role == 'national_admin'

    def get_points(self):
        approved = self.assessments.filter(status='approved_by_county_officer').count()
        in_progress = self.assessments.filter(
            status__in=['checked', 'approved_by_health_officer']
        ).count()
        return (approved * 100) + (in_progress * 30)

    def get_level(self):
        pts = self.get_points()
        if pts >= 400:
            return 5
        elif pts >= 300:
            return 4
        elif pts >= 200:
            return 3
        elif pts >= 100:
            return 2
        return 1
