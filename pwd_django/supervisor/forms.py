from django import forms
from core.models import Hospital


class HospitalForm(forms.ModelForm):
    class Meta:
        model = Hospital
        fields = ['name', 'subcounty', 'address', 'phone', 'email']
        widgets = {
            f: forms.TextInput(attrs={'class': 'w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm'})
            for f in ['name', 'subcounty', 'address', 'phone', 'email']
        }
