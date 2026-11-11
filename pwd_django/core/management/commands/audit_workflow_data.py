from django.core.management.base import BaseCommand, CommandError
from django.db.models import Q

from core.models import Assessment


class Command(BaseCommand):
    help = "Report assessment records that are inconsistent with the workflow."

    def add_arguments(self, parser):
        parser.add_argument(
            "--fail-on-error",
            action="store_true",
            help="Exit unsuccessfully when inconsistent records are found.",
        )

    def handle(self, *args, **options):
        checks = {
            "checked without medical officer": Assessment.objects.filter(
                status="checked", medical_officer__isnull=True
            ),
            "health-approved without required reviewers": Assessment.objects.filter(
                status="approved_by_health_officer"
            ).filter(Q(medical_officer__isnull=True) | Q(health_officer__isnull=True)),
            "certified without required reviewers": Assessment.objects.filter(
                status="approved_by_county_officer"
            ).filter(
                Q(medical_officer__isnull=True)
                | Q(health_officer__isnull=True)
                | Q(county_officer__isnull=True)
            ),
            "rejected without a reason": Assessment.objects.filter(
                status="rejected", rejection_reason=""
            ),
        }

        total = 0
        for label, queryset in checks.items():
            ids = list(queryset.values_list("pk", flat=True))
            total += len(ids)
            self.stdout.write(f"{label}: {len(ids)}" + (f" (IDs: {ids})" if ids else ""))

        if total:
            message = f"Found {total} workflow inconsistency result(s)."
            if options["fail_on_error"]:
                raise CommandError(message)
            self.stdout.write(self.style.WARNING(message))
        else:
            self.stdout.write(self.style.SUCCESS("No workflow inconsistencies found."))
