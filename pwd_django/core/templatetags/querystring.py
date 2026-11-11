from django import template


register = template.Library()


@register.simple_tag
def page_url(request, parameter, number):
    query = request.GET.copy()
    query[parameter] = number
    return '?' + query.urlencode()
