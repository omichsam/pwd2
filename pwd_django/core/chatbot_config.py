"""Effective chatbot configuration: the admin-edited `ChatbotSettings` row, with
environment variables as defaults.

Every chatbot request reads `get_config()`. The result is cached briefly so a
chat message doesn't cost a query. Saving the settings page clears the cache in
that process; other worker processes pick up the change within CACHE_SECONDS.
"""

import os
from dataclasses import dataclass

from django.conf import settings
from django.core.cache import cache
from django.core.signals import setting_changed
from django.db import DatabaseError
from django.dispatch import receiver

CACHE_KEY = 'chatbot-config:v1'
CACHE_SECONDS = 30


@dataclass(frozen=True)
class ChatbotConfig:
    docassist_enabled: bool
    docassist_base_url: str
    docassist_api_key: str
    docassist_key_source: str        # 'settings page', 'environment' or ''
    docassist_timeout_seconds: int
    rag_enabled: bool
    anthropic_key_present: bool
    llm_model: str
    max_per_window: int
    window_seconds: int
    from_database: bool

    @property
    def docassist_active(self):
        return bool(self.docassist_enabled and self.docassist_api_key and self.docassist_base_url)

    @property
    def rag_active(self):
        return bool(self.rag_enabled and self.anthropic_key_present)


def _anthropic_key_present():
    return bool(os.environ.get('ANTHROPIC_API_KEY') or os.environ.get('ANTHROPIC_AUTH_TOKEN'))


def env_defaults():
    """Values used before the settings page is first saved."""
    key = settings.DOCASSIST_API_KEY
    return ChatbotConfig(
        docassist_enabled=bool(settings.DOCASSIST_ENABLED),
        docassist_base_url=settings.DOCASSIST_BASE_URL,
        docassist_api_key=key,
        docassist_key_source='environment' if key else '',
        docassist_timeout_seconds=settings.DOCASSIST_TIMEOUT_SECONDS,
        rag_enabled=bool(settings.CHATBOT_RAG_ENABLED),
        anthropic_key_present=_anthropic_key_present(),
        llm_model=settings.CHATBOT_LLM_MODEL,
        max_per_window=settings.CHATBOT_RAG_MAX_PER_WINDOW,
        window_seconds=settings.CHATBOT_RAG_WINDOW_SECONDS,
        from_database=False,
    )


def _from_row(row):
    env = env_defaults()
    if row.docassist_api_key:
        key, source = row.docassist_api_key, 'settings page'
    else:
        key, source = env.docassist_api_key, env.docassist_key_source
    return ChatbotConfig(
        docassist_enabled=row.docassist_enabled,
        docassist_base_url=row.docassist_base_url or env.docassist_base_url,
        docassist_api_key=key,
        docassist_key_source=source,
        docassist_timeout_seconds=row.docassist_timeout_seconds or env.docassist_timeout_seconds,
        rag_enabled=row.rag_enabled,
        anthropic_key_present=env.anthropic_key_present,
        llm_model=row.llm_model or env.llm_model,
        max_per_window=row.max_per_window or env.max_per_window,
        window_seconds=row.window_seconds or env.window_seconds,
        from_database=True,
    )


def load_config():
    """Read the configuration without the cache."""
    from .models import ChatbotSettings
    try:
        row = ChatbotSettings.objects.filter(pk=1).first()
    except DatabaseError:        # e.g. migrations not applied yet
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
    """`override_settings` in tests must not be masked by a cached config."""
    if setting.startswith(('DOCASSIST_', 'CHATBOT_')):
        clear_cache()
