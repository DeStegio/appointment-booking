@extends('layouts.app')

@section('content')
<div class="container" style="max-width:720px;margin:0 auto;">
    <h1 style="margin-bottom:0.5rem;">Dashboard</h1>
    <p>Welcome, <strong>{{ $user->name ?? 'User' }}</strong>!</p>

    @if (session('status'))
        <div style="padding:8px 10px;margin:10px 0;border:1px solid #b2dfdb;background:#e0f2f1;color:#004d40;">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div style="padding:8px 10px;margin:10px 0;border:1px solid #ffcdd2;background:#ffebee;color:#b71c1c;">
            {{ $errors->first() }}
        </div>
    @endif

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
                    <th>Actions</th>
                </tr>
                @foreach ($appointments as $appt)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($appt->start_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $appt->provider->name ?? '—' }}</td>
                        <td>{{ $appt->service->name ?? '—' }}</td>
                        <td>
                            @php
                                $status = strtolower((string) $appt->status);
                                $badgeBg = [
                                    'pending' => '#eceff1',
                                    'confirmed' => '#e3f2fd',
                                    'cancelled' => '#ffebee',
                                    'completed' => '#e8f5e9',
                                ][$status] ?? '#eceff1';
                                $badgeColor = [
                                    'pending' => '#37474f',
                                    'confirmed' => '#0d47a1',
                                    'cancelled' => '#b71c1c',
                                    'completed' => '#1b5e20',
                                ][$status] ?? '#37474f';
                            @endphp
                            <span style="padding:2px 6px;border-radius:10px;background:{{ $badgeBg }};color:{{ $badgeColor }};font-size:12px;">
                                {{ $appt->status }}
                            </span>
                        </td>
                        <td>
                            @if (in_array(strtolower((string) $appt->status), ['pending','confirmed'], true) && (int) $appt->customer_id === (int) ($user->id ?? 0))
                                <form method="POST" action="{{ route('appointments.cancel', $appt) }}" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" style="padding:4px 8px;font-size:12px;">Cancel</button>
                                </form>
                            @else
                                <span style="color:#9e9e9e;font-size:12px;">—</span>
                            @endif
                        </td>
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
                    <th>Actions</th>
                </tr>
                @foreach ($appointments as $appt)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($appt->start_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $appt->customer->name ?? '—' }}</td>
                        <td>{{ $appt->service->name ?? '—' }}</td>
                        <td>
                            @php
                                $status = strtolower((string) $appt->status);
                                $badgeBg = [
                                    'pending' => '#eceff1',
                                    'confirmed' => '#e3f2fd',
                                    'cancelled' => '#ffebee',
                                    'completed' => '#e8f5e9',
                                ][$status] ?? '#eceff1';
                                $badgeColor = [
                                    'pending' => '#37474f',
                                    'confirmed' => '#0d47a1',
                                    'cancelled' => '#b71c1c',
                                    'completed' => '#1b5e20',
                                ][$status] ?? '#37474f';
                            @endphp
                            <span style="padding:2px 6px;border-radius:10px;background:{{ $badgeBg }};color:{{ $badgeColor }};font-size:12px;">
                                {{ $appt->status }}
                            </span>
                        </td>
                        <td>
                            @php $status = strtolower((string) $appt->status); @endphp
                            @if ((int) $appt->provider_id === (int) ($user->id ?? 0) && $status === 'pending')
                                <form method="POST" action="{{ route('appointments.confirm', $appt) }}" style="display:inline;margin-right:4px;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" style="padding:4px 8px;font-size:12px;">Confirm</button>
                                </form>
                                <form method="POST" action="{{ route('appointments.cancel', $appt) }}" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" style="padding:4px 8px;font-size:12px;">Cancel</button>
                                </form>
                            @elseif ((int) $appt->provider_id === (int) ($user->id ?? 0) && $status === 'confirmed')
                                <form method="POST" action="{{ route('appointments.complete', $appt) }}" style="display:inline;margin-right:4px;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" style="padding:4px 8px;font-size:12px;">Complete</button>
                                </form>
                                <form method="POST" action="{{ route('appointments.cancel', $appt) }}" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" style="padding:4px 8px;font-size:12px;">Cancel</button>
                                </form>
                            @else
                                <span style="color:#9e9e9e;font-size:12px;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif
    @endif
</div>
@endsection
