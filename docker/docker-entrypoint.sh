#!/usr/bin/env bash
set -euo pipefail

cd /var/www

# Volumes for var/, uploads and activity images are mounted at runtime and
# may be owned by root on first creation: reclaim them for the Apache user.
chown -R www-data:www-data var public/uploads public/images/activity 2>/dev/null || true

CONSOLE=(php bin/console --env="${APP_ENV:-prod}" --no-debug)

"${CONSOLE[@]}" cache:warmup
"${CONSOLE[@]}" assets:install public --relative
"${CONSOLE[@]}" ckeditor:install
"${CONSOLE[@]}" elfinder:install

if [ "${RUN_MIGRATIONS:-1}" = "1" ]; then
    "${CONSOLE[@]}" doctrine:migrations:migrate --no-interaction --allow-no-migration
fi

exec "$@"
