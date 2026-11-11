"""Knowledge base for the PWD Access assistant.

Content only — no request handling. `chatbot_views` imports these structures,
matches an incoming message against `INTENTS` and renders the answer.

Each intent is a dict:
    id        unique slug, also used as the explicit intent sent by quick replies
    keywords  phrases a user might type (English + common Kiswahili terms).
              Multi-word phrases score higher than single tokens.
    text      answer. Either a string, or a dict keyed by user role with a '*'
              fallback, so officers get workflow-specific wording.
    replies   quick-reply buttons shown after the answer.
    live      True when the answer is generated from the database instead of
              `text` (handled in chatbot_views).
    roles     optional set of roles this intent is offered to. Absent = everyone.
"""

from django.urls import reverse_lazy

# ── Link targets ───────────────────────────────────────────────────────────────
URLS = {
    'login':             reverse_lazy('accounts:login'),
    'register':          reverse_lazy('accounts:register'),
    'official_register': reverse_lazy('accounts:official_register'),
    'forgot_password':   reverse_lazy('accounts:forgot_password'),
    'pwd_dashboard':     reverse_lazy('pwd:dashboard'),
    'pwd_application':   reverse_lazy('pwd:application'),
    'pwd_hospitals':     reverse_lazy('pwd:hospitals'),
    'pwd_profile_edit':  reverse_lazy('pwd:edit_profile'),
    'medical_queue':     reverse_lazy('medical:assessment_list'),
    'health_queue':      reverse_lazy('health:assessment_list'),
    'county_queue':      reverse_lazy('supervisor:assessment_list'),
    'county_officers':   reverse_lazy('supervisor:officer_list'),
    'hospital_officers': reverse_lazy('hospital_admin:officer_list'),
    'national_counties': reverse_lazy('national_admin:county_breakdown'),
}

SUPPORT_EMAIL = 'support@pwdaccess.go.ke'
SUPPORT_PHONE = '+254 800 000 000'
SUPPORT_HOURS = 'Mon – Fri, 8:00 AM – 5:00 PM'

# Roles that act on assessments rather than owning them.
OFFICER_ROLES = {
    'medical_officer', 'health_officer', 'county_officer',
    'hospital_admin', 'national_admin',
}

# ── Reusable quick-reply sets ──────────────────────────────────────────────────
MENU_REPLY = {'label': '🔙 Main Menu', 'intent': 'menu'}

CITIZEN_MENU = [
    {'label': '📝 Register',        'intent': 'register'},
    {'label': '🏥 Book Assessment', 'intent': 'book'},
    {'label': '♿ Disability Types', 'intent': 'disability_types'},
    {'label': '📋 My Status',       'intent': 'status'},
    {'label': '⏱️ How Long?',       'intent': 'timeline'},
    {'label': '🏅 Certificate',     'intent': 'certificate'},
    {'label': '💰 Any Fees?',       'intent': 'cost'},
    {'label': '📞 Contact Us',      'intent': 'contact'},
]

OFFICER_MENU = [
    {'label': '📥 My Work Queue',  'intent': 'my_queue'},
    {'label': '🧭 My Role',        'intent': 'my_role'},
    {'label': '🔄 Full Process',   'intent': 'process'},
    {'label': '♿ Disability Types', 'intent': 'disability_types'},
    {'label': '🏅 Certificates',   'intent': 'certificate'},
    {'label': '📞 Contact Us',     'intent': 'contact'},
]


def menu_for(role):
    """Quick replies appropriate to the signed-in role."""
    return list(OFFICER_MENU if role in OFFICER_ROLES else CITIZEN_MENU)


# ── Status wording ─────────────────────────────────────────────────────────────
# order matters: index + 1 == step number shown to the user
STATUS_FLOW = ['pending', 'checked', 'approved_by_health_officer', 'approved_by_county_officer']

STATUS_LABELS = {
    'pending':                    '⏳ Pending — awaiting medical assessment',
    'checked':                    '🔵 Medical review complete — with the Health Officer',
    'approved_by_health_officer': '✅ Health Officer approved — awaiting County Officer',
    'approved_by_county_officer': '🏅 Fully certified — your certificate is ready',
    'rejected':                   '❌ Rejected',
}

