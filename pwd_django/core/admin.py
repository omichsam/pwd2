from django.contrib import admin
from .models import County, SubCounty, Hospital, Assessment, Document, AuditEvent, Certificate
from .disability_models import (
    HearingAssessment, PhysicalAssessment, VisualAssessment,
    MentalAssessment, MaxillofacialAssessment, ChronicAssessment,
    SpeechAssessment,
)

admin.site.register(County)
admin.site.register(SubCounty)
admin.site.register(Hospital)
admin.site.register(Assessment)
admin.site.register(Document)
admin.site.register(Certificate)
admin.site.register(HearingAssessment)
admin.site.register(PhysicalAssessment)
admin.site.register(VisualAssessment)
admin.site.register(MentalAssessment)
admin.site.register(MaxillofacialAssessment)
admin.site.register(ChronicAssessment)
admin.site.register(SpeechAssessment)


@admin.register(AuditEvent)
class AuditEventAdmin(admin.ModelAdmin):
    list_display = ('created_at', 'action', 'actor_role', 'entity_type', 'entity_id')
    list_filter = ('action', 'actor_role', 'entity_type')
    search_fields = ('event_id', 'entity_id', 'request_id')
    readonly_fields = [field.name for field in AuditEvent._meta.fields]

    def has_add_permission(self, request):
        return False

    def has_change_permission(self, request, obj=None):
        return False

    def has_delete_permission(self, request, obj=None):
        return False
