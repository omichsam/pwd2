#!/bin/sh
set -eu

: "${BACKUP_SET:?Set BACKUP_SET to one exact backup-set directory}"
: "${BACKUP_ENCRYPTION_PASSWORD:?Set BACKUP_ENCRYPTION_PASSWORD}"
: "${RESTORE_CONFIRM:?Set RESTORE_CONFIRM to the backup-set directory name}"
: "${DB_NAME:?Set DB_NAME to the restore target database}"
: "${DB_USER:?Set DB_USER}"
: "${DB_PASSWORD:?Set DB_PASSWORD}"

expected_confirmation="$(basename "$BACKUP_SET")"
if [ "$RESTORE_CONFIRM" != "$expected_confirmation" ]; then
  echo "ERROR: RESTORE_CONFIRM must equal '$expected_confirmation'." >&2
  exit 2
fi

if [ ! -f "$BACKUP_SET/database.sql.gz.enc" ] || [ ! -f "$BACKUP_SET/media.tar.gz.enc" ]; then
  echo "ERROR: Backup set is incomplete." >&2
  exit 2
fi

(
  cd "$BACKUP_SET"
  sha256sum -c SHA256SUMS
)

db_host="${DB_HOST:-db}"
db_port="${DB_PORT:-3306}"
media_dir="${MEDIA_DIR:-/app/media}"

echo "Restoring database into explicitly selected target '$DB_NAME'..."
openssl enc -d -aes-256-cbc -pbkdf2 \
  -pass env:BACKUP_ENCRYPTION_PASSWORD \
  -in "$BACKUP_SET/database.sql.gz.enc" \
  | gzip -d \
  | MYSQL_PWD="$DB_PASSWORD" mysql \
      --host="$db_host" --port="$db_port" --user="$DB_USER" "$DB_NAME"

echo "Restoring media files into '$media_dir'..."
mkdir -p "$media_dir"
openssl enc -d -aes-256-cbc -pbkdf2 \
  -pass env:BACKUP_ENCRYPTION_PASSWORD \
  -in "$BACKUP_SET/media.tar.gz.enc" \
  | tar -C "$media_dir" -xzf -

echo "Restore completed. Run application checks against the restored target."
