@extends('layouts.app')

@section('content')
<h1 class="title">Appointment Details</h1>

@if (session('status'))
    <div class="card mb-2">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="card mb-2">{{ $errors->first() }}</div>
@endif

<p><strong>Provider:</strong> {{ $appointment->provider->name ?? ('#'.$appointment->provider_id) }}</p>
<p><strong>Service:</strong> {{ $appointment->service->name ?? ('#'.$appointment->service_id) }}</p>
<p><strong>Start:</strong> {{ \Carbon\Carbon::parse($appointment->start_at)->format('Y-m-d H:i') }}</p>
<p><strong>End:</strong> {{ \Carbon\Carbon::parse($appointment->end_at)->format('Y-m-d H:i') }}</p>
<p><strong>Status:</strong> {{ ucfirst($appointment->status) }}</p>

<p class="inline-actions">
    <a class="btn btn-sm focus-ring" href="{{ route('my.appointments.index') }}">Back to list</a>
    @can('reschedule', $appointment)
        <a class="btn btn-sm focus-ring" href="{{ route('my.appointments.edit', $appointment) }}">Reschedule</a>
    @endcan
    @can('cancel', $appointment)
        <form method="POST" action="{{ route('my.appointments.cancel', $appointment) }}" data-confirm="Cancel this appointment?">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger btn-sm focus-ring">Cancel</button>
        </form>
    @endcan
    </p>

@endsection
