from django.db import migrations, models


ACTIVE_STATUSES = ['pending', 'checked', 'approved_by_health_officer']


def set_active_slots(apps, schema_editor):
    Assessment = apps.get_model('core', 'Assessment')
    Assessment.objects.exclude(status__in=ACTIVE_STATUSES).update(active_slot=None)


class Migration(migrations.Migration):
    dependencies = [('core', '0015_integrity_indexes')]
    operations = [
        migrations.AddField(
            model_name='assessment', name='active_slot',
            field=models.PositiveSmallIntegerField(default=1, editable=False, null=True),
        ),
        migrations.RunPython(set_active_slots, migrations.RunPython.noop),
        migrations.AddConstraint(
            model_name='assessment',
            constraint=models.UniqueConstraint(
                fields=('user', 'active_slot'),
                name='one_active_assessment_per_user',
            ),
        ),
    ]
