from django.db import models
from django.core.validators import MaxValueValidator, MinValueValidator
from .models import Assessment

CONCLUSION_CHOICES = [('temporary', 'Temporary'), ('permanent', 'Permanent')]


HEARING_LOSS_TYPE_CHOICES = [
    ('conductive', 'Conductive'), ('sensorineural', 'Sensorineural'),
    ('mixed', 'Mixed'), ('central', 'Central'),
]
HEARING_GRADE_CHOICES = [
    ('normal', 'Normal (0–25 dBHL)'),
    ('slight', 'Slight (26–40 dBHL)'),
    ('moderate', 'Moderate (41–60 dBHL)'),
    ('severe', 'Severe (61–80 dBHL)'),
    ('profound', 'Profound (81+ dBHL)'),
]


class HearingAssessment(models.Model):
    assessment = models.OneToOneField(Assessment, on_delete=models.CASCADE, related_name='hearing_detail')
    # Section 1 – Medical History
    history_of_hearing_loss = models.TextField(blank=True)
    history_of_hearing_devices = models.TextField(blank=True)
    # Section 2 – Hearing Test Results (Left Ear)
    hearing_loss_type_left = models.CharField(max_length=20, choices=HEARING_LOSS_TYPE_CHOICES, blank=True)
    hearing_grade_left = models.CharField(max_length=20, choices=HEARING_GRADE_CHOICES, blank=True)
    hearing_level_dbhl_left = models.FloatField(null=True, blank=True)
    monaural_percentage_left = models.FloatField(
        null=True, blank=True, validators=[MinValueValidator(0), MaxValueValidator(100)]
    )
    # Section 2 – Hearing Test Results (Right Ear)
    hearing_loss_type_right = models.CharField(max_length=20, choices=HEARING_LOSS_TYPE_CHOICES, blank=True)
    hearing_grade_right = models.CharField(max_length=20, choices=HEARING_GRADE_CHOICES, blank=True)
    hearing_level_dbhl_right = models.FloatField(null=True, blank=True)
    monaural_percentage_right = models.FloatField(
        null=True, blank=True, validators=[MinValueValidator(0), MaxValueValidator(100)]
    )
    # Section 2 – Combined
    binaural_percentage = models.FloatField(
        null=True, blank=True, validators=[MinValueValidator(0), MaxValueValidator(100)]
    )
    conclusion = models.CharField(max_length=20, choices=CONCLUSION_CHOICES, blank=True)
    # Section 3 – Recommendations
    assistive_devices = models.TextField(blank=True)
    recommendations = models.TextField(blank=True)

    class Meta:
        constraints = [
            models.CheckConstraint(
                check=models.Q(monaural_percentage_left__isnull=True)
                | models.Q(monaural_percentage_left__gte=0, monaural_percentage_left__lte=100),
                name='hearing_left_percentage_range',
            ),
            models.CheckConstraint(
                check=models.Q(monaural_percentage_right__isnull=True)
                | models.Q(monaural_percentage_right__gte=0, monaural_percentage_right__lte=100),
                name='hearing_right_percentage_range',
            ),
            models.CheckConstraint(
                check=models.Q(binaural_percentage__isnull=True)
                | models.Q(binaural_percentage__gte=0, binaural_percentage__lte=100),
                name='hearing_binaural_percentage_range',
            ),
        ]

    def __str__(self):
        return f"Hearing Assessment for #{self.assessment_id}"


IMPAIRMENT_LEVEL_CHOICES = [
    ('', '— Select Impairment Level —'),
    ('0', '0 — No Impairment'),
    ('1', '1 — Mild (< 25%)'),
    ('2', '2 — Moderate (25–49%)'),
    ('3', '3 — Severe (50–95%)'),
    ('4', '4 — Complete (96–100%)'),
]

DIFFICULTY_LEVEL_CHOICES = [
    ('', '— Select Difficulty Level —'),
    ('0', '0 — No Difficulty'),
    ('1', '1 — Mild Difficulty'),
    ('2', '2 — Moderate Difficulty'),
    ('3', '3 — Severe Difficulty'),
    ('4', '4 — Complete Difficulty'),
]

