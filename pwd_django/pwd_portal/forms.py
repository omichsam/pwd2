from django import forms
from core.models import Assessment, Hospital


class ApplicationForm(forms.ModelForm):
    class Meta:
        model = Assessment
        fields = ['hospital', 'disability_type', 'assessment_date', 'notes']
        widgets = {
            'assessment_date': forms.DateInput(attrs={'type': 'date'}),
            'notes': forms.Textarea(attrs={'rows': 3}),
        }

    def __init__(self, *args, user=None, **kwargs):
        super().__init__(*args, **kwargs)
        if user and user.county:
            self.fields['hospital'].queryset = Hospital.objects.filter(county=user.county)


class ProfileForm(forms.ModelForm):
    from accounts.models import CustomUser

    class Meta:
        from accounts.models import CustomUser
        model = CustomUser
        fields = [
            'first_name', 'last_name', 'email', 'mobile_number',
            'date_of_birth', 'gender', 'county', 'subcounty',
            'occupation', 'marital_status', 'education_level',
            'next_of_kin_name', 'next_of_kin_mobile', 'next_of_kin_relationship',
        ]
        widgets = {
            'date_of_birth': forms.DateInput(attrs={'type': 'date'}),
        }


class ChangePasswordForm(forms.Form):
    current_password = forms.CharField(widget=forms.PasswordInput)
    new_password = forms.CharField(widget=forms.PasswordInput, min_length=6)
    confirm_password = forms.CharField(widget=forms.PasswordInput)

    def clean(self):
        cleaned = super().clean()
        if cleaned.get('new_password') != cleaned.get('confirm_password'):
            raise forms.ValidationError('New passwords do not match.')
        return cleaned
