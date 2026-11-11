from django import forms

from core.models import ChatbotSettings


class ChatbotSettingsForm(forms.ModelForm):
    """Site-wide PWD Assistant settings.

    The API key is write-only: the field always renders empty, a blank
    submission keeps the saved key, and `clear_api_key` removes it.
    """

    docassist_api_key = forms.CharField(
        required=False, max_length=200, strip=True,
        widget=forms.PasswordInput(render_value=False, attrs={'autocomplete': 'new-password'}),
    )
    clear_api_key = forms.BooleanField(required=False)

    class Meta:
        model = ChatbotSettings
        fields = [
            'docassist_enabled', 'docassist_base_url', 'docassist_api_key',
            'docassist_timeout_seconds', 'max_per_window', 'window_seconds',
        ]

    def __init__(self, *args, saved_api_key='', **kwargs):
        super().__init__(*args, **kwargs)
        self.saved_api_key = saved_api_key
        self.fields['docassist_base_url'].required = True

    def clean_docassist_timeout_seconds(self):
        value = self.cleaned_data['docassist_timeout_seconds']
        if not 1 <= value <= 60:
            raise forms.ValidationError('Use a timeout between 1 and 60 seconds.')
        return value

    def clean_max_per_window(self):
        value = self.cleaned_data['max_per_window']
        if not 1 <= value <= 1000:
            raise forms.ValidationError('Use a limit between 1 and 1000 questions.')
        return value

    def clean_window_seconds(self):
        value = self.cleaned_data['window_seconds']
        if not 10 <= value <= 86400:
            raise forms.ValidationError('Use a window between 10 seconds and 24 hours.')
        return value

    def clean_docassist_api_key(self):
        key = self.cleaned_data.get('docassist_api_key', '')
        if any(c.isspace() for c in key):
            raise forms.ValidationError('The API key cannot contain spaces.')
        return key

    def effective_api_key(self):
        """The key the form will store: a new key, none (cleared), or the saved one."""
        if self.cleaned_data.get('clear_api_key'):
            return ''
        return self.cleaned_data.get('docassist_api_key') or self.saved_api_key

    def save(self, commit=True):
        instance = super().save(commit=False)
        instance.docassist_api_key = self.effective_api_key()
        if commit:
            instance.save()
        return instance
