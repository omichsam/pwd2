from django.conf import settings
from django.db import migrations, models
import django.core.validators
import django.db.models.deletion


class Migration(migrations.Migration):
    dependencies = [
        migrations.swappable_dependency(settings.AUTH_USER_MODEL),
        ('core', '0014_certificate'),
    ]
    operations = [
        migrations.AlterField(
            model_name='assessment', name='user',
            field=models.ForeignKey(on_delete=django.db.models.deletion.PROTECT, related_name='assessments', to=settings.AUTH_USER_MODEL),
        ),
        migrations.AlterField(
            model_name='hearingassessment', name='monaural_percentage_left',
            field=models.FloatField(blank=True, null=True, validators=[django.core.validators.MinValueValidator(0), django.core.validators.MaxValueValidator(100)]),
        ),
        migrations.AlterField(
            model_name='hearingassessment', name='monaural_percentage_right',
            field=models.FloatField(blank=True, null=True, validators=[django.core.validators.MinValueValidator(0), django.core.validators.MaxValueValidator(100)]),
        ),
        migrations.AlterField(
            model_name='hearingassessment', name='binaural_percentage',
            field=models.FloatField(blank=True, null=True, validators=[django.core.validators.MinValueValidator(0), django.core.validators.MaxValueValidator(100)]),
        ),
        migrations.AddIndex(model_name='assessment', index=models.Index(fields=['status', 'created_at'], name='assessment_status_created_idx')),
        migrations.AddIndex(model_name='assessment', index=models.Index(fields=['hospital', 'status'], name='assessment_hospital_status_idx')),
        migrations.AddIndex(model_name='assessment', index=models.Index(fields=['user', 'status'], name='assessment_user_status_idx')),
        migrations.AddIndex(model_name='assessment', index=models.Index(fields=['assessment_date'], name='assessment_date_idx')),
        migrations.AddConstraint(
            model_name='hearingassessment',
            constraint=models.CheckConstraint(check=models.Q(monaural_percentage_left__isnull=True) | models.Q(monaural_percentage_left__gte=0, monaural_percentage_left__lte=100), name='hearing_left_percentage_range'),
        ),
        migrations.AddConstraint(
            model_name='hearingassessment',
            constraint=models.CheckConstraint(check=models.Q(monaural_percentage_right__isnull=True) | models.Q(monaural_percentage_right__gte=0, monaural_percentage_right__lte=100), name='hearing_right_percentage_range'),
        ),
        migrations.AddConstraint(
            model_name='hearingassessment',
            constraint=models.CheckConstraint(check=models.Q(binaural_percentage__isnull=True) | models.Q(binaural_percentage__gte=0, binaural_percentage__lte=100), name='hearing_binaural_percentage_range'),
        ),
    ]
