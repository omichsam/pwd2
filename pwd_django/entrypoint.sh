#!/bin/sh
set -eu

db_host="${DB_HOST:-db}"
db_port="${DB_PORT:-3306}"
wait_timeout="${DB_WAIT_TIMEOUT:-60}"

case "$wait_timeout" in
  ''|*[!0-9]*) echo "ERROR: DB_WAIT_TIMEOUT must be a positive integer." >&2; exit 2 ;;
  0) echo "ERROR: DB_WAIT_TIMEOUT must be greater than zero." >&2; exit 2 ;;
esac

if [ -n "${DB_ENGINE:-}" ]; then
  echo "Waiting up to ${wait_timeout}s for database at ${db_host}:${db_port}..."
  elapsed=0
  until nc -z "$db_host" "$db_port"; do
    elapsed=$((elapsed + 1))
    if [ "$elapsed" -ge "$wait_timeout" ]; then
      echo "ERROR: Database ${db_host}:${db_port} was unavailable after ${wait_timeout}s." >&2
      exit 1
    fi
    sleep 1
  done
  echo "Database connection available."
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  echo "Applying database migrations..."
  python manage.py migrate --noinput
fi

if [ "${COLLECT_STATIC:-false}" = "true" ]; then
  echo "Collecting static files..."
  python manage.py collectstatic --noinput --clear
fi

exec "$@"
