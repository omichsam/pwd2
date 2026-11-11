import os
from pathlib import Path
from django.core.exceptions import ImproperlyConfigured

from pwd_system.env import env_bool, env_int, env_list

BASE_DIR = Path(__file__).resolve().parent.parent

DEBUG = env_bool('DEBUG', True)

_development_secret = 'django-insecure-pwd-access-system-secret-key-change-in-production'
SECRET_KEY = os.environ.get('SECRET_KEY', _development_secret)
if not DEBUG and (
    SECRET_KEY == _development_secret
    or SECRET_KEY.startswith('django-insecure-')
    or len(SECRET_KEY) < 50
):
    raise ImproperlyConfigured(
        'Production requires a random SECRET_KEY containing at least 50 characters.'
    )

ALLOWED_HOSTS = env_list('ALLOWED_HOSTS', 'localhost,127.0.0.1')
CSRF_TRUSTED_ORIGINS = env_list('CSRF_TRUSTED_ORIGINS')
if not DEBUG and not ALLOWED_HOSTS:
    raise ImproperlyConfigured('ALLOWED_HOSTS cannot be empty in production.')

INSTALLED_APPS = [
    'django.contrib.admin',
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.messages',
    'django.contrib.staticfiles',
    'accounts',
    'core',
    'pwd_portal',
    'medical',
    'health',
    'supervisor',
    'hospital_admin',
    'national_admin',
]

MIDDLEWARE = [
    'core.middleware.RequestIDMiddleware',
    'django.middleware.security.SecurityMiddleware',
    'whitenoise.middleware.WhiteNoiseMiddleware',
    'django.contrib.sessions.middleware.SessionMiddleware',
    'django.middleware.common.CommonMiddleware',
    'django.middleware.csrf.CsrfViewMiddleware',
    'django.contrib.auth.middleware.AuthenticationMiddleware',
    'django.contrib.messages.middleware.MessageMiddleware',
    'django.middleware.clickjacking.XFrameOptionsMiddleware',
]

ROOT_URLCONF = 'pwd_system.urls'

TEMPLATES = [
    {
        'BACKEND': 'django.template.backends.django.DjangoTemplates',
        'DIRS': [BASE_DIR / 'templates', BASE_DIR.parent / 'templates'],
        'APP_DIRS': True,
        'OPTIONS': {
            'context_processors': [
                'django.template.context_processors.debug',
                'django.template.context_processors.request',
                'django.contrib.auth.context_processors.auth',
                'django.contrib.messages.context_processors.messages',
                'core.context_processors.notifications',
            ],
        },
    },
]

WSGI_APPLICATION = 'pwd_system.wsgi.application'

_db_engine = os.environ.get('DB_ENGINE', '')
if _db_engine:
    DATABASES = {
        'default': {
            'ENGINE': _db_engine,
            'NAME': os.environ.get('DB_NAME', 'pwd_django'),
            'USER': os.environ.get('DB_USER', 'pwd_user'),
            'PASSWORD': os.environ.get('DB_PASSWORD', ''),
            'HOST': os.environ.get('DB_HOST', 'db'),
            'PORT': os.environ.get('DB_PORT', '3306'),
        }
    }
else:
    DATABASES = {
        'default': {
            'ENGINE': 'django.db.backends.sqlite3',
            'NAME': BASE_DIR / 'db.sqlite3',
        }
    }

AUTH_USER_MODEL = 'accounts.CustomUser'
AUTHENTICATION_BACKENDS = ['accounts.backends.PWDAuthBackend']

LOGIN_URL = '/login/'
LOGIN_REDIRECT_URL = '/'

AUTH_PASSWORD_VALIDATORS = [
    {'NAME': 'django.contrib.auth.password_validation.UserAttributeSimilarityValidator'},
    {'NAME': 'django.contrib.auth.password_validation.MinimumLengthValidator'},
    {'NAME': 'django.contrib.auth.password_validation.CommonPasswordValidator'},
    {'NAME': 'django.contrib.auth.password_validation.NumericPasswordValidator'},
]

