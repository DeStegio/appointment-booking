<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tz = config('app.timezone');
        $todayStart = Carbon::now($tz)->startOfDay();
        $todayEnd = (clone $todayStart)->endOfDay();
        $weekStart = Carbon::now($tz)->startOfWeek();
        $weekEnd = (clone $weekStart)->endOfWeek();

        $totalProviders = User::providers()->count();
        $activeProviders = User::providers()->active()->count();
        $inactiveProviders = $totalProviders - $activeProviders;
        $customers = User::where('role', 'customer')->count();

        $appointmentsToday = Appointment::whereBetween('start_at', [$todayStart, $todayEnd])->count();
        $appointmentsThisWeek = Appointment::whereBetween('start_at', [$weekStart, $weekEnd])->count();

        return view('admin.dashboard', compact(
            'totalProviders', 'activeProviders', 'inactiveProviders', 'customers',
            'appointmentsToday', 'appointmentsThisWeek'
        ));
    }
}

