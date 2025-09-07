## Appointment Booking — Local Dev Quickstart

This is a minimal appointment booking demo (users, providers, services, schedules, time-offs, and appointments) with Docker-based local setup and demo seed data.

### Prereqs

- Docker Desktop

### Quickstart

```bash
git clone <repo>
cd appointment-booking
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

The app seeds demo users, providers/services, weekly schedules (Mon–Fri 09:00–17:00), time-offs, and a few upcoming appointments.

### Login Accounts

- Admin: admin@example.com / password
- Providers: provider1@example.com / password, provider2@example.com / password
- Customers: customer1@example.com / password, customer2@example.com / password

### URLs

- App: http://localhost:8080
- Providers list: /providers
- Slots page: /providers/{provider}/services/{service}/slots?date=YYYY-MM-DD
- Admin: /admin (admin only)

### Common Commands

- Clear & cache:
  `docker compose exec app php artisan optimize:clear && docker compose exec app php artisan route:cache && docker compose exec app php artisan view:cache && docker compose exec app php artisan config:cache`

### Troubleshooting

If you hit permissions errors on `storage` or `bootstrap/cache`, run:

```
docker exec -it laravel-app bash -lc 'mkdir -p storage/framework/{cache,sessions,testing,views}; chown -R www-data:www-data storage bootstrap/cache; find storage -type d -exec chmod 775 {} \;; find storage -type f -exec chmod 664 {} \;; chmod -R 775 bootstrap/cache; php artisan optimize:clear; php artisan view:cache'
```

### Architecture (Short)

- Entities: User, Service, ProviderSchedule, TimeOff, Appointment
- Flows:
  - Providers manage services, schedules (Mon–Fri slots), and time-offs.
  - Customers browse providers, view service slots by date, and book.
  - Appointments respect provider schedules, service durations, time-offs, and lead time/window from `config/appointments.php`.
