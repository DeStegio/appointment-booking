#!/usr/bin/env bash
set -e
cd /var/www/html
mkdir -p storage/framework/{cache,sessions,testing,views}
chown -R www-data:www-data storage bootstrap/cache || true
find storage -type d -print0 | xargs -0 chmod 775 || true
find storage -type f -print0 | xargs -0 chmod 664 || true
chmod -R 775 bootstrap/cache || true
php artisan optimize:clear || true
php artisan view:cache || true

