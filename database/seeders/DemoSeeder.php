<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\ProviderSchedule;
use App\Models\Service;
use App\Models\TimeOff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Users
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Provider One',
                'email' => 'provider1@example.com',
                'role' => 'provider',
            ],
            [
                'name' => 'Provider Two',
                'email' => 'provider2@example.com',
                'role' => 'provider',
            ],
            [
                'name' => 'Customer One',
                'email' => 'customer1@example.com',
                'role' => 'customer',
            ],
            [
                'name' => 'Customer Two',
                'email' => 'customer2@example.com',
                'role' => 'customer',
            ],
        ];

        $byEmail = [];
        foreach ($users as $u) {
            $byEmail[$u['email']] = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'role' => $u['role'],
                    'is_active' => true,
                ]
            );
        }

        $providers = [
            $byEmail['provider1@example.com'],
            $byEmail['provider2@example.com'],
        ];
        $customers = [
            $byEmail['customer1@example.com'],
            $byEmail['customer2@example.com'],
        ];

        // 2) Services (2–3 each)
        $servicesByProvider = [];
        foreach ($providers as $idx => $provider) {
            $svcPayloads = [
                ['name' => 'Consultation', 'duration_minutes' => 30, 'price' => 30.00],
                ['name' => 'Follow-up',    'duration_minutes' => 45, 'price' => 45.00],
                ['name' => 'Extended Care','duration_minutes' => 60, 'price' => 60.00],
            ];
            $services = [];
            foreach ($svcPayloads as $p) {
                $services[] = Service::updateOrCreate(
                    [
                        'provider_id' => $provider->id,
                        'name' => $p['name'],
                    ],
                    [
                        'duration_minutes' => $p['duration_minutes'],
                        'price' => $p['price'],
                        'is_active' => true,
                    ]
                );
            }
            $servicesByProvider[$provider->id] = $services;
        }

        // 3) Weekly schedules Mon–Fri 09:00–17:00, 30-minute slots
        foreach ($providers as $provider) {
            for ($weekday = 1; $weekday <= 5; $weekday++) { // 1=Mon .. 5=Fri
                ProviderSchedule::updateOrCreate(
                    [
                        'provider_id' => $provider->id,
                        'weekday' => $weekday,
                    ],
                    [
                        'start_time' => '09:00:00',
                        'end_time' => '17:00:00',
                        'slot_interval_minutes' => 30,
                    ]
                );
            }
        }

        // 4) Time-offs in the next 10 days (1–2 per provider)
        foreach ($providers as $i => $provider) {
            $count = $i === 0 ? 2 : 1; // 2 for first provider, 1 for second
            for ($k = 0; $k < $count; $k++) {
                $dayOffset = 1 + ($k * 3); // spread out
                $date = $this->nextBusinessDay(Carbon::today()->addDays($dayOffset));
                $start = Carbon::create($date->year, $date->month, $date->day, 12, 0, 0);
                $end   = (clone $start)->addHours(2);

                TimeOff::updateOrCreate(
                    [
                        'provider_id' => $provider->id,
                        'start_at' => $start,
                    ],
                    [
                        'end_at' => $end,
                        'reason' => 'Personal time',
                    ]
                );
            }
        }

        // 5) A few upcoming appointments (pending/confirmed), non-overlapping
        $now = Carbon::now();
        foreach ($providers as $idx => $provider) {
            $services = $servicesByProvider[$provider->id] ?? [];
            if (count($services) === 0) {
                continue;
            }

            // Use next two business days at different times
            $d1 = $this->nextBusinessDay((clone $now)->addDays(1));
            $d2 = $this->nextBusinessDay((clone $now)->addDays(2));

            // Appointment 1
            $svc1 = $services[0];
            $start1 = Carbon::create($d1->year, $d1->month, $d1->day, 10, 0, 0);
            $end1   = (clone $start1)->addMinutes((int) $svc1->duration_minutes);
            if (!$this->overlapsTimeOff($provider->id, $start1, $end1)) {
                $this->createAppointment($provider->id, $customers[0]->id, $svc1->id, $start1, $end1, 'pending');
            }

            // Appointment 2, later day/time, different service
            $svc2 = $services[2] ?? $services[0];
            $start2 = Carbon::create($d2->year, $d2->month, $d2->day, 14, 30, 0);
            $end2   = (clone $start2)->addMinutes((int) $svc2->duration_minutes);
            if (!$this->overlapsTimeOff($provider->id, $start2, $end2)) {
                $this->createAppointment($provider->id, $customers[1]->id, $svc2->id, $start2, $end2, 'confirmed');
            }
        }
    }

    private function nextBusinessDay(Carbon $date): Carbon
    {
        // Ensure Mon-Fri (1..5)
        while ((int) $date->dayOfWeekIso > 5) {
            $date->addDay();
        }
        return $date;
    }

    private function overlapsTimeOff(int $providerId, Carbon $start, Carbon $end): bool
    {
        return TimeOff::where('provider_id', $providerId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_at', [$start, $end])
                  ->orWhereBetween('end_at', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_at', '<=', $start)
                         ->where('end_at', '>=', $end);
                  });
            })->exists();
    }

    private function createAppointment(int $providerId, int $customerId, int $serviceId, Carbon $start, Carbon $end, string $status = 'pending'): void
    {
        // Guard against overlaps with existing appointments
        $overlapsExisting = Appointment::where('provider_id', $providerId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_at', [$start, $end])
                  ->orWhereBetween('end_at', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_at', '<=', $start)
                         ->where('end_at', '>=', $end);
                  });
            })->exists();

        if ($overlapsExisting) {
            return;
        }

        Appointment::create([
            'provider_id' => $providerId,
            'customer_id' => $customerId,
            'service_id'  => $serviceId,
            'start_at'    => $start,
            'end_at'      => $end,
            'status'      => $status,
            'notes'       => null,
        ]);
    }
}

