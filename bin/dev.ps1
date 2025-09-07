param([switch]$Rebuild=$false,[switch]$Fresh=$false)
if ($Rebuild) { docker compose up -d --build } else { docker compose up -d }
docker compose exec app php artisan key:generate
if ($Fresh) { docker compose exec app php artisan migrate:fresh --seed } else { docker compose exec app php artisan migrate --seed }
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan route:cache; docker compose exec app php artisan view:cache; docker compose exec app php artisan config:cache

