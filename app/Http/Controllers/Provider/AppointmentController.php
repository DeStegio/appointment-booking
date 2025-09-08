<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:provider']);
    }

    public function index()
    {
        $providerId = (int) Auth::id();
        $tz = config('app.timezone');
        $startToday = Carbon::now($tz)->startOfDay();
        $endToday = (clone $startToday)->endOfDay();

        $today = Appointment::query()
            ->with(['customer', 'service'])
            ->where('provider_id', $providerId)
            ->whereBetween('start_at', [$startToday, $endToday])
            ->orderBy('start_at')
            ->get();

        $upcoming = Appointment::query()
            ->with(['customer', 'service'])
            ->where('provider_id', $providerId)
            ->where('start_at', '>', $endToday)
            ->orderBy('start_at')
            ->get()
            ->groupBy(function ($a) use ($tz) {
                $start = $a->start_at instanceof \DateTimeInterface ? Carbon::instance($a->start_at) : Carbon::parse((string) $a->start_at, $tz);
                return $start->toDateString();
            });

        return view('provider.appointments.index', compact('today', 'upcoming'));
    }
}

