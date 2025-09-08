@extends('layouts.app')

@section('content')
<h1>Appointments</h1>

@if (session('status'))
    <div style="padding:8px;background:#d1e7dd;color:#0f5132;margin-bottom:12px;">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div style="padding:8px;background:#f8d7da;color:#842029;margin-bottom:12px;">
        {{ $errors->first() }}
    </div>
@endif

<h2>Today</h2>
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;width:100%;">
    <tr>
        <th>Time</th>
        <th>Customer</th>
        <th>Service</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    @forelse ($today as $a)
        <tr>
            <td>{{ \Carbon\Carbon::parse($a->start_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($a->end_at)->format('H:i') }}</td>
            <td>{{ $a->customer->name ?? 'Customer #'.$a->customer_id }} <small>({{ $a->customer->email ?? '' }})</small></td>
            <td>{{ $a->service->name ?? 'Service #'.$a->service_id }}</td>
            <td>{{ ucfirst($a->status) }}</td>
            <td>
                @can('confirm', $a)
                    <form method="POST" action="{{ route('appointments.confirm', $a) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit">Confirm</button>
                    </form>
                @endcan
                @can('complete', $a)
                    <form method="POST" action="{{ route('appointments.complete', $a) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit">Complete</button>
                    </form>
                @endcan
                @can('cancel', $a)
                    <form method="POST" action="{{ route('appointments.cancel', $a) }}" class="inline" onsubmit="return confirm('Cancel this appointment?');">
                        @csrf @method('PATCH')
                        <button type="submit" class="linky">Cancel</button>
                    </form>
                @endcan
            </td>
        </tr>
    @empty
        <tr><td colspan="5">No appointments today.</td></tr>
    @endforelse
</table>

<h2 style="margin-top:20px;">Upcoming</h2>
@forelse ($upcoming as $date => $items)
    <h3>{{ $date }}</h3>
    <table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;width:100%;margin-bottom:10px;">
        <tr>
            <th>Time</th>
            <th>Customer</th>
            <th>Service</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        @foreach ($items as $a)
        <tr>
            <td>{{ \Carbon\Carbon::parse($a->start_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($a->end_at)->format('H:i') }}</td>
            <td>{{ $a->customer->name ?? 'Customer #'.$a->customer_id }} <small>({{ $a->customer->email ?? '' }})</small></td>
            <td>{{ $a->service->name ?? 'Service #'.$a->service_id }}</td>
            <td>{{ ucfirst($a->status) }}</td>
            <td>
                @can('confirm', $a)
                    <form method="POST" action="{{ route('appointments.confirm', $a) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit">Confirm</button>
                    </form>
                @endcan
                @can('complete', $a)
                    <form method="POST" action="{{ route('appointments.complete', $a) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit">Complete</button>
                    </form>
                @endcan
                @can('cancel', $a)
                    <form method="POST" action="{{ route('appointments.cancel', $a) }}" class="inline" onsubmit="return confirm('Cancel this appointment?');">
                        @csrf @method('PATCH')
                        <button type="submit" class="linky">Cancel</button>
                    </form>
                @endcan
            </td>
        </tr>
        @endforeach
    </table>
@empty
    <p>No upcoming appointments.</p>
@endforelse

@endsection

