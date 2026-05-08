from django.core.management.base import BaseCommand
from django.contrib.auth import get_user_model
from core.models import County, SubCounty, Hospital

User = get_user_model()

COUNTIES = [
    ("Mombasa", ["Mvita", "Likoni", "Kisauni", "Nyali", "Changamwe", "Jomvu"]),
    ("Kwale", ["Msambweni", "Lungalunga", "Matuga", "Kinango"]),
    ("Kilifi", ["Kilifi North", "Kilifi South", "Kaloleni", "Rabai", "Ganze", "Malindi", "Magarini"]),
    ("Tana River", ["Garsen", "Galole", "Bura"]),
    ("Lamu", ["Lamu East", "Lamu West"]),
    ("Taita Taveta", ["Taveta", "Wundanyi", "Mwatate", "Voi"]),
    ("Garissa", ["Garissa Township", "Balambala", "Lagdera", "Dadaab", "Fafi", "Ijara"]),
    ("Wajir", ["Wajir East", "Tarbaj", "Wajir West", "Eldas", "Wajir South", "Wajir North"]),
    ("Mandera", ["Mandera East", "Banissa", "Mandera North", "Mandera South", "Mandera West", "Lafey"]),
    ("Marsabit", ["Moyale", "North Horr", "Saku", "Laisamis"]),
    ("Isiolo", ["Isiolo North", "Isiolo South"]),
    ("Meru", ["Igembe South", "Igembe Central", "Igembe North", "Tigania West", "Tigania East", "Central Imenti", "North Imenti", "South Imenti", "Buuri"]),
    ("Tharaka-Nithi", ["Maara", "Chuka/Igambang'ombe", "Tharaka"]),
    ("Embu", ["Manyatta", "Runyenjes", "Mbeere South", "Mbeere North"]),
    ("Kitui", ["Mwingi North", "Mwingi West", "Mwingi Central", "Kitui West", "Kitui Rural", "Kitui Central", "Kitui East", "Kitui South"]),
    ("Machakos", ["Masinga", "Yatta", "Kangundo", "Matungulu", "Kathiani", "Mavoko", "Machakos Town", "Mwala"]),
    ("Makueni", ["Mbooni", "Kilome", "Kaiti", "Makueni", "Kibwezi West", "Kibwezi East"]),
    ("Nyandarua", ["Kinangop", "Kipipiri", "Ol Kalou", "Ol Joro Orok", "Ndaragwa"]),
    ("Nyeri", ["Tetu", "Kieni", "Mathira", "Othaya", "Mukurweini", "Nyeri Town"]),
    ("Kirinyaga", ["Mwea", "Gichugu", "Ndia", "Kirinyaga Central"]),
    ("Murang'a", ["Kangema", "Mathioya", "Kiharu", "Kigumo", "Maragwa", "Kandara", "Gatanga"]),
    ("Kiambu", ["Gatundu South", "Gatundu North", "Juja", "Thika Town", "Ruiru", "Githunguri", "Kiambu", "Kiambaa", "Kabete", "Kikuyu", "Limuru", "Lari"]),
    ("Turkana", ["Turkana North", "Turkana West", "Turkana Central", "Loima", "Turkana South", "Turkana East"]),
    ("West Pokot", ["Pokot South", "West Pokot", "Kacheliba", "Kapenguria"]),
    ("Samburu", ["Samburu West", "Samburu North", "Samburu East"]),
    ("Trans Nzoia", ["Kwanza", "Endebess", "Saboti", "Kiminini", "Cherangany"]),
    ("Uasin Gishu", ["Soy", "Turbo", "Moiben", "Ainabkoi", "Kapseret", "Kesses"]),
    ("Elgeyo-Marakwet", ["Marakwet East", "Marakwet West", "Keiyo North", "Keiyo South"]),
    ("Nandi", ["Tinderet", "Aldai", "Nandi Hills", "Chesumei", "Emgwen", "Mosop"]),
    ("Baringo", ["Tiaty", "Baringo North", "Baringo Central", "Baringo South", "Mogotio", "Eldama Ravine"]),
    ("Laikipia", ["Laikipia West", "Laikipia East", "Laikipia North"]),
    ("Nakuru", ["Molo", "Njoro", "Naivasha", "Gilgil", "Kuresoi South", "Kuresoi North", "Subukia", "Rongai", "Bahati", "Nakuru Town West", "Nakuru Town East"]),
    ("Narok", ["Kilgoris", "Emurua Dikirr", "Narok North", "Narok East", "Narok South", "Narok West"]),
    ("Kajiado", ["Kajiado North", "Kajiado Central", "Kajiado East", "Kajiado West", "Kajiado South"]),
    ("Kericho", ["Kipkelion East", "Kipkelion West", "Ainamoi", "Bureti", "Belgut", "Sigowet/Soin"]),
    ("Bomet", ["Sotik", "Chepalungu", "Bomet East", "Bomet Central", "Konoin"]),
    ("Kakamega", ["Lugari", "Likuyani", "Malava", "Lurambi", "Navakholo", "Mumias West", "Mumias East", "Matungu", "Butere", "Khwisero", "Shinyalu", "Ikolomani"]),
    ("Vihiga", ["Vihiga", "Sabatia", "Hamisi", "Luanda", "Emuhaya"]),
    ("Bungoma", ["Mt. Elgon", "Sirisia", "Kabuchai", "Bumula", "Kanduyi", "Webuye East", "Webuye West", "Kimilili", "Tongaren"]),
    ("Busia", ["Teso North", "Teso South", "Nambale", "Matayos", "Butula", "Funyula", "Budalangi"]),
    ("Siaya", ["Ugenya", "Ugunja", "Alego Usonga", "Gem", "Bondo", "Rarieda"]),
    ("Kisumu", ["Kisumu East", "Kisumu West", "Kisumu Central", "Seme", "Nyando", "Muhoroni", "Nyakach"]),
    ("Homa Bay", ["Kasipul", "Kabondo Kasipul", "Karachuonyo", "Rangwe", "Homa Bay Town", "Ndhiwa", "Suba North", "Suba South"]),
    ("Migori", ["Rongo", "Awendo", "Suna East", "Suna West", "Uriri", "Nyatike", "Kuria West", "Kuria East"]),
    ("Kisii", ["Bonchari", "South Mugirango", "Bomachoge Borabu", "Bobasi", "Bomachoge Chache", "Nyaribari Masaba", "Nyaribari Chache", "Kitutu Chache North", "Kitutu Chache South"]),
    ("Nyamira", ["Kitutu Masaba", "West Mugirango", "North Mugirango", "Borabu"]),
    ("Nairobi", ["Westlands", "Dagoretti North", "Dagoretti South", "Langata", "Kibra", "Roysambu", "Kasarani", "Ruaraka", "Embakasi South", "Embakasi North", "Embakasi Central", "Embakasi East", "Embakasi West", "Makadara", "Kamukunji", "Starehe", "Mathare"]),
]

