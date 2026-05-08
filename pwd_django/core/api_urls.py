from django.urls import path
from django.http import JsonResponse
from .models import SubCounty, Hospital


def subcounties(request, county_id):
    subs = list(SubCounty.objects.filter(county_id=county_id).values('id', 'name'))
    return JsonResponse({'subcounties': subs})


def hospitals_by_county(request, county_id):
    hospitals = list(Hospital.objects.filter(county_id=county_id).values('id', 'name', 'address'))
    return JsonResponse({'hospitals': hospitals})


urlpatterns = [
    path('subcounties/<int:county_id>/', subcounties, name='api_subcounties'),
    path('hospitals/<int:county_id>/', hospitals_by_county, name='api_hospitals'),
]
