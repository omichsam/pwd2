import logging
import uuid
from time import perf_counter

from core.request_context import request_id_context


logger = logging.getLogger(__name__)


class RequestIDMiddleware:
    def __init__(self, get_response):
        self.get_response = get_response

    def __call__(self, request):
        started_at = perf_counter()
        request.request_id = uuid.uuid4()
        token = request_id_context.set(request.request_id)
        try:
            response = self.get_response(request)
            response['X-Request-ID'] = str(request.request_id)
            duration_ms = (perf_counter() - started_at) * 1000
            response['Server-Timing'] = f'app;dur={duration_ms:.1f}'
            logger.info(
                'request_completed',
                extra={
                    'request': request,
                    'method': request.method,
                    'view_name': getattr(request.resolver_match, 'view_name', '') or '',
                    'status_code': response.status_code,
                    'duration_ms': round(duration_ms, 1),
                },
            )
            return response
        except Exception:
            logger.exception('unhandled_request_exception')
            raise
        finally:
            request_id_context.reset(token)


class RequestIDLogFilter(logging.Filter):
    def filter(self, record):
        request = getattr(record, 'request', None)
        request_id = getattr(request, 'request_id', None) or request_id_context.get()
        record.request_id = str(request_id or '-')
        return True
