@extends('layouts.app')

@section('content')
<h1 class="title">Appointments</h1>

@if (session('status'))
    <div class="card mb-2">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="card mb-2">{{ $errors->first() }}</div>
@endif

<h2>Today</h2>
<table class="table">
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
                    <form method="POST" action="{{ route('appointments.confirm', $a) }}" class="inline-actions">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-primary btn-sm">Confirm</button>
                    </form>
                @endcan
                @can('complete', $a)
                    <form method="POST" action="{{ route('appointments.complete', $a) }}" class="inline-actions">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-primary btn-sm">Complete</button>
                    </form>
                @endcan
                @can('cancel', $a)
                    <form method="POST" action="{{ route('appointments.cancel', $a) }}" class="inline-actions" onsubmit="return confirm('Cancel this appointment?');">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                    </form>
                @endcan
            </td>
        </tr>
    @empty
        <tr><td colspan="5">No appointments today.</td></tr>
    @endforelse
</table>

<h2 class="title mt-2">Upcoming</h2>
@forelse ($upcoming as $date => $items)
    <h3>{{ $date }}</h3>
    <table class="table">
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
                    <form method="POST" action="{{ route('appointments.confirm', $a) }}" class="inline-actions">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-primary btn-sm">Confirm</button>
                    </form>
                @endcan
                @can('complete', $a)
                    <form method="POST" action="{{ route('appointments.complete', $a) }}" class="inline-actions">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-primary btn-sm">Complete</button>
                    </form>
                @endcan
                @can('cancel', $a)
                    <form method="POST" action="{{ route('appointments.cancel', $a) }}" class="inline-actions" onsubmit="return confirm('Cancel this appointment?');">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
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