SCORE_CHOICES = [
    ('', '— Select —'),
    ('0', '0 — No Impairment'),
    ('1', '1 — Mild'),
    ('2', '2 — Moderate'),
    ('3', '3 — Severe'),
    ('4', '4 — Complete/Profound'),
]

DISABILITY_RATING_CHOICES = [
    ('', '— Select —'),
    ('mild', 'Mild (< 25%)'),
    ('moderate', 'Moderate (25–49%)'),
    ('severe', 'Severe (50–74%)'),
    ('profound', 'Profound (75–100%)'),
]


class PhysicalAssessment(models.Model):
    assessment = models.OneToOneField(Assessment, on_delete=models.CASCADE, related_name='physical_detail')

    # Step 1 — Medical History
    injury_onset = models.DateField(null=True, blank=True)
    last_intervention = models.DateField(null=True, blank=True)
    cause_of_disability = models.TextField(blank=True)

    # Step 2 — Structural Impairments: regions
    region_s710 = models.BooleanField(default=False)   # Head & Neck
    region_s720 = models.BooleanField(default=False)   # Shoulder
    region_s730 = models.BooleanField(default=False)   # Upper Extremity
    region_s740 = models.BooleanField(default=False)   # Pelvis
    region_s750 = models.BooleanField(default=False)   # Lower Extremity
    region_s760 = models.BooleanField(default=False)   # Trunk
    region_s8   = models.BooleanField(default=False)   # Skin & Related
    regions_affected = models.TextField(blank=True)

    # Step 2 — Structural Assessment Table
    muscle_power_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    muscle_power_findings = models.TextField(blank=True)
    range_of_motion_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    range_of_motion_findings = models.TextField(blank=True)
    angulation_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    angulation_findings = models.TextField(blank=True)
    limb_amputation_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    limb_amputation_findings = models.TextField(blank=True)
    limb_length_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    limb_length_findings = models.TextField(blank=True)
    balance_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    balance_findings = models.TextField(blank=True)
    other_impairment_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    other_impairment_findings = models.TextField(blank=True)
    score_for_impairments = models.CharField(max_length=5, choices=SCORE_CHOICES, blank=True)

    # Step 3 — Functional & Participation Table
    mobility_level    = models.CharField(max_length=5, choices=DIFFICULTY_LEVEL_CHOICES, blank=True)
    mobility_findings = models.TextField(blank=True)
    domestic_life_level    = models.CharField(max_length=5, choices=DIFFICULTY_LEVEL_CHOICES, blank=True)
    domestic_life_findings = models.TextField(blank=True)
    community_level    = models.CharField(max_length=5, choices=DIFFICULTY_LEVEL_CHOICES, blank=True)
    community_findings = models.TextField(blank=True)
    selfcare_level    = models.CharField(max_length=5, choices=DIFFICULTY_LEVEL_CHOICES, blank=True)
    selfcare_findings = models.TextField(blank=True)
    major_life_level    = models.CharField(max_length=5, choices=DIFFICULTY_LEVEL_CHOICES, blank=True)
    major_life_findings = models.TextField(blank=True)
    score_for_participation = models.CharField(max_length=5, choices=SCORE_CHOICES, blank=True)
    disability_rating = models.CharField(max_length=20, choices=DISABILITY_RATING_CHOICES, blank=True)

    # Step 4 — Recommendations & Conclusion
    assistive_devices     = models.TextField(blank=True)
    other_required_services = models.TextField(blank=True)
    conclusion = models.CharField(max_length=20, choices=CONCLUSION_CHOICES, blank=True)
    recommendations = models.TextField(blank=True)

    # legacy fields kept for compatibility
    disability_type    = models.CharField(max_length=30, blank=True)
    affected_limbs     = models.CharField(max_length=200, blank=True)
    degree_of_disability = models.CharField(max_length=100, blank=True)
    functional_limitation = models.TextField(blank=True)

    def __str__(self):
        return f"Physical Assessment for #{self.assessment_id}"


