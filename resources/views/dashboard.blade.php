@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="title mb-1">Dashboard</h1>
    <p>Welcome, <strong>{{ $user->name ?? 'User' }}</strong>!</p>

    @if (session('status'))
        <div class="card mt-2">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="card mt-2">{{ $errors->first() }}</div>
    @endif

    <div class="mt-2">
        <p>Your role: <code>{{ $user->role ?? 'n/a' }}</code></p>
        @if (($user->role ?? null) === 'provider')
            <div class="inline-actions mt-1">
                <a class="btn btn-sm focus-ring" href="{{ route('provider.services.index') }}">Services</a>
                <a class="btn btn-sm focus-ring" href="{{ route('provider.schedules.index') }}">My Schedules</a>
            </div>
        @endif
    </div>

    @php
        use App\Models\Appointment;
        $now = now();
    @endphp

    @if (($user->role ?? null) === 'customer')
        <h2 class="title mt-2">My Upcoming Appointments</h2>
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
            <div class="table-responsive"><table class="table">
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
                                $badgeClass = [
                                    'pending' => 'badge',
                                    'confirmed' => 'badge-success',
                                    'cancelled' => 'badge-danger',
                                    'completed' => 'badge-success',
                                ][$status] ?? 'badge';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $appt->status }}</span>
                        </td>
                        <td>
                            @if (in_array(strtolower((string) $appt->status), ['pending','confirmed'], true) && (int) $appt->customer_id === (int) ($user->id ?? 0))
                                <form method="POST" action="{{ route('appointments.cancel', $appt) }}" class="inline-actions">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm focus-ring">Cancel</button>
                                </form>
                            @else
                                <span class="badge">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table></div>
        @endif
    @endif

    @if (($user->role ?? null) === 'provider')
        <h2 class="title mt-2">Upcoming Appointments (My Clients)</h2>
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
            <div class="table-responsive"><table class="table">
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
                                $badgeClass = [
                                    'pending' => 'badge',
                                    'confirmed' => 'badge-success',
                                    'cancelled' => 'badge-danger',
                                    'completed' => 'badge-success',
                                ][$status] ?? 'badge';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $appt->status }}</span>
                        </td>
                        <td>
                            @php $status = strtolower((string) $appt->status); @endphp
                            @if ((int) $appt->provider_id === (int) ($user->id ?? 0) && $status === 'pending')
                                <form method="POST" action="{{ route('appointments.confirm', $appt) }}" class="inline-actions">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-primary btn-sm focus-ring">Confirm</button>
                                </form>
                                <form method="POST" action="{{ route('appointments.cancel', $appt) }}" class="inline-actions">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm focus-ring">Cancel</button>
                                </form>
                            @elseif ((int) $appt->provider_id === (int) ($user->id ?? 0) && $status === 'confirmed')
                                <form method="POST" action="{{ route('appointments.complete', $appt) }}" class="inline-actions">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-primary btn-sm focus-ring">Complete</button>
                                </form>
                                <form method="POST" action="{{ route('appointments.cancel', $appt) }}" class="inline-actions">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm focus-ring">Cancel</button>
                                </form>
                            @else
                                <span class="badge">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table></div>
        @endif
    @endif
</div>
@endsection