LANGUAGE_CODE = 'en-us'
TIME_ZONE = 'Africa/Nairobi'
USE_I18N = True
USE_TZ = True

STATIC_URL = '/static/'
STATICFILES_DIRS = [BASE_DIR / 'static']
STATIC_ROOT = BASE_DIR / 'staticfiles'

STORAGES = {
    "default": {"BACKEND": "django.core.files.storage.FileSystemStorage"},
    "staticfiles": {"BACKEND": "whitenoise.storage.CompressedManifestStaticFilesStorage"},
}

MEDIA_URL = '/media/'
MEDIA_ROOT = BASE_DIR / 'media'

DEFAULT_AUTO_FIELD = 'django.db.models.BigAutoField'

MESSAGE_STORAGE = 'django.contrib.messages.storage.session.SessionStorage'

# ── Security ───────────────────────────────────────────────────────────────────
SECURE_SSL_REDIRECT = env_bool('SECURE_SSL_REDIRECT', not DEBUG)
SESSION_COOKIE_SECURE = env_bool('SESSION_COOKIE_SECURE', not DEBUG)
CSRF_COOKIE_SECURE = env_bool('CSRF_COOKIE_SECURE', not DEBUG)
SESSION_COOKIE_HTTPONLY = True
SESSION_COOKIE_SAMESITE = 'Lax'
CSRF_COOKIE_SAMESITE = 'Lax'
SECURE_HSTS_SECONDS = env_int('SECURE_HSTS_SECONDS', 31536000 if not DEBUG else 0, minimum=0)
SECURE_HSTS_INCLUDE_SUBDOMAINS = env_bool('SECURE_HSTS_INCLUDE_SUBDOMAINS', False)
SECURE_HSTS_PRELOAD = env_bool('SECURE_HSTS_PRELOAD', False)
SECURE_CONTENT_TYPE_NOSNIFF = True
SECURE_REFERRER_POLICY = 'same-origin'
X_FRAME_OPTIONS = 'DENY'

if env_bool('TRUST_X_FORWARDED_PROTO', not DEBUG):
    SECURE_PROXY_SSL_HEADER = ('HTTP_X_FORWARDED_PROTO', 'https')

DATA_UPLOAD_MAX_MEMORY_SIZE = env_int('DATA_UPLOAD_MAX_MEMORY_SIZE', 12 * 1024 * 1024, minimum=1024)
FILE_UPLOAD_MAX_MEMORY_SIZE = env_int('FILE_UPLOAD_MAX_MEMORY_SIZE', 10 * 1024 * 1024, minimum=1024)
MAX_DOCUMENT_UPLOAD_SIZE = env_int('MAX_DOCUMENT_UPLOAD_SIZE', 10 * 1024 * 1024, minimum=1024)
ALLOWED_DOCUMENT_EXTENSIONS = env_list('ALLOWED_DOCUMENT_EXTENSIONS', '.pdf,.png,.jpg,.jpeg')
ALLOWED_DOCUMENT_CONTENT_TYPES = env_list(
    'ALLOWED_DOCUMENT_CONTENT_TYPES',
    'application/pdf,image/png,image/jpeg',
)

LOGIN_MAX_ATTEMPTS = env_int('LOGIN_MAX_ATTEMPTS', 5, minimum=1)
LOGIN_LOCKOUT_SECONDS = env_int('LOGIN_LOCKOUT_SECONDS', 900, minimum=1)
CERTIFICATE_VERIFY_MAX_ATTEMPTS = env_int('CERTIFICATE_VERIFY_MAX_ATTEMPTS', 30, minimum=1)
CERTIFICATE_VERIFY_WINDOW_SECONDS = env_int('CERTIFICATE_VERIFY_WINDOW_SECONDS', 60, minimum=1)

