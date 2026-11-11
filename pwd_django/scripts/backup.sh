#!/bin/sh
set -eu

: "${BACKUP_DIR:?Set BACKUP_DIR to a dedicated backup directory}"
: "${BACKUP_ENCRYPTION_PASSWORD:?Set BACKUP_ENCRYPTION_PASSWORD}"
: "${DB_NAME:?Set DB_NAME}"
: "${DB_USER:?Set DB_USER}"
: "${DB_PASSWORD:?Set DB_PASSWORD}"

db_host="${DB_HOST:-db}"
db_port="${DB_PORT:-3306}"
media_dir="${MEDIA_DIR:-/app/media}"
timestamp="$(date -u +%Y%m%dT%H%M%SZ)"
backup_set="${BACKUP_DIR%/}/pwd-access-${timestamp}"

mkdir -p "$backup_set"
umask 077

echo "Creating encrypted database backup..."
MYSQL_PWD="$DB_PASSWORD" mysqldump \
  --host="$db_host" --port="$db_port" --user="$DB_USER" \
  --single-transaction --routines --triggers "$DB_NAME" \
  | gzip \
  | openssl enc -aes-256-cbc -salt -pbkdf2 \
      -pass env:BACKUP_ENCRYPTION_PASSWORD \
      -out "$backup_set/database.sql.gz.enc"

echo "Creating encrypted media backup..."
tar -C "$media_dir" -czf - . \
  | openssl enc -aes-256-cbc -salt -pbkdf2 \
      -pass env:BACKUP_ENCRYPTION_PASSWORD \
      -out "$backup_set/media.tar.gz.enc"

(
  cd "$backup_set"
  sha256sum database.sql.gz.enc media.tar.gz.enc > SHA256SUMS
)

echo "Backup completed: $backup_set"
