from django.contrib.auth.models import AbstractUser
from django.db import models


class CustomUser(AbstractUser):
    ROLE_CHOICES = [
        ('pwd', 'PWD Citizen'),
        ('medical_officer', 'Medical Officer'),
        ('health_officer', 'Health Officer'),
        ('county_officer', 'County Officer'),
    ]
    GENDER_CHOICES = [('male', 'Male'), ('female', 'Female'), ('other', 'Other')]
    MARITAL_CHOICES = [('single', 'Single'), ('married', 'Married'), ('divorced', 'Divorced'), ('widowed', 'Widowed')]
    EDUCATION_CHOICES = [
        ('none', 'None'), ('primary', 'Primary'), ('secondary', 'Secondary'),
        ('diploma', 'Diploma'), ('degree', 'Degree'), ('postgraduate', 'Postgraduate'),
    ]

    role = models.CharField(max_length=20, choices=ROLE_CHOICES, default='pwd')
    mobile_number = models.CharField(max_length=15, blank=True)
    date_of_birth = models.DateField(null=True, blank=True)
    gender = models.CharField(max_length=10, choices=GENDER_CHOICES, blank=True)
    county = models.ForeignKey('core.County', on_delete=models.SET_NULL, null=True, blank=True)
    subcounty = models.CharField(max_length=100, blank=True)
    occupation = models.CharField(max_length=100, blank=True)
    marital_status = models.CharField(max_length=20, choices=MARITAL_CHOICES, blank=True)
    education_level = models.CharField(max_length=20, choices=EDUCATION_CHOICES, blank=True)
    next_of_kin_name = models.CharField(max_length=100, blank=True)
    next_of_kin_mobile = models.CharField(max_length=15, blank=True)
    next_of_kin_relationship = models.CharField(max_length=50, blank=True)

    # PWD citizen field
    id_number = models.CharField(max_length=20, unique=True, null=True, blank=True)

    # Officials fields
    license_id = models.CharField(max_length=50, unique=True, null=True, blank=True)
    hospital = models.ForeignKey('core.Hospital', on_delete=models.SET_NULL, null=True, blank=True)
    is_approved = models.BooleanField(default=False)

    class Meta:
        verbose_name = 'User'
        verbose_name_plural = 'Users'

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
