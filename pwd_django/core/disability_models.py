from django.db import models
from .models import Assessment

CONCLUSION_CHOICES = [('temporary', 'Temporary'), ('permanent', 'Permanent')]


class HearingAssessment(models.Model):
    assessment = models.OneToOneField(Assessment, on_delete=models.CASCADE, related_name='hearing_detail')
    history_of_hearing_loss = models.TextField(blank=True)
    hearing_level_dbhl_left = models.FloatField(null=True, blank=True)
    hearing_level_dbhl_right = models.FloatField(null=True, blank=True)
    monaural_percentage_left = models.FloatField(null=True, blank=True)
    monaural_percentage_right = models.FloatField(null=True, blank=True)
    binaural_percentage = models.FloatField(null=True, blank=True)
    conclusion = models.CharField(max_length=20, choices=CONCLUSION_CHOICES, blank=True)
    recommendations = models.TextField(blank=True)
    assistive_devices = models.TextField(blank=True)

    def __str__(self):
        return f"Hearing Assessment for #{self.assessment_id}"


class PhysicalAssessment(models.Model):
    DISABILITY_TYPE_CHOICES = [
        ('limb_loss', 'Limb Loss'), ('paralysis', 'Paralysis'),
        ('cerebral_palsy', 'Cerebral Palsy'), ('spinal_injury', 'Spinal Cord Injury'),
        ('other', 'Other'),
    ]
    assessment = models.OneToOneField(Assessment, on_delete=models.CASCADE, related_name='physical_detail')
    disability_type = models.CharField(max_length=30, choices=DISABILITY_TYPE_CHOICES, blank=True)
    affected_limbs = models.CharField(max_length=200, blank=True)
    degree_of_disability = models.CharField(max_length=100, blank=True)
    functional_limitation = models.TextField(blank=True)
    conclusion = models.CharField(max_length=20, choices=CONCLUSION_CHOICES, blank=True)
    recommendations = models.TextField(blank=True)
    assistive_devices = models.TextField(blank=True)

    def __str__(self):
        return f"Physical Assessment for #{self.assessment_id}"


class VisualAssessment(models.Model):
    assessment = models.OneToOneField(Assessment, on_delete=models.CASCADE, related_name='visual_detail')
    visual_acuity_right = models.CharField(max_length=20, blank=True)
    visual_acuity_left = models.CharField(max_length=20, blank=True)
    visual_field_right = models.CharField(max_length=50, blank=True)
    visual_field_left = models.CharField(max_length=50, blank=True)
    diagnosis = models.TextField(blank=True)
    conclusion = models.CharField(max_length=20, choices=CONCLUSION_CHOICES, blank=True)
    recommendations = models.TextField(blank=True)
    assistive_devices = models.TextField(blank=True)

    def __str__(self):
        return f"Visual Assessment for #{self.assessment_id}"


class MentalAssessment(models.Model):
    DISORDER_CHOICES = [
        ('intellectual', 'Intellectual Disability'), ('autism', 'Autism Spectrum'),
        ('schizophrenia', 'Schizophrenia'), ('bipolar', 'Bipolar Disorder'),
        ('depression', 'Severe Depression'), ('other', 'Other'),
    ]
    assessment = models.OneToOneField(Assessment, on_delete=models.CASCADE, related_name='mental_detail')
    disorder_type = models.CharField(max_length=30, choices=DISORDER_CHOICES, blank=True)
    history = models.TextField(blank=True)
    current_medication = models.TextField(blank=True)
    iq_score = models.IntegerField(null=True, blank=True)
    functional_assessment = models.TextField(blank=True)
    conclusion = models.CharField(max_length=20, choices=CONCLUSION_CHOICES, blank=True)
    recommendations = models.TextField(blank=True)

    def __str__(self):
        return f"Mental Assessment for #{self.assessment_id}"


class MaxillofacialAssessment(models.Model):
    assessment = models.OneToOneField(Assessment, on_delete=models.CASCADE, related_name='maxillofacial_detail')
    condition = models.CharField(max_length=200, blank=True)
    affected_area = models.CharField(max_length=200, blank=True)
    severity = models.CharField(max_length=50, blank=True)
    surgical_history = models.TextField(blank=True)
    functional_impact = models.TextField(blank=True)
    conclusion = models.CharField(max_length=20, choices=CONCLUSION_CHOICES, blank=True)
    recommendations = models.TextField(blank=True)

    def __str__(self):
        return f"Maxillofacial Assessment for #{self.assessment_id}"


class ChronicAssessment(models.Model):
    CONDITION_CHOICES = [
        ('hiv_aids', 'HIV/AIDS'), ('diabetes', 'Diabetes Complications'),
        ('cancer', 'Cancer'), ('kidney_disease', 'Chronic Kidney Disease'),
        ('heart_disease', 'Chronic Heart Disease'), ('other', 'Other'),
    ]
    assessment = models.OneToOneField(Assessment, on_delete=models.CASCADE, related_name='chronic_detail')
    condition_type = models.CharField(max_length=30, choices=CONDITION_CHOICES, blank=True)
    diagnosis_date = models.DateField(null=True, blank=True)
    current_status = models.CharField(max_length=100, blank=True)
    cd4_count = models.IntegerField(null=True, blank=True)
    viral_load = models.CharField(max_length=50, blank=True)
    current_medication = models.TextField(blank=True)
    functional_limitation = models.TextField(blank=True)
    conclusion = models.CharField(max_length=20, choices=CONCLUSION_CHOICES, blank=True)
    recommendations = models.TextField(blank=True)

    def __str__(self):
        return f"Chronic Assessment for #{self.assessment_id}"
