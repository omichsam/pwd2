"""Effective Document Checker configuration: the admin-edited
`DocumentCheckerSettings` row, with environment variables as defaults.

Mirrors `core.chatbot_config`. The result is cached briefly so an upload does
not cost an extra query; saving the settings page clears the cache.
"""

import os
from dataclasses import dataclass

from django.conf import settings
from django.core.cache import cache
from django.core.signals import setting_changed
from django.db import DatabaseError
from django.dispatch import receiver

CACHE_KEY = 'doc-checker-config:v1'
CACHE_SECONDS = 30


@dataclass(frozen=True)
class DocCheckerConfig:
    enabled: bool
    base_url: str
    api_key: str
    key_source: str          # 'settings page', 'environment' or ''
    timeout_seconds: int
    from_database: bool

    @property
    def active(self):
        return bool(self.enabled and self.api_key and self.base_url)


def env_defaults():
    key = settings.DOC_CHECKER_API_KEY
    return DocCheckerConfig(
        enabled=bool(settings.DOC_CHECKER_ENABLED),
        base_url=settings.DOC_CHECKER_BASE_URL,
        api_key=key,
        key_source='environment' if key else '',
        timeout_seconds=settings.DOC_CHECKER_TIMEOUT_SECONDS,
        from_database=False,
    )


def _from_row(row):
    env = env_defaults()
    if row.api_key:
        key, source = row.api_key, 'settings page'
    else:
        key, source = env.api_key, env.key_source
    return DocCheckerConfig(
        enabled=row.enabled,
        base_url=row.base_url or env.base_url,
        api_key=key,
        key_source=source,
        timeout_seconds=row.timeout_seconds or env.timeout_seconds,
        from_database=True,
    )


def load_config():
    from .models import DocumentCheckerSettings
    try:
        row = DocumentCheckerSettings.objects.filter(pk=1).first()
    except DatabaseError:        # migrations not applied yet
        row = None
    return _from_row(row) if row else env_defaults()


def get_config():
    config = cache.get(CACHE_KEY)
    if config is None:
        config = load_config()
        cache.set(CACHE_KEY, config, CACHE_SECONDS)
    return config


def clear_cache():
    cache.delete(CACHE_KEY)


@receiver(setting_changed)
def _settings_changed(sender, setting, **kwargs):
    if setting.startswith('DOC_CHECKER_'):
        clear_cache()
