@extends('layouts.app')

@section('content')
<h1>My Appointments</h1>

@if (session('status'))
    <div style="padding:8px;background:#d1e7dd;color:#0f5132;margin-bottom:12px;">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div style="padding:8px;background:#f8d7da;color:#842029;margin-bottom:12px;">
        {{ $errors->first() }}
    </div>
@endif

<h2>Upcoming</h2>
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;width:100%;">
    <tr>
        <th>Date/Time</th>
        <th>Provider / Service</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    @forelse ($upcoming as $a)
        <tr>
            <td>{{ \Carbon\Carbon::parse($a->start_at)->format('Y-m-d H:i') }}</td>
            <td>{{ $a->provider->name ?? 'Provider #'.$a->provider_id }} — {{ $a->service->name ?? 'Service #'.$a->service_id }}</td>
            <td>{{ ucfirst($a->status) }}</td>
            <td>
                <a href="{{ route('my.appointments.show', $a) }}">View</a>
                @can('reschedule', $a)
                    | <a href="{{ route('my.appointments.edit', $a) }}">Reschedule</a>
                @endcan
                @can('cancel', $a)
                    | <form method="POST" action="{{ route('my.appointments.cancel', $a) }}" class="inline" onsubmit="return confirm('Cancel this appointment?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="linky">Cancel</button>
                    </form>
                @endcan
            </td>
        </tr>
    @empty
        <tr><td colspan="4">No upcoming appointments.</td></tr>
    @endforelse
</table>

<div style="margin:8px 0;">
    {{ $upcoming->withQueryString()->links() }}
    </div>

<h2>Past</h2>
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;width:100%;">
    <tr>
        <th>Date/Time</th>
        <th>Provider / Service</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    @forelse ($past as $a)
        <tr>
            <td>{{ \Carbon\Carbon::parse($a->start_at)->format('Y-m-d H:i') }}</td>
            <td>{{ $a->provider->name ?? 'Provider #'.$a->provider_id }} — {{ $a->service->name ?? 'Service #'.$a->service_id }}</td>
            <td>{{ ucfirst($a->status) }}</td>
            <td>
                <a href="{{ route('my.appointments.show', $a) }}">View</a>
            </td>
        </tr>
    @empty
        <tr><td colspan="4">No past appointments.</td></tr>
    @endforelse
</table>
<div style="margin:8px 0;">
    {{ $past->withQueryString()->links() }}
</div>

@endsection