STATUS_NEXT_STEP = {
    'pending': 'Attend your appointment at the hospital on the date shown. Carry your ID and any medical reports.',
    'checked': 'No action needed from you — the Health Officer is reviewing the medical findings.',
    'approved_by_health_officer': 'No action needed — the County Officer is doing the final approval.',
    'approved_by_county_officer': 'Open your dashboard and click *Print Certificate*.',
    'rejected': 'Read the reason below, then book a new assessment once you have addressed it.',
}

# ── Kiswahili / colloquial synonyms folded into English tokens ─────────────────
SYNONYMS = {
    # greetings & courtesy
    'habari': 'hello', 'jambo': 'hello', 'hujambo': 'hello', 'sasa': 'hello',
    'mambo': 'hello', 'niaje': 'hello', 'hi': 'hello', 'hey': 'hello', 'hallo': 'hello',
    'asante': 'thanks', 'ahsante': 'thanks', 'shukrani': 'thanks', 'thx': 'thanks',
    'thank': 'thanks', 'kwaheri': 'bye', 'baadaye': 'bye',
    # domain
    'msaada': 'help', 'saidia': 'help', 'nisaidie': 'help',
    'cheti': 'certificate', 'vyeti': 'certificate', 'cert': 'certificate',
    'hospitali': 'hospital', 'kliniki': 'hospital', 'clinic': 'hospital',
    'daktari': 'doctor', 'dokta': 'doctor',
    'ulemavu': 'disability', 'kilema': 'disability',
    'pesa': 'cost', 'malipo': 'cost', 'bei': 'cost', 'gharama': 'cost',
    'fee': 'cost', 'fees': 'cost', 'charge': 'cost', 'price': 'cost', 'pay': 'cost',
    'jinsi': 'how', 'wapi': 'where', 'lini': 'when', 'nini': 'what',
    'sajili': 'register', 'kujiandikisha': 'register', 'signup': 'register',
    'kuingia': 'login', 'signin': 'login', 'logon': 'login',
    'muda': 'long', 'siku': 'days',
    'kitambulisho': 'id', 'nid': 'id',
    'appointment': 'booking', 'appointments': 'booking', 'book': 'booking',
    'cancel': 'reschedule', 'postpone': 'reschedule',
    'reject': 'rejected', 'declined': 'rejected', 'denied': 'rejected',
    'pass': 'password', 'passwd': 'password',
}

# Words that carry no intent signal.
STOPWORDS = {
    'a', 'an', 'and', 'are', 'as', 'at', 'be', 'been', 'but', 'by', 'can', 'could',
    'did', 'do', 'does', 'for', 'from', 'get', 'got', 'has', 'have', 'i', 'if',
    'in', 'is', 'it', 'its', 'just', 'me', 'my', 'na', 'of', 'on', 'or', 'our',
    'please', 'so', 'that', 'the', 'their', 'them', 'there', 'they', 'this', 'to',
    'up', 'us', 'want', 'was', 'we', 'were', 'will', 'with', 'would', 'you', 'your',
    'ya', 'wa', 'kwa', 'ni', 'je',
}

# Interrogatives and near-filler verbs. They carry *some* meaning ("how long"),
# but on their own they must never decide an intent — otherwise every question
# starting with "what" lands on whichever intent happens to mention "what".
WEAK_TOKENS = {
    'what', 'how', 'who', 'where', 'when', 'why', 'which', 'need', 'know',
    'tell', 'show', 'make', 'take', 'give', 'say', 'am', 'thing', 'site',
}

