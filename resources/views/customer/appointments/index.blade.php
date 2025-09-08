@extends('layouts.app')

@section('content')
<h1 class="title">My Appointments</h1>

@if (session('status'))
    <div class="card mb-2">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="card mb-2">{{ $errors->first() }}</div>
@endif

<h2>Upcoming</h2>
<table class="table">
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
                <div class="inline-actions">
                <a class="btn btn-sm" href="{{ route('my.appointments.show', $a) }}">View</a>
                @can('reschedule', $a)
                    <a class="btn btn-sm" href="{{ route('my.appointments.edit', $a) }}">Reschedule</a>
                @endcan
                @can('cancel', $a)
                    <form method="POST" action="{{ route('my.appointments.cancel', $a) }}" onsubmit="return confirm('Cancel this appointment?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                    </form>
                @endcan
                </div>
            </td>
        </tr>
    @empty
        <tr><td colspan="4">No upcoming appointments.</td></tr>
    @endforelse
</table>

<div class="mt-2">
    {{ $upcoming->withQueryString()->links() }}
    </div>

<h2>Past</h2>
<table class="table">
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
<div class="mt-2">{{ $past->withQueryString()->links() }}</div>

@endsection