class VisualAssessment(models.Model):
    assessment = models.OneToOneField(Assessment, on_delete=models.CASCADE, related_name='visual_detail')

    # Section 1 – History & Background
    assistive_devices = models.TextField(blank=True)
    medical_history = models.TextField(blank=True)
    ocular_history = models.TextField(blank=True)

    # Section 2 – Distance Visual Acuity
    visual_acuity_right = models.CharField(max_length=20, blank=True)        # with correction
    visual_acuity_right_uncorrected = models.CharField(max_length=20, blank=True)
    visual_acuity_left = models.CharField(max_length=20, blank=True)         # with correction
    visual_acuity_left_uncorrected = models.CharField(max_length=20, blank=True)
    near_vision_corrected = models.CharField(max_length=20, blank=True)
    near_vision_uncorrected = models.CharField(max_length=20, blank=True)

    # Section 2 – Ophthalmic Examination (Right Eye)
    present_eyeball_right = models.CharField(max_length=100, blank=True)
    squint_right = models.CharField(max_length=100, blank=True)
    nystagmus_right = models.CharField(max_length=100, blank=True)
    tearing_right = models.CharField(max_length=100, blank=True)
    lids_right = models.CharField(max_length=100, blank=True)
    conjunctiva_right = models.CharField(max_length=100, blank=True)
    cornea_right = models.CharField(max_length=100, blank=True)
    anterior_chamber_right = models.CharField(max_length=100, blank=True)
    iris_right = models.CharField(max_length=100, blank=True)
    pupil_right = models.CharField(max_length=100, blank=True)
    lens_right = models.CharField(max_length=100, blank=True)
    fundus_right = models.CharField(max_length=100, blank=True)

    # Section 2 – Ophthalmic Examination (Left Eye)
    present_eyeball_left = models.CharField(max_length=100, blank=True)
    squint_left = models.CharField(max_length=100, blank=True)
    nystagmus_left = models.CharField(max_length=100, blank=True)
    tearing_left = models.CharField(max_length=100, blank=True)
    lids_left = models.CharField(max_length=100, blank=True)
    conjunctiva_left = models.CharField(max_length=100, blank=True)
    cornea_left = models.CharField(max_length=100, blank=True)
    anterior_chamber_left = models.CharField(max_length=100, blank=True)
    iris_left = models.CharField(max_length=100, blank=True)
    pupil_left = models.CharField(max_length=100, blank=True)
    lens_left = models.CharField(max_length=100, blank=True)
    fundus_left = models.CharField(max_length=100, blank=True)

    # Section 3 – Specialized Tests
    humphreys_visual_field = models.CharField(max_length=100, blank=True)
    colour_vision = models.CharField(max_length=100, blank=True)
    stereopsis = models.CharField(max_length=100, blank=True)

    # Section 3 – Conclusion
    visual_category = models.CharField(max_length=50, blank=True)
    cause_of_vision_loss = models.CharField(max_length=200, blank=True)
    percentage_disability = models.CharField(max_length=20, blank=True)
    possible_intervention = models.CharField(max_length=100, blank=True)
    recommendations = models.TextField(blank=True)
    conclusion = models.CharField(max_length=20, choices=CONCLUSION_CHOICES, blank=True)

    # legacy fields kept for compat
    visual_field_right = models.CharField(max_length=50, blank=True)
    visual_field_left = models.CharField(max_length=50, blank=True)
    diagnosis = models.TextField(blank=True)

    def __str__(self):
        return f"Visual Assessment for #{self.assessment_id}"


FUNCTIONAL_SCORE_CHOICES = [
    ('', '— Select —'),
    ('0', '0 — Completely Independent'),
    ('1', '1 — Requires Minimal Assistance'),
    ('2', '2 — Requires Moderate Assistance'),
    ('3', '3 — Requires Maximum Assistance'),
    ('4', '4 — Completely Dependent'),
]

EMPLOYABILITY_SCORE_CHOICES = [
    ('', '— Select —'),
    ('0', '0 — No Restriction'),
    ('1', '1 — Mild Restriction'),
    ('2', '2 — Moderate Restriction'),
    ('3', '3 — Severe Restriction'),
    ('4', '4 — Complete Restriction'),
]


