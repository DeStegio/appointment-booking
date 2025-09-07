#!/usr/bin/env bash
set -e
REBUILD=${REBUILD:-0}
FRESH=${FRESH:-0}
if [ "$REBUILD" = "1" ]; then docker compose up -d --build; else docker compose up -d; fi
docker compose exec app php artisan key:generate
if [ "$FRESH" = "1" ]; then docker compose exec app php artisan migrate:fresh --seed; else docker compose exec app php artisan migrate --seed; fi
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan route:cache && docker compose exec app php artisan view:cache && docker compose exec app php artisan config:cache

