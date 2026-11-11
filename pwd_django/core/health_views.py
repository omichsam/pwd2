from django.db import connection
from django.http import JsonResponse
from django.views.decorators.cache import never_cache
from django.views.decorators.http import require_GET


def _response(status, http_status=200):
    response = JsonResponse({'status': status}, status=http_status)
    response['Cache-Control'] = 'no-store'
    return response


@require_GET
@never_cache
def live(request):
    return _response('ok')


@require_GET
@never_cache
def ready(request):
    try:
        with connection.cursor() as cursor:
            cursor.execute('SELECT 1')
            cursor.fetchone()
    except Exception:
        return _response('unavailable', 503)
    return _response('ready')