class MentalAssessment(models.Model):
    DISORDER_CHOICES = [
        ('intellectual', 'Intellectual Disability'), ('autism', 'Autism Spectrum'),
        ('schizophrenia', 'Schizophrenia'), ('bipolar', 'Bipolar Disorder'),
        ('depression', 'Severe Depression'), ('other', 'Other'),
    ]
    assessment = models.OneToOneField(Assessment, on_delete=models.CASCADE, related_name='mental_detail')

    # Step 1 — Clinical History & Mental Status
    disorder_type = models.CharField(max_length=30, choices=DISORDER_CHOICES, blank=True)
    history = models.TextField(blank=True)
    current_medication = models.TextField(blank=True)
    iq_score = models.IntegerField(null=True, blank=True)
    mental_status_exam = models.TextField(blank=True)

    # Step 2 — Functional Assessment Tool
    feeding_score       = models.CharField(max_length=2, choices=FUNCTIONAL_SCORE_CHOICES, blank=True)
    toileting_score     = models.CharField(max_length=2, choices=FUNCTIONAL_SCORE_CHOICES, blank=True)
    grooming_score      = models.CharField(max_length=2, choices=FUNCTIONAL_SCORE_CHOICES, blank=True)
    physical_disability_score = models.CharField(max_length=2, choices=FUNCTIONAL_SCORE_CHOICES, blank=True)
    employability_score = models.CharField(max_length=2, choices=EMPLOYABILITY_SCORE_CHOICES, blank=True)

    # Step 3 — Summary & Upload
    duration_of_illness      = models.CharField(max_length=100, blank=True)
    major_cause_of_disability = models.CharField(max_length=200, blank=True)
    assistive_devices        = models.TextField(blank=True)
    other_required_services  = models.TextField(blank=True)
    conclusion = models.CharField(max_length=20, choices=CONCLUSION_CHOICES, blank=True)
    recommendations = models.TextField(blank=True)

    # legacy field kept for compatibility
    functional_assessment = models.TextField(blank=True)

    def __str__(self):
        return f"Mental Assessment for #{self.assessment_id}"


class MaxillofacialAssessment(models.Model):
    assessment = models.OneToOneField(Assessment, on_delete=models.CASCADE, related_name='maxillofacial_detail')

    # Step 1 — History
    medical_history = models.TextField(blank=True)
    dental_history  = models.TextField(blank=True)

    # Step 2 — Assessment
    dental_assessment             = models.TextField(blank=True)
    conclusion                    = models.CharField(max_length=20, choices=CONCLUSION_CHOICES, blank=True)
    recommended_assistive_products = models.TextField(blank=True)
    other_services_required        = models.TextField(blank=True)

    # legacy fields kept for compatibility
    condition        = models.CharField(max_length=200, blank=True)
    affected_area    = models.CharField(max_length=200, blank=True)
    severity         = models.CharField(max_length=50, blank=True)
    surgical_history = models.TextField(blank=True)
    functional_impact = models.TextField(blank=True)
    recommendations  = models.TextField(blank=True)

    def __str__(self):
        return f"Maxillofacial Assessment for #{self.assessment_id}"


