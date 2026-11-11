"""Check the DocAssist connection: `python manage.py docassist_check [--ask "question"]`."""

from django.core.management.base import BaseCommand, CommandError

from core import docassist
from core.chatbot_config import load_config


class Command(BaseCommand):
    help = 'Verify the DocAssist API key and connectivity, and optionally ask a test question.'

    def add_arguments(self, parser):
        parser.add_argument('--ask', help='Send a test question to /chat.')

    def handle(self, *args, ask=None, **options):
        config = load_config()
        if not config.docassist_api_key:
            raise CommandError('No DocAssist API key: set one on the chatbot settings page or DOCASSIST_API_KEY.')
        self.stdout.write(f'Base URL: {config.docassist_base_url}')
        self.stdout.write(f'Enabled:  {docassist.is_enabled()}')

        results = docassist.health()
        status, body = results['health']
        self.stdout.write(f'/health        → {status} {body}')
        status, body = results['validate_key']
        self.stdout.write(f'/validate-key  → {status} {body}')
        if status != 200:
            raise CommandError('API key rejected or service unreachable.')
        status, body = results['documents']
        docs = (body or {}).get('documents') or []
        self.stdout.write(f'/documents     → {status}, {len(docs)} document(s)')
        for d in docs:
            self.stdout.write(f"    • {d.get('filename')} ({d.get('pages')} pages)")

        if ask:
            reply = docassist.ask(ask)
            if reply is None:
                raise CommandError('/chat failed — see the log for details.')
            self.stdout.write(self.style.SUCCESS(f"\nAnswer: {reply['answer']}"))
            self.stdout.write(docassist.format_citations(reply.get('citations')) or '(no citations)')
