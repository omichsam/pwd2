from django import forms
from core.disability_models import (
    HearingAssessment, PhysicalAssessment, VisualAssessment,
    MentalAssessment, MaxillofacialAssessment, ChronicAssessment,
    SpeechAssessment,
)


class HearingForm(forms.ModelForm):
    class Meta:
        model = HearingAssessment
        exclude = ['assessment']
        widgets = {
            'history_of_hearing_loss': forms.Textarea(attrs={'rows': 3}),
            'history_of_hearing_devices': forms.Textarea(attrs={'rows': 3}),
            'assistive_devices': forms.Textarea(attrs={'rows': 3}),
            'recommendations': forms.Textarea(attrs={'rows': 3}),
        }


class PhysicalForm(forms.ModelForm):
    class Meta:
        model = PhysicalAssessment
        exclude = ['assessment']
        widgets = {
            'functional_limitation': forms.Textarea(attrs={'rows': 3}),
            'recommendations': forms.Textarea(attrs={'rows': 3}),
            'assistive_devices': forms.Textarea(attrs={'rows': 2}),
        }


class VisualForm(forms.ModelForm):
    class Meta:
        model = VisualAssessment
        exclude = ['assessment']
        widgets = {
            'diagnosis': forms.Textarea(attrs={'rows': 3}),
            'recommendations': forms.Textarea(attrs={'rows': 3}),
            'assistive_devices': forms.Textarea(attrs={'rows': 2}),
        }


class MentalForm(forms.ModelForm):
    class Meta:
        model = MentalAssessment
        exclude = ['assessment']
        widgets = {
            'history': forms.Textarea(attrs={'rows': 3}),
            'current_medication': forms.Textarea(attrs={'rows': 2}),
            'functional_assessment': forms.Textarea(attrs={'rows': 3}),
            'recommendations': forms.Textarea(attrs={'rows': 3}),
        }


class MaxillofacialForm(forms.ModelForm):
    class Meta:
        model = MaxillofacialAssessment
        exclude = ['assessment']
        widgets = {
            'surgical_history': forms.Textarea(attrs={'rows': 3}),
            'functional_impact': forms.Textarea(attrs={'rows': 3}),
            'recommendations': forms.Textarea(attrs={'rows': 3}),
        }


class ChronicForm(forms.ModelForm):
    class Meta:
        model = ChronicAssessment
        exclude = ['assessment']
        widgets = {
            'diagnosis_date': forms.DateInput(attrs={'type': 'date'}),
            'current_medication': forms.Textarea(attrs={'rows': 2}),
            'functional_limitation': forms.Textarea(attrs={'rows': 3}),
            'recommendations': forms.Textarea(attrs={'rows': 3}),
        }


class SpeechForm(forms.ModelForm):
    class Meta:
        model = SpeechAssessment
        exclude = ['assessment']
        widgets = {
            'reason_for_referral': forms.Textarea(attrs={'rows': 2}),
            'medical_diagnosis': forms.Textarea(attrs={'rows': 2}),
            'history_of_condition': forms.Textarea(attrs={'rows': 4}),
            'other_developmental': forms.Textarea(attrs={'rows': 2}),
            'speech_language_milestones': forms.Textarea(attrs={'rows': 2}),
            'receptive_attention_memory': forms.Textarea(attrs={'rows': 2}),
            'receptive_listening_auditory': forms.Textarea(attrs={'rows': 2}),
            'receptive_syntactic_comprehension': forms.Textarea(attrs={'rows': 2}),
            'receptive_semantic_comprehension': forms.Textarea(attrs={'rows': 2}),
            'receptive_reading_comprehension': forms.Textarea(attrs={'rows': 2}),
            'expressive_sound_word_sentence': forms.Textarea(attrs={'rows': 2}),
            'expressive_nonverbal_communication': forms.Textarea(attrs={'rows': 2}),
            'expressive_pragmatics': forms.Textarea(attrs={'rows': 2}),
            'expressive_play_work': forms.Textarea(attrs={'rows': 2}),
            'expressive_written_output': forms.Textarea(attrs={'rows': 2}),
            'fluency_stuttering': forms.Textarea(attrs={'rows': 2}),
            'fluency_cluttering': forms.Textarea(attrs={'rows': 2}),
            'voice_aphonia': forms.Textarea(attrs={'rows': 2}),
            'voice_dysphonia': forms.Textarea(attrs={'rows': 2}),
            'articulation_speech_sound_disorder': forms.Textarea(attrs={'rows': 2}),
            'articulation_motor_speech_disorder': forms.Textarea(attrs={'rows': 2}),
            'standardised_test_results': forms.Textarea(attrs={'rows': 4}),
            'slt_diagnosis': forms.Textarea(attrs={'rows': 3}),
            'impact_on_roles': forms.Textarea(attrs={'rows': 3}),
            'impact_on_career': forms.Textarea(attrs={'rows': 3}),
            'recommendations_notes': forms.Textarea(attrs={'rows': 3}),
            'cause_of_disability': forms.Textarea(attrs={'rows': 2}),
            'injury_onset': forms.DateInput(attrs={'type': 'date'}),
            'last_intervention': forms.DateInput(attrs={'type': 'date'}),
            'recommended_assistive_products': forms.Textarea(attrs={'rows': 2}),
            'other_required_services': forms.Textarea(attrs={'rows': 2}),
        }
