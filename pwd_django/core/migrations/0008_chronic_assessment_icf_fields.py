from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('core', '0007_maxillofacial_new_fields'),
    ]

    operations = [
        migrations.AddField(model_name='chronicassessment', name='injury_onset', field=models.DateField(blank=True, null=True)),
        migrations.AddField(model_name='chronicassessment', name='last_intervention', field=models.DateField(blank=True, null=True)),
        migrations.AddField(model_name='chronicassessment', name='past_interventions', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='cause_of_disability', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='structural_impairments', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='regions_affected', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='cardiopulmonary_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='cardiopulmonary_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='respiratory_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='respiratory_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='malignancies_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='malignancies_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='musculoskeletal_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='musculoskeletal_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='neurological_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='neurological_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='gastrointestinal_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='gastrointestinal_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='dermatological_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='dermatological_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='hematologic_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='hematologic_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='vascular_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='vascular_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='genitourinary_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='genitourinary_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='frailty_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='frailty_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='other_clinical_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='other_clinical_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='mobility_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='mobility_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='selfcare_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='selfcare_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='domestic_life_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='domestic_life_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='major_life_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='major_life_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='community_level', field=models.CharField(blank=True, max_length=5)),
        migrations.AddField(model_name='chronicassessment', name='community_findings', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='disability_rating', field=models.CharField(blank=True, max_length=20)),
        migrations.AddField(model_name='chronicassessment', name='recommended_assistive_products', field=models.TextField(blank=True)),
        migrations.AddField(model_name='chronicassessment', name='other_services_required', field=models.TextField(blank=True)),
    ]
