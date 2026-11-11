import logging
from django.core.mail import EmailMultiAlternatives
from django.template.loader import render_to_string
from django.conf import settings

logger = logging.getLogger(__name__)


def notify(user, title, message, notif_type='info', link=''):
    """Create an in-app notification for a user."""
    from core.models import Notification
    Notification.objects.create(
        recipient=user,
        title=title,
        message=message,
        notification_type=notif_type,
        link=link,
    )


def send_email(to_email, subject, template_name, context):
    """Render an HTML email template and send it. Silently logs failures."""
    try:
        html_body = render_to_string(f'emails/{template_name}', context)
        text_body = context.get('_plain', html_body)
        msg = EmailMultiAlternatives(
            subject=f'[PWD Access Kenya] {subject}',
            body=text_body,
            from_email=settings.DEFAULT_FROM_EMAIL,
            to=[to_email],
        )
        msg.attach_alternative(html_body, 'text/html')
        msg.send(fail_silently=False)
    except Exception as exc:
        logger.warning(
            'email_delivery_failed',
            extra={
                'email_template': template_name,
                'error_type': type(exc).__name__,
            },
        )


def notify_and_email(user, title, message, subject, template_name, context,
                     notif_type='info', link=''):
    """Create in-app notification AND send email in one call."""
    notify(user, title, message, notif_type, link)
    if user.email:
        context.setdefault('user', user)
        context.setdefault('title', title)
        context.setdefault('message', message)
        context.setdefault('link', link)
        send_email(user.email, subject, template_name, context)
