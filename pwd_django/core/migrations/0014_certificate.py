from django.conf import settings
from django.db import migrations, models
import django.db.models.deletion
import uuid


def backfill_complete_certificates(apps, schema_editor):
    Assessment = apps.get_model('core', 'Assessment')
    Certificate = apps.get_model('core', 'Certificate')
    complete = Assessment.objects.filter(
        status='approved_by_county_officer', medical_officer__isnull=False,
        health_officer__isnull=False, county_officer__isnull=False,
    )
    for assessment in complete.iterator():
        Certificate.objects.create(
            assessment_id=assessment.pk,
            certificate_number=f'PWD-LEGACY-{uuid.uuid4().hex[:12].upper()}',
            verification_token=uuid.uuid4(), issued_by_id=assessment.county_officer_id,
        )


class Migration(migrations.Migration):
    dependencies = [
        migrations.swappable_dependency(settings.AUTH_USER_MODEL),
        ('core', '0013_auditevent'),
    ]
    operations = [
        migrations.CreateModel(
            name='Certificate',
            fields=[
                ('id', models.BigAutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('certificate_number', models.CharField(editable=False, max_length=32, unique=True)),
                ('verification_token', models.UUIDField(default=uuid.uuid4, editable=False, unique=True)),
                ('issued_at', models.DateTimeField(auto_now_add=True)),
                ('revoked_at', models.DateTimeField(blank=True, null=True)),
                ('revocation_reason', models.TextField(blank=True)),
                ('assessment', models.ForeignKey(on_delete=django.db.models.deletion.PROTECT, related_name='certificates', to='core.assessment')),
                ('issued_by', models.ForeignKey(blank=True, null=True, on_delete=django.db.models.deletion.SET_NULL, related_name='issued_certificates', to=settings.AUTH_USER_MODEL)),
                ('revoked_by', models.ForeignKey(blank=True, null=True, on_delete=django.db.models.deletion.SET_NULL, related_name='revoked_certificates', to=settings.AUTH_USER_MODEL)),
                ('supersedes', models.OneToOneField(blank=True, null=True, on_delete=django.db.models.deletion.SET_NULL, related_name='replacement', to='core.certificate')),
            ], options={'ordering': ['-issued_at']},
        ),
        migrations.AddConstraint(
            model_name='certificate',
            constraint=models.UniqueConstraint(condition=models.Q(revoked_at__isnull=True), fields=('assessment',), name='one_active_certificate_per_assessment'),
        ),
        migrations.RunPython(backfill_complete_certificates, migrations.RunPython.noop),
    ]