class ChronicAssessment(models.Model):
    CONDITION_CHOICES = [
        ('hiv_aids', 'HIV/AIDS'), ('diabetes', 'Diabetes Complications'),
        ('cancer', 'Cancer'), ('kidney_disease', 'Chronic Kidney Disease'),
        ('heart_disease', 'Chronic Heart Disease'), ('other', 'Other'),
    ]
    assessment = models.OneToOneField(Assessment, on_delete=models.CASCADE, related_name='chronic_detail')

    # Step 1 — Summary Findings
    injury_onset = models.DateField(null=True, blank=True)
    last_intervention = models.DateField(null=True, blank=True)
    past_interventions = models.TextField(blank=True)
    cause_of_disability = models.TextField(blank=True)

    # Step 2 — Structural / Clinical Assessment
    structural_impairments = models.TextField(blank=True)
    regions_affected = models.TextField(blank=True)
    cardiopulmonary_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    cardiopulmonary_findings = models.TextField(blank=True)
    respiratory_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    respiratory_findings = models.TextField(blank=True)
    malignancies_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    malignancies_findings = models.TextField(blank=True)
    musculoskeletal_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    musculoskeletal_findings = models.TextField(blank=True)
    neurological_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    neurological_findings = models.TextField(blank=True)
    gastrointestinal_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    gastrointestinal_findings = models.TextField(blank=True)
    dermatological_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    dermatological_findings = models.TextField(blank=True)
    hematologic_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    hematologic_findings = models.TextField(blank=True)
    vascular_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    vascular_findings = models.TextField(blank=True)
    genitourinary_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    genitourinary_findings = models.TextField(blank=True)
    frailty_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    frailty_findings = models.TextField(blank=True)
    other_clinical_level    = models.CharField(max_length=5, choices=IMPAIRMENT_LEVEL_CHOICES, blank=True)
    other_clinical_findings = models.TextField(blank=True)

    # Step 3 — Functional / Participation Restrictions
    mobility_level    = models.CharField(max_length=5, choices=DIFFICULTY_LEVEL_CHOICES, blank=True)
    mobility_findings = models.TextField(blank=True)
    selfcare_level    = models.CharField(max_length=5, choices=DIFFICULTY_LEVEL_CHOICES, blank=True)
    selfcare_findings = models.TextField(blank=True)
    domestic_life_level    = models.CharField(max_length=5, choices=DIFFICULTY_LEVEL_CHOICES, blank=True)
    domestic_life_findings = models.TextField(blank=True)
    major_life_level    = models.CharField(max_length=5, choices=DIFFICULTY_LEVEL_CHOICES, blank=True)
    major_life_findings = models.TextField(blank=True)
    community_level    = models.CharField(max_length=5, choices=DIFFICULTY_LEVEL_CHOICES, blank=True)
    community_findings = models.TextField(blank=True)
    disability_rating = models.CharField(max_length=20, choices=DISABILITY_RATING_CHOICES, blank=True)
    recommended_assistive_products = models.TextField(blank=True)
    other_services_required = models.TextField(blank=True)
    conclusion = models.CharField(max_length=20, choices=CONCLUSION_CHOICES, blank=True)
    recommendations = models.TextField(blank=True)

    # legacy fields kept for compatibility
    condition_type = models.CharField(max_length=30, choices=CONDITION_CHOICES, blank=True)
    diagnosis_date = models.DateField(null=True, blank=True)
    current_status = models.CharField(max_length=100, blank=True)
    cd4_count = models.IntegerField(null=True, blank=True)
    viral_load = models.CharField(max_length=50, blank=True)
    current_medication = models.TextField(blank=True)
    functional_limitation = models.TextField(blank=True)

    def __str__(self):
        return f"Chronic Assessment for #{self.assessment_id}"


SEVERITY_CHOICES = [
    ('', '— Select Severity —'),
    ('mild', 'Mild'),
    ('moderate', 'Moderate'),
    ('severe', 'Severe'),
    ('profound', 'Profound'),
]

CONDITION_TYPE_CHOICES = [
    ('', '— Select —'),
    ('acute', 'Acute'),
    ('chronic', 'Chronic'),
]


