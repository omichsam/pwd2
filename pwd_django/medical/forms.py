from django import forms
from core.disability_models import (
    HearingAssessment, PhysicalAssessment, VisualAssessment,
    MentalAssessment, MaxillofacialAssessment, ChronicAssessment
)


class HearingForm(forms.ModelForm):
    class Meta:
        model = HearingAssessment
        exclude = ['assessment']
        widgets = {
            'history_of_hearing_loss': forms.Textarea(attrs={'rows': 3}),
            'recommendations': forms.Textarea(attrs={'rows': 3}),
            'assistive_devices': forms.Textarea(attrs={'rows': 2}),
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
