from django.core.paginator import Paginator


DEFAULT_PAGE_SIZE = 50


def paginate_queryset(request, queryset, parameter='page', per_page=DEFAULT_PAGE_SIZE):
    return Paginator(queryset, per_page).get_page(request.GET.get(parameter))