class SpeechAssessment(models.Model):
    assessment = models.OneToOneField(Assessment, on_delete=models.CASCADE, related_name='speech_detail')

    # Referral
    referred_by = models.CharField(max_length=200, blank=True)
    reason_for_referral = models.TextField(blank=True)
    medical_diagnosis = models.TextField(blank=True)

    # History
    history_of_condition = models.TextField(blank=True)

    # Domains
    domain_language = models.BooleanField(default=False)
    domain_speech = models.BooleanField(default=False)
    domain_dysphagia = models.BooleanField(default=False)
    domain_communication = models.BooleanField(default=False)

    # Developmental disorders
    delay_motor_milestones = models.BooleanField(null=True, blank=True)
    sensory_visual_impairment = models.BooleanField(default=False)
    sensory_auditory_impairment = models.BooleanField(default=False)
    other_developmental = models.TextField(blank=True)
    speech_language_milestones = models.TextField(blank=True)

    # Pre-linguistic skills
    prelinguistic_eye_contact = models.BooleanField(null=True, blank=True)
    prelinguistic_attention_span = models.BooleanField(null=True, blank=True)
    prelinguistic_imitation_skills = models.BooleanField(null=True, blank=True)

    # A(i). Receptive Language
    receptive_attention_memory = models.TextField(blank=True)
    receptive_listening_auditory = models.TextField(blank=True)
    receptive_syntactic_comprehension = models.TextField(blank=True)
    receptive_semantic_comprehension = models.TextField(blank=True)
    receptive_reading_comprehension = models.TextField(blank=True)

    # A(ii). Expressive Language
    expressive_sound_word_sentence = models.TextField(blank=True)
    expressive_nonverbal_communication = models.TextField(blank=True)
    expressive_pragmatics = models.TextField(blank=True)
    expressive_play_work = models.TextField(blank=True)
    expressive_written_output = models.TextField(blank=True)

    # B. Speech Impairments
    fluency_stuttering = models.TextField(blank=True)
    fluency_cluttering = models.TextField(blank=True)
    voice_aphonia = models.TextField(blank=True)
    voice_dysphonia = models.TextField(blank=True)
    articulation_speech_sound_disorder = models.TextField(blank=True)
    articulation_motor_speech_disorder = models.TextField(blank=True)
    standardised_test_results = models.TextField(blank=True)

    # C. Dysphagia — Red Flag symptoms
    dysphagia_recurrent_chest_infections = models.BooleanField(default=False)
    dysphagia_current_chest_infection = models.BooleanField(default=False)
    dysphagia_dehydration_malnutrition = models.BooleanField(default=False)
    dysphagia_weight_loss = models.BooleanField(default=False)
    dysphagia_long_eating_time = models.BooleanField(default=False)
    dysphagia_food_avoidance = models.BooleanField(default=False)
    dysphagia_social_avoidance = models.BooleanField(default=False)
    dysphagia_distress_eating = models.BooleanField(default=False)

    # C. Dysphagia — Pre-oral stage
    dysphagia_difficulty_self_feeding = models.BooleanField(default=False)
    dysphagia_difficulty_mouth_cleaning = models.BooleanField(default=False)

    # C. Dysphagia — Oral stage
    dysphagia_difficulty_closing_lips = models.BooleanField(default=False)
    dysphagia_difficulty_taking_food_off_spoon = models.BooleanField(default=False)
    dysphagia_oral_escape = models.BooleanField(default=False)
    dysphagia_restricted_oral_movements = models.BooleanField(default=False)
    dysphagia_food_residue = models.BooleanField(default=False)
    dysphagia_difficulty_managing_saliva = models.BooleanField(default=False)

    # C. Dysphagia — Pharyngeal stage
    dysphagia_eye_symptoms = models.BooleanField(default=False)
    dysphagia_coughing_throat_clearing = models.BooleanField(default=False)
    dysphagia_colour_breath_changes = models.BooleanField(default=False)
    dysphagia_nasal_oral_regurgitation = models.BooleanField(default=False)
    dysphagia_wet_gurgly_voice = models.BooleanField(default=False)

    # C. Dysphagia — Esophageal stage
    dysphagia_reflux = models.BooleanField(default=False)
    dysphagia_coughing_after_eating = models.BooleanField(default=False)
    dysphagia_coughing_lying_down = models.BooleanField(default=False)
    dysphagia_breathing_difficulties = models.BooleanField(default=False)

    # Conclusion
    slt_diagnosis = models.TextField(blank=True)
    severity = models.CharField(max_length=20, choices=SEVERITY_CHOICES, blank=True)
    impact_on_roles = models.TextField(blank=True)
    impact_on_career = models.TextField(blank=True)

    # Recommendations
    recommend_further_management = models.BooleanField(default=False)
    recommend_referral_professionals = models.BooleanField(default=False)
    recommend_communication_aids = models.BooleanField(default=False)
    recommendations_notes = models.TextField(blank=True)

    # Cause & dates
    cause_of_disability = models.TextField(blank=True)
    injury_onset = models.DateField(null=True, blank=True)
    condition_type = models.CharField(max_length=10, choices=CONDITION_TYPE_CHOICES, blank=True)
    last_intervention = models.DateField(null=True, blank=True)

    # Final services
    recommended_assistive_products = models.TextField(blank=True)
    other_required_services = models.TextField(blank=True)

    def __str__(self):
        return f"Speech Assessment for #{self.assessment_id}"
