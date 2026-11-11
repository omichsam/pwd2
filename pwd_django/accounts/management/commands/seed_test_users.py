"""Create one test account per role, all sharing a known password.

    python manage.py seed_test_users
    python manage.py seed_test_users --password "My-Test-Pass-1"

For local development and QA only. The command is idempotent: it updates the
test accounts in place and resets their password on every run. It refuses to run
when DEBUG is off unless --force is given, so test logins can't be created in
production by accident.
"""

from django.conf import settings
from django.core.management.base import BaseCommand, CommandError
from django.db import transaction

from accounts.models import CustomUser
from core.models import County, Hospital

DEFAULT_PASSWORD = 'Test@1234'

# role, login id (id_number for pwd / license_id for officials), first, last
TEST_USERS = [
    ('pwd',             '99999999',   'Test',     'Citizen'),
    ('medical_officer', 'TEST-MED-01', 'Test',    'Medical Officer'),
    ('health_officer',  'TEST-HLT-01', 'Test',    'Health Officer'),
    ('county_officer',  'TEST-CO-01',  'Test',    'County Officer'),
    ('national_admin',  'TEST-NAT-01', 'Test',    'National Admin'),
]


class Command(BaseCommand):
    help = 'Create/refresh one test login per role with a known password (dev/QA only).'

    def add_arguments(self, parser):
        parser.add_argument('--password', default=DEFAULT_PASSWORD,
                            help=f'Password for every test account (default: {DEFAULT_PASSWORD}).')
        parser.add_argument('--force', action='store_true',
                            help='Allow running even when DEBUG is False.')

    @transaction.atomic
    def handle(self, *args, password, force, **options):
        if not settings.DEBUG and not force:
            raise CommandError('DEBUG is False. Refusing to create test accounts without --force.')

        county = County.objects.order_by('id').first()
        hospital = Hospital.objects.order_by('id').first()
        if hospital is None or county is None:
            raise CommandError('No counties/hospitals found. Run `manage.py seed_data` first.')

        self.stdout.write('Creating test accounts...\n')
        rows = []
        for role, login, first, last in TEST_USERS:
            is_pwd = role == 'pwd'
            username = login
            defaults = dict(
                role=role, first_name=first, last_name=last,
                email=f'{login.lower().replace("-", ".")}@example.test',
                is_approved=True, is_active=True,
                county=county,
                id_number=login if is_pwd else None,
                license_id=None if is_pwd else login,
                hospital=None if is_pwd or role in ('county_officer', 'national_admin') else hospital,
            )
            user, created = CustomUser.objects.update_or_create(username=username, defaults=defaults)
            user.set_password(password)
            user.save(update_fields=['password'])
            rows.append((role, login, 'created' if created else 'updated'))

        label = {'pwd': 'PWD citizen (login with ID number)'}
        width = max(len(r) for r, *_ in rows)
        for role, login, state in rows:
            self.stdout.write(f'  {role.ljust(width)}  login={login:<12} ({state})')

        self.stdout.write(self.style.SUCCESS(
            f'\nDone. All {len(rows)} test accounts use the password: {password}'))
        self.stdout.write(
            'PWD citizens log in with the ID number; officials log in with the license ID.')