SAMPLE_HOSPITALS = [
    ("Kenyatta National Hospital", "Nairobi", "Westlands", "Hospital Road, Upper Hill"),
    ("Nairobi Women's Hospital", "Nairobi", "Langata", "Argwings Kodhek Road"),
    ("Aga Khan University Hospital", "Nairobi", "Westlands", "3rd Parklands Avenue"),
    ("Mombasa County Hospital", "Mombasa", "Mvita", "Mama Ngina Drive"),
    ("Coast General Hospital", "Mombasa", "Mvita", "Moi Avenue, Mombasa"),
    ("Kisumu County Referral Hospital", "Kisumu", "Kisumu Central", "Aga Khan Road"),
    ("Nakuru Level 5 Hospital", "Nakuru", "Nakuru Town West", "Hospital Road, Nakuru"),
    ("Meru Teaching and Referral Hospital", "Meru", "Central Imenti", "Hospital Road, Meru"),
    ("Garissa County Referral Hospital", "Garissa", "Garissa Township", "Garissa Town"),
    ("Embu Level 5 Hospital", "Embu", "Manyatta", "Hospital Road, Embu"),
]


class Command(BaseCommand):
    help = "Seed the database with all 47 Kenyan counties, sub-counties, and sample hospitals"

    def add_arguments(self, parser):
        parser.add_argument("--admin", action="store_true", help="Also create a superuser admin account")
        parser.add_argument("--flush", action="store_true", help="Delete existing counties, sub-counties, and hospitals before seeding")

    def handle(self, *args, **options):
        if options["flush"]:
            self.stdout.write("Flushing existing geography data...")
            Hospital.objects.all().delete()
            SubCounty.objects.all().delete()
            County.objects.all().delete()

        self.stdout.write("Seeding counties and sub-counties...")
        county_map = {}
        for county_name, subcounty_names in COUNTIES:
            county, created = County.objects.get_or_create(name=county_name)
            county_map[county_name] = county
            if created:
                self.stdout.write(f"  Created county: {county_name}")
            for sc_name in subcounty_names:
                SubCounty.objects.get_or_create(name=sc_name, county=county)

        self.stdout.write("Seeding sample hospitals...")
        for name, county_name, subcounty, address in SAMPLE_HOSPITALS:
            county = county_map.get(county_name)
            if county:
                Hospital.objects.get_or_create(
                    name=name,
                    defaults={"county": county, "subcounty": subcounty, "address": address},
                )
                self.stdout.write(f"  Created hospital: {name}")

        if options["admin"]:
            if not User.objects.filter(username="admin").exists():
                User.objects.create_superuser(
                    username="admin",
                    email="admin@pwd.go.ke",
                    password="admin1234",
                    first_name="System",
                    last_name="Administrator",
                    role="county_officer",
                    is_approved=True,
                )
                self.stdout.write(self.style.SUCCESS("Created superuser: admin / admin1234"))
            else:
                self.stdout.write("Superuser 'admin' already exists, skipping.")

        self.stdout.write(self.style.SUCCESS(
            f"\nDone! {County.objects.count()} counties, "
            f"{SubCounty.objects.count()} sub-counties, "
            f"{Hospital.objects.count()} hospitals."
        ))