# ── Intents ────────────────────────────────────────────────────────────────────
INTENTS = [
    {
        'id': 'menu',
        'keywords': ['menu', 'main menu', 'start', 'start over', 'restart', 'home',
                     'options', 'topics', 'help', 'what can you do', 'anything else'],
        'text': 'What would you like help with? Pick a topic below, or just type your question.',
        'replies': None,  # role-specific menu injected at runtime
    },
    {
        'id': 'greeting',
        'keywords': ['hello', 'good morning', 'good afternoon', 'good evening', 'greetings'],
        'text': {
            '*': "Hello! 👋 I'm the PWD Assistant. I can explain registration, booking an "
                 "assessment, certificates and fees — or check your application status.\n\n"
                 "What would you like to know?",
        },
        'replies': None,
    },
    {
        'id': 'thanks',
        'keywords': ['thanks', 'appreciate', 'appreciated', 'useful', 'helpful', 'good bot'],
        'text': "You're very welcome! 😊 Anything else I can help with?",
        'replies': None,
    },
    {
        'id': 'bye',
        'keywords': ['bye', 'goodbye', 'see you', 'thats all', 'that is all', 'nothing else'],
        'text': "Take care! 👋 I'll be right here in the corner if you need anything else.\n\n"
                f"For urgent matters call {SUPPORT_PHONE}.",
        'replies': [MENU_REPLY],
    },

    # ── Getting started ──
    {
        'id': 'register',
        'keywords': ['register', 'registration', 'create account', 'new account', 'open account',
                     'join', 'enrol', 'enroll', 'how do i register', 'become a member'],
        'text': (
            "To register as a PWD Citizen:\n\n"
            "1️⃣ Click *Register* on the homepage\n"
            "2️⃣ Fill in your personal details and date of birth\n"
            "3️⃣ Enter your National ID (18 and over) or Birth Certificate number (under 18)\n"
            "4️⃣ Create a password and confirm it\n\n"
            "Registration is *free* and takes about 5 minutes. A parent or guardian may register "
            "on behalf of a child or a person who needs assistance."
        ),
        'replies': [
            {'label': '📄 What documents?', 'intent': 'documents'},
            {'label': '👨‍⚕️ I am a health worker', 'intent': 'official_register'},
            {'label': '📝 Register Now →', 'intent': 'register', 'url': URLS['register']},
            MENU_REPLY,
        ],
    },
    {
        'id': 'official_register',
        'keywords': ['officer', 'official', 'staff', 'medical officer', 'health officer',
                     'county officer', 'doctor', 'nurse', 'license', 'licence', 'license id',
                     'register as officer', 'staff account', 'work here', 'employee'],
        'text': (
            "Health workers and county staff use a separate sign-up:\n\n"
            "1️⃣ Go to *Register → Official / Health Worker*\n"
            "2️⃣ Enter your professional *License ID* (this is your username)\n"
            "3️⃣ Choose your role and hospital\n"
            "4️⃣ Submit and wait for approval\n\n"
            "⚠️ Your account stays inactive until a County Officer or Hospital Admin approves it. "
            "You will not be able to log in before then."
        ),
        'replies': [
            {'label': '👨‍⚕️ Official Sign-up →', 'intent': 'official_register', 'url': URLS['official_register']},
            {'label': '🔄 Full Process', 'intent': 'process'},
            MENU_REPLY,
        ],
    },
    {
        'id': 'login',
        'keywords': ['login', 'log in', 'cannot log in', 'cant login', 'sign in', 'password',
                     'forgot password', 'reset password', 'locked out', 'wrong password',
                     'account not working', 'access my account', 'username'],
        'text': (
            "Logging in:\n\n"
            "• *PWD Citizens* log in with their *National ID / Birth Certificate number*\n"
            "• *Officials* log in with their *License ID*\n\n"
            "Forgot your password? Click *Forgot Password* on the login page and we will email you "
            "a reset link (valid for 24 hours).\n\n"
            "Still blocked? If you are an official, your account may not be approved yet."
        ),
        'replies': [
            {'label': '🔑 Reset Password →', 'intent': 'login', 'url': URLS['forgot_password']},
            {'label': '➡️ Go to Login', 'intent': 'login', 'url': URLS['login']},
            {'label': '📞 Contact Support', 'intent': 'contact'},
            MENU_REPLY,
        ],
    },
    {
        'id': 'documents',
        'keywords': ['document', 'documents', 'id', 'id number', 'national id', 'birth certificate',
                     'requirements', 'what do i need', 'what to bring', 'required', 'paperwork',
                     'medical report', 'medical records', 'upload'],
        'text': (
            "For *registration* you need:\n\n"
            "✅ National ID (18 and over) *or* Birth Certificate (under 18)\n"
            "✅ A mobile phone number\n"
            "✅ An email address\n\n"
            "For your *assessment appointment*, also bring:\n\n"
            "📋 Any existing medical reports, referral letters or discharge summaries\n"
            "📋 Previous disability documentation, if you have any\n\n"
            "Your medical officer can upload supporting files (PDF, JPG or PNG) during the assessment."
        ),
        'replies': [
            {'label': '📝 Register Now →', 'intent': 'register', 'url': URLS['register']},
            {'label': '🏥 Book Assessment', 'intent': 'book'},
            MENU_REPLY,
        ],
    },
    {
        'id': 'book',
        'keywords': ['booking', 'schedule', 'visit', 'how do i book', 'apply', 'application',
                     'new assessment', 'make appointment', 'see a doctor', 'get assessed'],
        'text': (
            "To book an assessment:\n\n"
            "1️⃣ Log in to your account\n"
            "2️⃣ Click *Book Assessment* on your dashboard\n"
            "3️⃣ Choose an assessment centre near you\n"
            "4️⃣ Select the disability type being assessed\n"
            "5️⃣ Pick an available date\n\n"
            "You'll get a notification confirming the appointment, and it will appear on your "
            "dashboard with a live status."
        ),
        'replies': [
            {'label': '🏥 Find Centres', 'intent': 'hospitals'},
            {'label': '♿ Disability Types', 'intent': 'disability_types'},
            {'label': '📅 Book Now →', 'intent': 'book', 'url': URLS['pwd_application']},
            MENU_REPLY,
        ],
    },
    {
        'id': 'reschedule',
        'keywords': ['reschedule', 'change date', 'change appointment', 'missed appointment',
                     'did not attend', 'move my appointment', 'cannot make it', 'wrong hospital',
                     'wrong date', 'change hospital'],
        'text': (
            "Need to change or cancel an appointment?\n\n"
            "Appointments cannot be edited once booked, so:\n\n"
            "1️⃣ Call the assessment centre to let them know\n"
            "2️⃣ Book a fresh appointment from your dashboard for a date that works\n\n"
            "If you missed your appointment, your assessment simply stays *Pending* — book again "
            "when you are ready. There is no penalty."
        ),
        'replies': [
            {'label': '📅 Book Again →', 'intent': 'book', 'url': URLS['pwd_application']},
            {'label': '🏥 Centre Contacts', 'intent': 'hospitals'},
            MENU_REPLY,
        ],
    },
    {
        'id': 'on_behalf',
        'keywords': ['child', 'children', 'minor', 'my son', 'my daughter', 'baby', 'kid',
                     'guardian', 'parent', 'on behalf', 'for someone else', 'my mother',
                     'my father', 'my brother', 'my sister', 'relative', 'caregiver',
                     'under 18', 'can my child'],
        'text': (
            "Yes — you can act for someone who cannot apply themselves:\n\n"
            "👶 *Children under 18* — a parent or guardian registers using the child's *Birth "
            "Certificate number*, and the parent's own phone and email\n"
            "🧑‍🦽 *Adults needing assistance* — a family member or caregiver may register and book "
            "on their behalf using the person's *National ID*\n\n"
            "⚠️ The account and the certificate always belong to the person being assessed, and "
            "*they must attend the appointment in person* — the medical officer has to examine them.\n\n"
            "Bring your own ID too, so staff can record who accompanied them."
        ),
        'replies': [
            {'label': '📄 Documents needed', 'intent': 'documents'},
            {'label': '📝 Register →', 'intent': 'register', 'url': URLS['register']},
            MENU_REPLY,
        ],
    },
    {
        'id': 'hospitals',
        'keywords': ['hospital', 'hospitals', 'centre', 'center', 'centres', 'facility',
                     'facilities', 'assessment centre', 'where can i go', 'location', 'near me',
                     'nearby', 'county', 'which hospital'],
        'live': True,
    },
    {
        'id': 'disability_types',
        'keywords': ['disability', 'disability type', 'types', 'physical', 'hearing', 'deaf',
                     'visual', 'blind', 'sight', 'mental', 'psychiatric', 'maxillofacial',
                     'chronic', 'albinism', 'speech', 'language', 'communication', 'autism',
                     'conditions', 'covered', 'categories', 'which disabilities', 'qualify',
                     'eligible', 'eligibility'],
        'text': (
            "PWD Access assesses 7 categories:\n\n"
            "1. 🦽 *Physical* — mobility, limbs, spine\n"
            "2. 🔇 *Hearing* — deafness, hearing loss\n"
            "3. 👁️ *Visual* — blindness, low vision, albinism-related\n"
            "4. 🧠 *Mental* — psychiatric and intellectual conditions\n"
            "5. 😬 *Maxillofacial* — face, jaw and oral structures\n"
            "6. 💊 *Chronic* — long-term illness causing impairment\n"
            "7. 🗣️ *Speech, Language & Communication*\n\n"
            "Each category has its own specialised assessment form completed by a medical officer. "
            "If more than one applies, book the category that affects you most and mention the others "
            "at the appointment."
        ),
        'replies': [
            {'label': '📅 Book Assessment', 'intent': 'book'},
            {'label': '⏱️ How long does it take?', 'intent': 'timeline'},
            MENU_REPLY,
        ],
    },

    # ── Process & expectations ──
    {
        'id': 'process',
        'keywords': ['process', 'stages', 'steps', 'how it works', 'workflow', 'who approves',
                     'what happens next', 'procedure', 'journey', 'levels of approval'],
        'text': {
            '*': (
                "Every assessment moves through 4 stages:\n\n"
                "1️⃣ *Booked* → you choose a centre and date — status *Pending*\n"
                "2️⃣ *Medical Officer* examines you and fills the disability form — status "
                "*Medical Review Complete*\n"
                "3️⃣ *Health Officer* reviews the findings — status *Health Officer Approved*\n"
                "4️⃣ *County Officer* gives final approval — status *Fully Certified* and your "
                "certificate is issued\n\n"
                "You are notified at every stage, and your dashboard always shows the current step."
            ),
            'medical_officer': (
                "Your position in the workflow (stage 2 of 4):\n\n"
                "1️⃣ Citizen books an appointment — arrives as *Pending*\n"
                "2️⃣ *You* examine the citizen, complete the disability-specific form and submit → "
                "status becomes *Medical Review Complete*\n"
                "3️⃣ Health Officer reviews your findings\n"
                "4️⃣ County Officer certifies\n\n"
                "You can only see assessments booked at your hospital, and only the disability types "
                "your designation allows."
            ),
            'health_officer': (
                "Your position in the workflow (stage 3 of 4):\n\n"
                "1️⃣ Citizen books — *Pending*\n"
                "2️⃣ Medical Officer completes the clinical form — *Medical Review Complete*\n"
                "3️⃣ *You* review the findings and either approve or reject with a reason\n"
                "4️⃣ County Officer gives final certification\n\n"
                "Rejecting sends the citizen a notification with your reason, so be specific."
            ),
            'county_officer': (
                "Your position in the workflow (stage 4 of 4):\n\n"
                "1️⃣–3️⃣ Booking, medical assessment, health officer review\n"
                "4️⃣ *You* give final approval — this issues the numbered certificate with its QR "
                "verification code\n\n"
                "You also manage the county's hospitals, sub-counties, officer approvals and "
                "designations, and can revoke or re-issue certificates."
            ),
            'hospital_admin': (
                "As Hospital Admin you support stage 2 of the workflow:\n\n"
                "• Approve and deactivate medical and health officers at your facility\n"
                "• Monitor assessment volumes and turnaround at your hospital\n\n"
                "Clinical decisions stay with the medical and health officers; certification stays "
                "with the County Officer."
            ),
            'national_admin': (
                "You oversee the whole pipeline:\n\n"
                "1️⃣ Booking → 2️⃣ Medical assessment → 3️⃣ Health officer review → 4️⃣ County certification\n\n"
                "Your tools cover counties, hospitals, county-officer accounts and national reporting."
            ),
        },
        'replies': [
            {'label': '📋 Check Status', 'intent': 'status'},
            {'label': '⏱️ How long?', 'intent': 'timeline'},
            MENU_REPLY,
        ],
    },
    {
        'id': 'timeline',
        'keywords': ['how long', 'long', 'duration', 'days', 'weeks', 'when will', 'how soon',
                     'turnaround', 'take time', 'delay', 'delayed', 'still waiting', 'taking long',
                     'timeline', 'how many days'],
        'text': (
            "Typical timings:\n\n"
            "📅 *Booking* — immediate; choose any available date\n"
            "🩺 *Medical assessment* — completed on the day of your appointment\n"
            "✅ *Health Officer review* — usually 2 to 5 working days\n"
            "🏅 *County Officer certification* — usually 3 to 7 working days\n\n"
            "So most people are certified within *2 to 3 weeks* of their appointment. "
            "Public holidays and busy periods can add a few days.\n\n"
            "Waiting much longer than that? Check your status first — it may be waiting on you."
        ),
        'replies': [
            {'label': '📋 Check My Status', 'intent': 'status'},
            {'label': '📞 Contact Support', 'intent': 'contact'},
            MENU_REPLY,
        ],
    },
    {
        'id': 'cost',
        'keywords': ['cost', 'how much', 'is it free', 'free', 'money', 'expensive', 'budget',
                     'bribe', 'payment', 'deposit'],
        'text': (
            "💰 *Assessment and certification are free.*\n\n"
            "There is no charge for registering, booking, being assessed, or receiving your "
            "certificate.\n\n"
            "⚠️ Nobody should ask you for money to speed up your application. If anyone does, "
            f"report it to us at {SUPPORT_EMAIL} or {SUPPORT_PHONE}.\n\n"
            "You may still need to cover your own transport to the assessment centre."
        ),
        'replies': [
            {'label': '📅 Book Assessment', 'intent': 'book'},
            {'label': '📞 Report an Issue', 'intent': 'contact'},
            MENU_REPLY,
        ],
    },
    {
        'id': 'status',
        'keywords': ['status', 'progress', 'stage', 'pending', 'approved', 'check status',
                     'where is my application', 'my application', 'update', 'how far',
                     'processed', 'ready', 'my assessment', 'track'],
        'live': True,
    },
    {
        'id': 'my_queue',
        'keywords': ['my queue', 'work queue', 'my workload', 'assigned to me', 'pending reviews',
                     'my cases', 'how many pending', 'my assessments', 'to do'],
        'live': True,
        'roles': OFFICER_ROLES,
    },
    {
        'id': 'my_role',
        'keywords': ['my role', 'what do i do', 'my duties', 'my responsibilities',
                     'my permissions', 'what can i see', 'my designation'],
        'live': True,
        'roles': OFFICER_ROLES,
    },
    {
        'id': 'rejected',
        'keywords': ['rejected', 'appeal', 'reapply', 'apply again', 'turned down', 'not approved',
                     'why was i rejected', 'dispute', 'second opinion'],
        'text': (
            "If an assessment is rejected:\n\n"
            "1️⃣ Open the assessment on your dashboard — the officer's *reason* is shown there\n"
            "2️⃣ Address what was raised (for example, get the missing medical report)\n"
            "3️⃣ Book a *new* assessment — there is no limit on re-applications and no fee\n\n"
            "If you believe the decision was wrong, contact the County Health Office through our "
            "support line and quote your assessment date and hospital."
        ),
        'replies': [
            {'label': '📋 See My Reason', 'intent': 'status'},
            {'label': '📅 Book Again →', 'intent': 'book', 'url': URLS['pwd_application']},
            {'label': '📞 Contact Support', 'intent': 'contact'},
            MENU_REPLY,
        ],
    },

    # ── Certificate ──
    {
        'id': 'certificate',
        'keywords': ['certificate', 'download', 'print', 'certified', 'official document',
                     'get my certificate', 'certificate number', 'copy of certificate',
                     'lost certificate', 'replace certificate'],
        'text': (
            "Once your assessment is *Fully Certified* by the County Officer:\n\n"
            "1️⃣ Log in and open your Dashboard\n"
            "2️⃣ Find the certified assessment\n"
            "3️⃣ Click *Print Certificate* — you can print it or save it as a PDF\n\n"
            "Each certificate has a unique number and a QR code, and can be re-printed any time — "
            "so a lost paper copy is never a problem."
        ),
        'replies': [
            {'label': '📋 Check Status', 'intent': 'status'},
            {'label': '🔍 Verify a Certificate', 'intent': 'verify'},
            {'label': '🎁 What is it used for?', 'intent': 'rights'},
            {'label': '📊 My Dashboard →', 'intent': 'status', 'url': URLS['pwd_dashboard']},
            MENU_REPLY,
        ],
    },
    {
        'id': 'verify',
        'keywords': ['verify', 'verification', 'qr', 'qr code', 'scan', 'authentic', 'genuine',
                     'fake certificate', 'confirm certificate', 'is it valid', 'check certificate',
                     'verify certificate', 'verify a certificate', 'validate certificate'],
        'text': (
            "Every PWD Access certificate carries a *QR code*:\n\n"
            "📱 Scan it with any phone camera — it opens a public verification page\n"
            "🔎 The page confirms the certificate number, the holder, the disability category and "
            "whether the certificate is *currently valid*\n"
            "🚫 Revoked or replaced certificates show clearly as invalid\n\n"
            "No login is needed to verify, so employers, schools and county offices can check "
            "instantly."
        ),
        'replies': [
            {'label': '🏅 Get My Certificate', 'intent': 'certificate'},
            MENU_REPLY,
        ],
    },
    {
        'id': 'rights',
        'keywords': ['benefit', 'benefits', 'rights', 'entitlement', 'exemption', 'tax',
                     'tax exemption', 'bursary', 'why do i need a certificate', 'used for',
                     'employment', 'school', 'assistive device', 'ncpwd', 'support'],
        'text': (
            "A PWD certificate is the official proof of disability in Kenya. It is normally "
            "required to:\n\n"
            "🏛️ Register with the National Council for Persons with Disabilities (NCPWD)\n"
            "💼 Access disability-inclusive employment provisions\n"
            "📚 Apply for bursaries and education support\n"
            "🧾 Apply for tax exemptions and duty relief on assistive devices\n"
            "🚌 Claim service and access accommodations\n\n"
            "PWD Access issues and verifies the certificate. Individual benefits are administered "
            "by NCPWD and the relevant government department, so apply to them with your certificate."
        ),
        'replies': [
            {'label': '🏅 Get My Certificate', 'intent': 'certificate'},
            {'label': '📞 Contact Us', 'intent': 'contact'},
            MENU_REPLY,
        ],
    },

    # ── Account & meta ──
    {
        'id': 'update_profile',
        'keywords': ['update profile', 'edit profile', 'change my details', 'change phone',
                     'change phone number', 'update phone number', 'new phone number',
                     'change email', 'change my email', 'wrong name', 'correct my details',
                     'next of kin', 'change county', 'my profile', 'change my address'],
        'text': (
            "To update your details:\n\n"
            "1️⃣ Log in and open *Profile*\n"
            "2️⃣ Click *Edit Profile*\n"
            "3️⃣ Update your phone, email, address, next of kin or other details and save\n\n"
            "⚠️ Your *ID number* cannot be changed by you, because it identifies your record and "
            "appears on your certificate. If it is wrong, contact support with proof of the correct "
            "number."
        ),
        'replies': [
            {'label': '✏️ Edit Profile →', 'intent': 'update_profile', 'url': URLS['pwd_profile_edit']},
            {'label': '📞 Contact Support', 'intent': 'contact'},
            MENU_REPLY,
        ],
    },
    {
        'id': 'accessibility',
        'keywords': ['accessibility', 'accessible', 'font size', 'bigger text', 'large text',
                     'contrast', 'high contrast', 'screen reader', 'keyboard', 'sign language',
                     'braille', 'hard to read', 'cannot see'],
        'text': (
            "This site is built to be used by everyone:\n\n"
            "🔠 *Text size* — use the ♿ accessibility button (bottom-right) to enlarge text up to 140%\n"
            "🌗 *High contrast* — switch it on from the same menu\n"
            "⌨️ *Keyboard only* — press Tab to move, Enter to activate; a *Skip to main content* "
            "link is first on every page\n"
            "🔊 *Screen readers* — pages use proper landmarks and labels\n"
            "🤟 *Sign language or in-person help* — ask at your assessment centre, or call us\n\n"
            "Your settings are remembered on this device."
        ),
        'replies': [
            {'label': '📞 Contact Us', 'intent': 'contact'},
            MENU_REPLY,
        ],
    },
    {
        'id': 'privacy',
        'keywords': ['privacy', 'data', 'my data', 'secure', 'security', 'safe', 'confidential',
                     'who can see', 'share my information', 'delete my account', 'gdpr'],
        'text': (
            "Your information is handled as confidential health data:\n\n"
            "🔒 Only the officers handling your assessment — your medical officer, the health "
            "officer and the county officer — can see your clinical details\n"
            "📝 Every view and approval is recorded in an audit trail\n"
            "🚫 We never sell your data or share it for marketing\n"
            "✅ Certificate *verification* only reveals validity and category, never your medical "
            "findings\n\n"
            "To request a copy of your data or raise a concern, contact us."
        ),
        'replies': [
            {'label': '📞 Contact Us', 'intent': 'contact'},
            MENU_REPLY,
        ],
    },
    {
        'id': 'language',
        'keywords': ['swahili', 'kiswahili', 'language', 'english', 'translate', 'lugha',
                     'speak swahili', 'other language'],
        'text': (
            "The portal is currently in *English*, and I understand common Kiswahili words such as "
            "*cheti*, *hospitali*, *msaada*, *gharama* and *jinsi* — so feel free to mix languages.\n\n"
            "Kiswahili screens are on the roadmap. At your assessment centre, staff can assist you "
            "in Kiswahili or your local language."
        ),
        'replies': [MENU_REPLY],
    },
    {
        'id': 'human',
        'keywords': ['human', 'real person', 'agent', 'talk to someone', 'speak to someone',
                     'customer care', 'complaint', 'complain', 'you are not helping',
                     'this is useless', 'escalate'],
        'text': (
            "Of course — a person can help you directly:\n\n"
            f"📞 {SUPPORT_PHONE} ({SUPPORT_HOURS})\n"
            f"📧 {SUPPORT_EMAIL}\n\n"
            "When you get in touch, have ready: your ID number, the assessment centre and the "
            "appointment date. That lets the team find your record immediately."
        ),
        'replies': [
            {'label': '📞 Contact Details', 'intent': 'contact'},
            MENU_REPLY,
        ],
    },
    {
        'id': 'contact',
        'keywords': ['contact', 'support', 'phone', 'phone number', 'email', 'call', 'reach you',
                     'office', 'address', 'opening hours', 'hours', 'helpline'],
        'text': (
            "Get in touch:\n\n"
            f"📧 {SUPPORT_EMAIL}\n"
            f"📞 {SUPPORT_PHONE}\n"
            f"🕐 {SUPPORT_HOURS}\n"
            "📍 Nairobi, Kenya\n\n"
            "For anything urgent, please call — it is the fastest route. Quote your ID number so "
            "the team can pull up your record."
        ),
        'replies': [
            {'label': '🏥 Centre Contacts', 'intent': 'hospitals'},
            MENU_REPLY,
        ],
    },
    {
        'id': 'about',
        'keywords': ['who are you', 'what are you', 'about', 'about pwd access', 'bot', 'robot',
                     'are you human', 'your name', 'what is this site', 'what is pwd access'],
        'text': (
            "I'm the *PWD Assistant* — an automated helper for PWD Access Kenya, the county "
            "disability assessment and certification portal.\n\n"
            "I can explain how registration, booking, assessments, fees and certificates work, and "
            "if you are logged in I can look up your own application status.\n\n"
            "I'm not a medical professional and I can't make decisions on your application — for "
            "that you'll want a human."
        ),
        'replies': [
            {'label': '🔄 How it works', 'intent': 'process'},
            {'label': '🙋 Talk to a person', 'intent': 'human'},
            MENU_REPLY,
        ],
    },
]

