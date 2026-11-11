from django import forms
from django.contrib.auth.password_validation import validate_password
from .models import CustomUser


class PWDRegistrationForm(forms.ModelForm):
    password = forms.CharField(widget=forms.PasswordInput)
    confirm_password = forms.CharField(widget=forms.PasswordInput)

    class Meta:
        model = CustomUser
        fields = [
            'first_name', 'last_name', 'email', 'id_number', 'mobile_number',
            'date_of_birth', 'gender', 'county', 'subcounty', 'occupation',
            'marital_status', 'education_level',
            'next_of_kin_name', 'next_of_kin_mobile', 'next_of_kin_relationship',
        ]

    def clean(self):
        cleaned = super().clean()
        password = cleaned.get('password')
        if password != cleaned.get('confirm_password'):
            raise forms.ValidationError('Passwords do not match.')
        if password:
            validate_password(password, self.instance)
        return cleaned

    def save(self, commit=True):
        user = super().save(commit=False)
        # Use id_number as username
        user.username = self.cleaned_data['id_number']
        if commit:
            user.save()
        return user


class OfficialRegistrationForm(forms.ModelForm):
    ROLE_CHOICES = [
        ('medical_officer', 'Medical Officer'),
        ('health_officer', 'Health Officer'),
        ('county_officer', 'County Officer'),
    ]
    role = forms.ChoiceField(choices=ROLE_CHOICES)
    password = forms.CharField(widget=forms.PasswordInput)
    confirm_password = forms.CharField(widget=forms.PasswordInput)

    class Meta:
        model = CustomUser
        fields = [
            'first_name', 'last_name', 'email', 'license_id', 'mobile_number',
            'county', 'hospital',
        ]

    def clean(self):
        cleaned = super().clean()
        password = cleaned.get('password')
        if password != cleaned.get('confirm_password'):
            raise forms.ValidationError('Passwords do not match.')
        if password:
            validate_password(password, self.instance)
        return cleaned

    def save(self, commit=True):
        user = super().save(commit=False)
        user.username = self.cleaned_data['license_id']
        if commit:
            user.save()
        return user


class ProfileUpdateForm(forms.ModelForm):
    class Meta:
        model = CustomUser
        fields = [
            'first_name', 'last_name', 'email', 'mobile_number',
            'date_of_birth', 'gender', 'county', 'subcounty',
            'occupation', 'marital_status', 'education_level',
            'next_of_kin_name', 'next_of_kin_mobile', 'next_of_kin_relationship',
        ]


class ChangePasswordForm(forms.Form):
    current_password = forms.CharField(widget=forms.PasswordInput)
    new_password = forms.CharField(widget=forms.PasswordInput)
    confirm_password = forms.CharField(widget=forms.PasswordInput)

    def __init__(self, *args, user=None, **kwargs):
        super().__init__(*args, **kwargs)
        self.user = user

    def clean_current_password(self):
        current = self.cleaned_data['current_password']
        if not self.user or not self.user.check_password(current):
            raise forms.ValidationError('Current password is incorrect.')
        return current

    def clean(self):
        cleaned = super().clean()
        new_password = cleaned.get('new_password')
        if new_password and new_password != cleaned.get('confirm_password'):
            raise forms.ValidationError('New passwords do not match.')
        if new_password:
            validate_password(new_password, self.user)
        return cleaned
