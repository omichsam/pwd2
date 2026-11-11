from django import forms

from core.models import DocumentCheckerSettings


class DocumentCheckerSettingsForm(forms.ModelForm):
    """Site-wide Document Checker API settings.

    The API key is write-only: the field renders empty, a blank submission keeps
    the saved key, and `clear_api_key` removes it.
    """

    api_key = forms.CharField(
        required=False, max_length=200, strip=True,
        widget=forms.PasswordInput(render_value=False, attrs={'autocomplete': 'new-password'}),
    )
    clear_api_key = forms.BooleanField(required=False)

    class Meta:
        model = DocumentCheckerSettings
        fields = ['enabled', 'base_url', 'api_key', 'timeout_seconds']

    def __init__(self, *args, saved_api_key='', **kwargs):
        super().__init__(*args, **kwargs)
        self.saved_api_key = saved_api_key
        self.fields['base_url'].required = True

    def clean_timeout_seconds(self):
        value = self.cleaned_data['timeout_seconds']
        if not 1 <= value <= 120:
            raise forms.ValidationError('Use a timeout between 1 and 120 seconds.')
        return value

    def clean_api_key(self):
        key = self.cleaned_data.get('api_key', '')
        if any(c.isspace() for c in key):
            raise forms.ValidationError('The API key cannot contain spaces.')
        return key

    def effective_api_key(self):
        if self.cleaned_data.get('clear_api_key'):
            return ''
        return self.cleaned_data.get('api_key') or self.saved_api_key

    def save(self, commit=True):
        instance = super().save(commit=False)
        instance.api_key = self.effective_api_key()
        if commit:
            instance.save()
        return instance