INTENTS_BY_ID = {i['id']: i for i in INTENTS}

# Human-friendly names used by the "did you mean?" suggestions.
SUGGESTION_LABELS = {
    'register':          '📝 How to register',
    'official_register': '👨‍⚕️ Officer sign-up',
    'login':             '🔑 Login & password',
    'documents':         '📄 Documents needed',
    'on_behalf':         '👶 Applying for someone else',
    'book':              '📅 Book an assessment',
    'reschedule':        '🔁 Change appointment',
    'hospitals':         '🏥 Assessment centres',
    'disability_types':  '♿ Disability types',
    'process':           '🔄 How the process works',
    'timeline':          '⏱️ How long it takes',
    'cost':              '💰 Fees',
    'status':            '📋 My application status',
    'my_queue':          '📥 My work queue',
    'my_role':           '🧭 My role',
    'rejected':          '❌ Rejected applications',
    'certificate':       '🏅 Certificates',
    'verify':            '🔍 Verify a certificate',
    'rights':            '🎁 Benefits & rights',
    'update_profile':    '✏️ Update my details',
    'accessibility':     '♿ Accessibility options',
    'privacy':           '🔒 Privacy & data',
    'language':          '🌍 Language',
    'human':             '🙋 Talk to a person',
    'contact':           '📞 Contact us',
    'about':             '🤖 About this assistant',
}
