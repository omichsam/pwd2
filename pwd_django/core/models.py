from django.conf import settings
from django.db import models
from django.utils import timezone


class County(models.Model):
    name = models.CharField(max_length=100)

    class Meta:
        verbose_name_plural = 'Counties'
        ordering = ['name']

    def __str__(self):
        return self.name


class SubCounty(models.Model):
    name = models.CharField(max_length=100)
    county = models.ForeignKey(County, on_delete=models.CASCADE, related_name='subcounties')

    class Meta:
        verbose_name_plural = 'Sub Counties'
        ordering = ['name']

    def __str__(self):
        return f"{self.name} ({self.county.name})"


class Hospital(models.Model):
    name = models.CharField(max_length=200)
    county = models.ForeignKey(County, on_delete=models.CASCADE, related_name='hospitals')
    subcounty = models.CharField(max_length=100, blank=True)
    address = models.TextField(blank=True)
    phone = models.CharField(max_length=20, blank=True)
    email = models.EmailField(blank=True)
    created_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        ordering = ['name']

    def __str__(self):
        return self.name

    def get_approval_rate(self):
        total = self.assessments.count()
        if not total:
            return 0
        approved = self.assessments.filter(status='approved_by_county_officer').count()
        return round((approved / total) * 100)


class Assessment(models.Model):
    STATUS_CHOICES = [
        ('pending', 'Pending'),
        ('checked', 'Medical Review Complete'),
        ('approved_by_health_officer', 'Health Officer Approved'),
        ('approved_by_county_officer', 'Fully Certified'),
        ('rejected', 'Rejected'),
    ]
    DISABILITY_CHOICES = [
        ('physical', 'Physical'),
        ('hearing', 'Hearing'),
        ('visual', 'Visual'),
        ('mental', 'Mental'),
        ('maxillofacial', 'Maxillofacial'),
        ('chronic', 'Chronic'),
    ]

    user = models.ForeignKey(
        settings.AUTH_USER_MODEL, on_delete=models.CASCADE, related_name='assessments'
    )
    hospital = models.ForeignKey(Hospital, on_delete=models.SET_NULL, null=True, related_name='assessments')
    disability_type = models.CharField(max_length=20, choices=DISABILITY_CHOICES)
    status = models.CharField(max_length=40, choices=STATUS_CHOICES, default='pending')
    assessment_date = models.DateField()
    notes = models.TextField(blank=True)

    medical_officer = models.ForeignKey(
        settings.AUTH_USER_MODEL, null=True, blank=True,
        related_name='medical_assessments', on_delete=models.SET_NULL
    )
    health_officer = models.ForeignKey(
        settings.AUTH_USER_MODEL, null=True, blank=True,
        related_name='health_assessments', on_delete=models.SET_NULL
    )
    county_officer = models.ForeignKey(
        settings.AUTH_USER_MODEL, null=True, blank=True,
        related_name='county_assessments', on_delete=models.SET_NULL
    )
    rejection_reason = models.TextField(blank=True)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    class Meta:
        ordering = ['-created_at']

    def __str__(self):
        return f"Assessment #{self.id} - {self.user.get_full_name()} ({self.get_disability_type_display()})"

    def get_days_pending(self):
        return (timezone.now().date() - self.created_at.date()).days

    def get_processing_days(self):
        if self.status == 'approved_by_county_officer':
            return (self.updated_at.date() - self.created_at.date()).days
        return self.get_days_pending()

    def get_status_color(self):
        colors = {
            'pending': 'amber',
            'checked': 'sky',
            'approved_by_health_officer': 'teal',
            'approved_by_county_officer': 'emerald',
            'rejected': 'red',
        }
        return colors.get(self.status, 'gray')

    def get_status_step(self):
        steps = {
            'pending': 1,
            'checked': 2,
            'approved_by_health_officer': 3,
            'approved_by_county_officer': 4,
            'rejected': 0,
        }
        return steps.get(self.status, 0)


class Document(models.Model):
    assessment = models.ForeignKey(Assessment, on_delete=models.CASCADE, related_name='documents')
    file = models.FileField(upload_to='documents/%Y/%m/')
    document_type = models.CharField(max_length=50, blank=True)
    uploaded_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"Document for Assessment #{self.assessment_id}"
