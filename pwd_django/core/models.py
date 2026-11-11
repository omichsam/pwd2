from django.conf import settings
from django.db import models
from django.utils import timezone
from django.core.exceptions import ValidationError
import uuid


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
        ('speech', 'Speech, Language & Communication'),
    ]

    user = models.ForeignKey(
        settings.AUTH_USER_MODEL, on_delete=models.PROTECT, related_name='assessments'
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
    active_slot = models.PositiveSmallIntegerField(
        null=True, default=1, editable=False
    )
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    class Meta:
        ordering = ['-created_at']
        indexes = [
            models.Index(fields=['status', 'created_at'], name='assessment_status_created_idx'),
            models.Index(fields=['hospital', 'status'], name='assessment_hospital_status_idx'),
            models.Index(fields=['user', 'status'], name='assessment_user_status_idx'),
            models.Index(fields=['assessment_date'], name='assessment_date_idx'),
        ]
        constraints = [
            models.UniqueConstraint(
                fields=['user', 'active_slot'],
                name='one_active_assessment_per_user',
            ),
        ]

    def save(self, *args, **kwargs):
        self.active_slot = (
            1 if self.status in {'pending', 'checked', 'approved_by_health_officer'} else None
        )
        update_fields = kwargs.get('update_fields')
        if update_fields is not None:
            kwargs['update_fields'] = set(update_fields) | {'active_slot'}
        super().save(*args, **kwargs)

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


class OfficerDesignation(models.Model):
    county = models.ForeignKey(County, on_delete=models.CASCADE, related_name='designations')
    name = models.CharField(max_length=100)
    description = models.TextField(blank=True)
    allowed_disability_types = models.CharField(
        max_length=200, blank=True,
        help_text="Comma-separated disability types, e.g. hearing,visual. Empty = all types."
    )
    created_by = models.ForeignKey(
        settings.AUTH_USER_MODEL, on_delete=models.SET_NULL,
        null=True, blank=True, related_name='created_designations'
    )
    created_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        ordering = ['county', 'name']

    def __str__(self):
        return f"{self.name} ({self.county.name})"

    def get_allowed_types(self):
        if not self.allowed_disability_types:
            return []
        return [t.strip() for t in self.allowed_disability_types.split(',') if t.strip()]

    def get_allowed_display(self):
        type_map = dict(Assessment.DISABILITY_CHOICES)
        types = self.get_allowed_types()
        return [type_map.get(t, t.title()) for t in types] if types else ['All Types']


class Document(models.Model):
    assessment = models.ForeignKey(Assessment, on_delete=models.CASCADE, related_name='documents')
    file = models.FileField(upload_to='documents/%Y/%m/')
    document_type = models.CharField(max_length=50, blank=True)
    uploaded_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"Document for Assessment #{self.assessment_id}"


class Notification(models.Model):
    TYPE_CHOICES = [
        ('info', 'Info'),
        ('success', 'Success'),
        ('warning', 'Warning'),
        ('error', 'Error'),
    ]
    recipient = models.ForeignKey(
        settings.AUTH_USER_MODEL, on_delete=models.CASCADE, related_name='notifications'
    )
    title = models.CharField(max_length=200)
    message = models.TextField()
    notification_type = models.CharField(max_length=20, choices=TYPE_CHOICES, default='info')
    link = models.CharField(max_length=500, blank=True)
    is_read = models.BooleanField(default=False)
    created_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        ordering = ['-created_at']

    def __str__(self):
        return f'{self.recipient} — {self.title}'


class Certificate(models.Model):
    assessment = models.ForeignKey(
        Assessment, on_delete=models.PROTECT, related_name='certificates'
    )
    certificate_number = models.CharField(max_length=32, unique=True, editable=False)
    verification_token = models.UUIDField(default=uuid.uuid4, unique=True, editable=False)
    issued_at = models.DateTimeField(auto_now_add=True)
    issued_by = models.ForeignKey(
        settings.AUTH_USER_MODEL, null=True, blank=True, on_delete=models.SET_NULL,
        related_name='issued_certificates',
    )
    revoked_at = models.DateTimeField(null=True, blank=True)
    revoked_by = models.ForeignKey(
        settings.AUTH_USER_MODEL, null=True, blank=True, on_delete=models.SET_NULL,
        related_name='revoked_certificates',
    )
    revocation_reason = models.TextField(blank=True)
    supersedes = models.OneToOneField(
        'self', null=True, blank=True, on_delete=models.SET_NULL,
        related_name='replacement',
    )

    class Meta:
        ordering = ['-issued_at']
        constraints = [
            models.UniqueConstraint(
                fields=['assessment'],
                condition=models.Q(revoked_at__isnull=True),
                name='one_active_certificate_per_assessment',
            ),
        ]

    @property
    def is_active(self):
        return (
            self.revoked_at is None
            and self.assessment.status == 'approved_by_county_officer'
        )

    def __str__(self):
        return self.certificate_number


class AuditEvent(models.Model):
    event_id = models.UUIDField(default=uuid.uuid4, editable=False, unique=True)
    created_at = models.DateTimeField(auto_now_add=True, db_index=True)
    actor = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        null=True,
        blank=True,
        on_delete=models.SET_NULL,
        related_name='audit_events',
    )
    actor_role = models.CharField(max_length=30, blank=True)
    action = models.CharField(max_length=80, db_index=True)
    entity_type = models.CharField(max_length=50, db_index=True)
    entity_id = models.CharField(max_length=64, blank=True, db_index=True)
    assessment = models.ForeignKey(
        Assessment,
        null=True,
        blank=True,
        on_delete=models.SET_NULL,
        related_name='audit_events',
    )
    old_status = models.CharField(max_length=40, blank=True)
    new_status = models.CharField(max_length=40, blank=True)
    reason = models.TextField(blank=True)
    request_id = models.UUIDField(null=True, blank=True, db_index=True)
    metadata = models.JSONField(default=dict, blank=True)

    class Meta:
        ordering = ['-created_at']

    def save(self, *args, **kwargs):
        if self.pk:
            raise ValidationError('Audit events are append-only and cannot be updated.')
        super().save(*args, **kwargs)

    def delete(self, *args, **kwargs):
        raise ValidationError('Audit events are append-only and cannot be deleted.')

    def __str__(self):
        return f'{self.created_at:%Y-%m-%d %H:%M:%S} — {self.action}'


