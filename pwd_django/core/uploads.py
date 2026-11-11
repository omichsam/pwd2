"""Validation helpers for user-supplied assessment documents."""

from pathlib import Path

from django.conf import settings
from django.core.exceptions import ValidationError


def validate_assessment_document(uploaded_file):
    extension = Path(uploaded_file.name).suffix.lower()
    if extension not in settings.ALLOWED_DOCUMENT_EXTENSIONS:
        raise ValidationError(
            f"Unsupported document type. Allowed extensions: "
            f"{', '.join(settings.ALLOWED_DOCUMENT_EXTENSIONS)}."
        )

    if uploaded_file.size > settings.MAX_DOCUMENT_UPLOAD_SIZE:
        size_mb = settings.MAX_DOCUMENT_UPLOAD_SIZE // (1024 * 1024)
        raise ValidationError(f"Document exceeds the {size_mb} MB upload limit.")

    content_type = getattr(uploaded_file, 'content_type', '')
    if content_type not in settings.ALLOWED_DOCUMENT_CONTENT_TYPES:
        raise ValidationError('The uploaded document content type is not allowed.')
