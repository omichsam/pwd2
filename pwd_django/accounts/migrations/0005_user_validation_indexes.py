from django.db import migrations, models
import django.core.validators


class Migration(migrations.Migration):
    dependencies = [('accounts', '0004_customuser_designation')]
    operations = [
        migrations.AlterField(
            model_name='customuser', name='mobile_number',
            field=models.CharField(blank=True, max_length=15, validators=[django.core.validators.RegexValidator(message='Enter a valid phone number containing 7 to 15 digits.', regex='^\\+?[0-9][0-9 -]{6,14}$')]),
        ),
        migrations.AlterField(
            model_name='customuser', name='next_of_kin_mobile',
            field=models.CharField(blank=True, max_length=15, validators=[django.core.validators.RegexValidator(message='Enter a valid phone number containing 7 to 15 digits.', regex='^\\+?[0-9][0-9 -]{6,14}$')]),
        ),
        migrations.AlterField(
            model_name='customuser', name='id_number',
            field=models.CharField(blank=True, max_length=20, null=True, unique=True, validators=[django.core.validators.RegexValidator(message='Use only letters, numbers, spaces, hyphens, and slashes.', regex='^[A-Za-z0-9][A-Za-z0-9 /-]*$')]),
        ),
        migrations.AlterField(
            model_name='customuser', name='license_id',
            field=models.CharField(blank=True, max_length=50, null=True, unique=True, validators=[django.core.validators.RegexValidator(message='Use only letters, numbers, spaces, hyphens, and slashes.', regex='^[A-Za-z0-9][A-Za-z0-9 /-]*$')]),
        ),
        migrations.AddIndex(model_name='customuser', index=models.Index(fields=['role', 'is_approved'], name='user_role_active_idx')),
        migrations.AddIndex(model_name='customuser', index=models.Index(fields=['county', 'role'], name='user_county_role_idx')),
        migrations.AddIndex(model_name='customuser', index=models.Index(fields=['hospital', 'role'], name='user_hospital_role_idx')),
    ]