class ChatbotSettings(models.Model):
    """Site-wide PWD Assistant settings, edited by the national admin.

    One row at most (pk=1). Until it exists, values come from the environment
    (see `core.chatbot_config`). A blank `docassist_api_key` falls back to
    DOCASSIST_API_KEY, so a key can stay in `.env` if preferred.
    """
    docassist_enabled = models.BooleanField(default=False)
    docassist_base_url = models.URLField(max_length=300, blank=True)
    docassist_api_key = models.CharField(max_length=200, blank=True)
    docassist_timeout_seconds = models.PositiveSmallIntegerField(default=20)

    rag_enabled = models.BooleanField(default=False)
    llm_model = models.CharField(max_length=100, blank=True)

    max_per_window = models.PositiveIntegerField(default=20)
    window_seconds = models.PositiveIntegerField(default=300)

    updated_at = models.DateTimeField(auto_now=True)
    updated_by = models.ForeignKey(
        settings.AUTH_USER_MODEL, null=True, blank=True, on_delete=models.SET_NULL,
        related_name='+',
    )

    class Meta:
        verbose_name = 'Chatbot settings'
        verbose_name_plural = 'Chatbot settings'

    def save(self, *args, **kwargs):
        self.pk = 1
        super().save(*args, **kwargs)

    def __str__(self):
        return 'Chatbot settings'


class DocumentCheckerSettings(models.Model):
    """Site-wide Document Checker API settings, edited by the national admin.

    One row at most (pk=1). Until it exists, values come from the environment
    (see `core.doc_checker_config`). A blank `api_key` falls back to
    DOC_CHECKER_API_KEY, so a key can stay in `.env` if preferred.
    """
    enabled = models.BooleanField(default=False)
    base_url = models.URLField(max_length=300, blank=True)
    api_key = models.CharField(max_length=200, blank=True)
    timeout_seconds = models.PositiveSmallIntegerField(default=45)

    updated_at = models.DateTimeField(auto_now=True)
    updated_by = models.ForeignKey(
        settings.AUTH_USER_MODEL, null=True, blank=True, on_delete=models.SET_NULL,
        related_name='+',
    )

    class Meta:
        verbose_name = 'Document checker settings'
        verbose_name_plural = 'Document checker settings'

    def save(self, *args, **kwargs):
        self.pk = 1
        super().save(*args, **kwargs)

    def __str__(self):
        return 'Document checker settings'


class DocumentCheck(models.Model):
    """Result of the Document Checker API for one uploaded document.

    'Screening aid only' — advisory. The document is kept and the assessment
    proceeds regardless; officers see this to decide on a rescan or a closer look.
    """
    ACTION_CONTINUE = 'continue'
    ACTION_RESCAN = 'request_rescan'
    ACTION_REVIEW = 'manual_review'
    ACTION_CHOICES = [
        (ACTION_CONTINUE, 'Looks fine'),
        (ACTION_RESCAN, 'Rescan needed'),
        (ACTION_REVIEW, 'Manual review'),
    ]
    STATUS_DONE = 'done'
    STATUS_ERROR = 'error'

    document = models.OneToOneField('Document', on_delete=models.CASCADE, related_name='quality_check')
    status = models.CharField(max_length=10, default=STATUS_DONE)
    action = models.CharField(max_length=20, blank=True)
    level = models.CharField(max_length=10, blank=True)         # low / medium / high
    message = models.TextField(blank=True)
    quality_category = models.CharField(max_length=12, blank=True)   # GOOD / ACCEPTABLE / POOR
    quality_advice = models.TextField(blank=True)
    change_score = models.FloatField(null=True, blank=True)
    worst_page = models.PositiveSmallIntegerField(null=True, blank=True)
    page_count = models.PositiveSmallIntegerField(null=True, blank=True)
    check_id = models.CharField(max_length=40, blank=True)
    cached = models.BooleanField(default=False)
    error = models.CharField(max_length=200, blank=True)
    raw = models.JSONField(default=dict, blank=True)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f'Check for Document #{self.document_id}: {self.action or self.status}'

    @property
    def ok(self):
        return self.status == self.STATUS_DONE and self.action == self.ACTION_CONTINUE

    @property
    def needs_attention(self):
        return self.status == self.STATUS_DONE and self.action in (self.ACTION_RESCAN, self.ACTION_REVIEW)

    @property
    def action_label(self):
        return dict(self.ACTION_CHOICES).get(self.action, 'Unknown')
