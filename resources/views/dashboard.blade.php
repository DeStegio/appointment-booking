@extends('layouts.app')

@section('content')
<div class="container" style="max-width:720px;margin:0 auto;">
    <h1 style="margin-bottom:0.5rem;">Dashboard</h1>
    <p>Welcome, <strong>{{ $user->name ?? 'User' }}</strong>!</p>

    <div style="margin-top:1rem;">
        <p>Your role: <code>{{ $user->role ?? 'n/a' }}</code></p>
        @if (($user->role ?? null) === 'provider')
            <div style="margin-top:0.5rem;">
                <a href="{{ route('provider.services.index') }}" style="margin-right:0.75rem;">Services</a>
                <a href="{{ route('provider.schedules.index') }}">My Schedules</a>
            </div>
        @endif
    </div>

    @php
        use App\Models\Appointment;
        $now = now();
    @endphp

    @if (($user->role ?? null) === 'customer')
        <h2 style="margin-top:1.25rem;">My Upcoming Appointments</h2>
        @php
            $appointments = Appointment::with(['provider', 'service'])
                ->where('customer_id', $user->id)
                ->where('start_at', '>=', $now)
                ->orderBy('start_at')
                ->limit(10)
                ->get();
        @endphp
        @if ($appointments->isEmpty())
            <p>No upcoming appointments.</p>
        @else
            <table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse; width:100%;">
                <tr>
                    <th>Start</th>
                    <th>Provider</th>
                    <th>Service</th>
                    <th>Status</th>
                </tr>
                @foreach ($appointments as $appt)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($appt->start_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $appt->provider->name ?? '—' }}</td>
                        <td>{{ $appt->service->name ?? '—' }}</td>
                        <td>{{ $appt->status }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    @endif

    @if (($user->role ?? null) === 'provider')
        <h2 style="margin-top:1.25rem;">Upcoming Appointments (My Clients)</h2>
        @php
            $appointments = Appointment::with(['customer', 'service'])
                ->where('provider_id', $user->id)
                ->where('start_at', '>=', $now)
                ->orderBy('start_at')
                ->limit(10)
                ->get();
        @endphp
        @if ($appointments->isEmpty())
            <p>No upcoming appointments.</p>
        @else
            <table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse; width:100%;">
                <tr>
                    <th>Start</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Status</th>
                </tr>
                @foreach ($appointments as $appt)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($appt->start_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $appt->customer->name ?? '—' }}</td>
                        <td>{{ $appt->service->name ?? '—' }}</td>
                        <td>{{ $appt->status }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    @endif
</div>
@endsection