# ── Chatbot RAG (retrieval + Claude) ───────────────────────────────────────────
# On by default only when an Anthropic credential is configured; without one the
# chatbot runs on its rule-based engine alone.
CHATBOT_RAG_ENABLED = env_bool(
    'CHATBOT_RAG_ENABLED',
    bool(os.environ.get('ANTHROPIC_API_KEY') or os.environ.get('ANTHROPIC_AUTH_TOKEN')),
)
CHATBOT_LLM_MODEL = os.environ.get('CHATBOT_LLM_MODEL', 'claude-opus-5')
CHATBOT_RAG_TIMEOUT_SECONDS = env_int('CHATBOT_RAG_TIMEOUT_SECONDS', 15, minimum=1)
CHATBOT_RAG_MAX_PER_WINDOW = env_int('CHATBOT_RAG_MAX_PER_WINDOW', 20, minimum=1)
CHATBOT_RAG_WINDOW_SECONDS = env_int('CHATBOT_RAG_WINDOW_SECONDS', 300, minimum=1)

# ── DocAssist (document Q&A service) ───────────────────────────────────────────
# Tried before Claude RAG for free-text chatbot questions. On only when an API
# key is set; the key is an organisation key created on DocAssist's /keys/ page.
DOCASSIST_BASE_URL = os.environ.get('DOCASSIST_BASE_URL', 'http://38.242.200.57:8001/api/v1')
DOCASSIST_API_KEY = os.environ.get('DOCASSIST_API_KEY', '')
DOCASSIST_ENABLED = env_bool('DOCASSIST_ENABLED', bool(DOCASSIST_API_KEY))
DOCASSIST_TIMEOUT_SECONDS = env_int('DOCASSIST_TIMEOUT_SECONDS', 20, minimum=1)

# ── Document Checker (scan quality / tamper screening) ─────────────────────────
# Runs on documents uploaded during medical review. On only when an API key is
# set. Configured by the national admin; these are the fallback defaults.
DOC_CHECKER_BASE_URL = os.environ.get('DOC_CHECKER_BASE_URL', '')
DOC_CHECKER_API_KEY = os.environ.get('DOC_CHECKER_API_KEY', '')
DOC_CHECKER_ENABLED = env_bool('DOC_CHECKER_ENABLED', bool(DOC_CHECKER_API_KEY))
DOC_CHECKER_TIMEOUT_SECONDS = env_int('DOC_CHECKER_TIMEOUT_SECONDS', 45, minimum=1)

LOG_LEVEL = os.environ.get('LOG_LEVEL', 'INFO').upper()
LOGGING = {
    'version': 1,
    'disable_existing_loggers': False,
    'filters': {
        'request_id': {'()': 'core.middleware.RequestIDLogFilter'},
    },
    'formatters': {
        'structured': {
            'format': 'timestamp={asctime} level={levelname} logger={name} request_id={request_id} message={message}',
            'style': '{',
        },
    },
    'handlers': {
        'console': {
            'class': 'logging.StreamHandler',
            'filters': ['request_id'],
            'formatter': 'structured',
        },
    },
    'root': {'handlers': ['console'], 'level': LOG_LEVEL},
}

# ── Email ──────────────────────────────────────────────────────────────────────
EMAIL_BACKEND = os.environ.get('EMAIL_BACKEND', 'django.core.mail.backends.console.EmailBackend')
EMAIL_HOST = os.environ.get('EMAIL_HOST', 'smtp.gmail.com')
EMAIL_PORT = int(os.environ.get('EMAIL_PORT', '587'))
EMAIL_USE_TLS = env_bool('EMAIL_USE_TLS', True)
EMAIL_HOST_USER = os.environ.get('EMAIL_HOST_USER', '')
EMAIL_HOST_PASSWORD = os.environ.get('EMAIL_HOST_PASSWORD', '')
DEFAULT_FROM_EMAIL = os.environ.get('DEFAULT_FROM_EMAIL', 'PWD Access Kenya <noreply@pwdaccess.go.ke>')

# ── Password Reset ─────────────────────────────────────────────────────────────
PASSWORD_RESET_TIMEOUT = 86400  # link valid for 24 hours
