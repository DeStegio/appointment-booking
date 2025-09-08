#!/usr/bin/env sh
set -e

# prepare compiled dir every start
mkdir -p "${VIEW_COMPILED_PATH:-/tmp/laravel-views}"
chmod 1777 "${VIEW_COMPILED_PATH:-/tmp/laravel-views}" || true

# optional: clear stale compiled views
rm -rf "${VIEW_COMPILED_PATH:-/tmp/laravel-views}"/* 2>/dev/null || true

# warm caches to reduce first-hit latency
php artisan view:clear >/dev/null 2>&1 || true
php artisan view:cache >/dev/null 2>&1 || true

# start PHP-FPM in foreground
exec php-fpm -F
