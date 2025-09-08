#!/usr/bin/env sh
set -e

# Prepare writable dirs
mkdir -p "$VIEW_COMPILED_PATH" storage/framework/{cache,sessions,views} bootstrap/cache
chown -R www-data:www-data "$VIEW_COMPILED_PATH" storage bootstrap/cache || true
chmod -R 775 "$VIEW_COMPILED_PATH" storage bootstrap/cache || true

# Optional: clear stale compiled views owned by root
rm -rf storage/framework/views/* || true

# Start php-fpm
exec php-fpm -F

