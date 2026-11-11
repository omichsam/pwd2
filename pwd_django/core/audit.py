import logging
import uuid

from core.models import AuditEvent
from core.request_context import get_request_id


logger = logging.getLogger(__name__)


def record_audit_event(
    action,
    *,
    actor=None,
    entity_type,
    entity_id='',
    assessment=None,
    old_status='',
    new_status='',
    reason='',
    metadata=None,
    request_id=None,
):
    context_request_id = request_id or get_request_id()
    if context_request_id and not isinstance(context_request_id, uuid.UUID):
        context_request_id = uuid.UUID(str(context_request_id))

    event = AuditEvent.objects.create(
        actor=actor if getattr(actor, 'is_authenticated', False) else None,
        actor_role=getattr(actor, 'role', '') if actor else '',
        action=action,
        entity_type=entity_type,
        entity_id=str(entity_id or ''),
        assessment=assessment,
        old_status=old_status,
        new_status=new_status,
        reason=reason,
        request_id=context_request_id,
        metadata=metadata or {},
    )
    logger.info(
        'audit_event_created',
        extra={
            'audit_action': action,
            'audit_event_id': str(event.event_id),
            'entity_type': entity_type,
            'entity_id': str(entity_id or ''),
        },
    )
    return event
