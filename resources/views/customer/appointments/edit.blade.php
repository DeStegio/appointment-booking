@extends('layouts.app')

@section('content')
<h1 class="title">Reschedule Appointment</h1>

@if ($errors->any())
    <div class="card mb-2">{{ $errors->first() }}</div>
@endif

<p><strong>Provider:</strong> {{ $appointment->provider->name ?? ('#'.$appointment->provider_id) }}</p>
<p><strong>Service:</strong> {{ $service->name ?? ('#'.$appointment->service_id) }} ({{ $service->duration_minutes }}m)</p>

<form method="GET" action="{{ route('my.appointments.edit', $appointment) }}" class="mb-2 inline-actions">
    <div class="form-group">
        <label class="form-label" for="date">Date</label>
        <input class="form-control" type="date" id="date" name="date" value="{{ $date }}">
    </div>
    <button class="btn btn-primary btn-sm focus-ring" type="submit">Change date</button>
    <a class="link" href="{{ route('my.appointments.show', $appointment) }}">Cancel</a>
    </form>

<form method="POST" action="{{ route('my.appointments.update', $appointment) }}">
    @csrf
    @method('PATCH')
    <input type="hidden" name="date" value="{{ $date }}" />

    @if (empty($slots))
        <p>No available slots for {{ $date }}.</p>
    @else
        <div class="mt-2">
            @foreach ($slots as $slot)
                <div>
                    <label>
                        <input type="radio" name="start_at" value="{{ $slot }}" {{ old('start_at')===$slot ? 'checked' : '' }}>
                        {{ \Carbon\Carbon::parse($slot)->format('H:i') }}
                    </label>
                </div>
            @endforeach
        </div>
        <button class="btn btn-primary btn-sm focus-ring" type="submit">Reschedule</button>
    @endif
    </form>

@endsection
