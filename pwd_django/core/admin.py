from django.contrib import admin
from .models import County, SubCounty, Hospital, Assessment, Document
from .disability_models import (
    HearingAssessment, PhysicalAssessment, VisualAssessment,
    MentalAssessment, MaxillofacialAssessment, ChronicAssessment
)

admin.site.register(County)
admin.site.register(SubCounty)
admin.site.register(Hospital)
admin.site.register(Assessment)
admin.site.register(Document)
admin.site.register(HearingAssessment)
admin.site.register(PhysicalAssessment)
admin.site.register(VisualAssessment)
admin.site.register(MentalAssessment)
admin.site.register(MaxillofacialAssessment)
admin.site.register(ChronicAssessment)
