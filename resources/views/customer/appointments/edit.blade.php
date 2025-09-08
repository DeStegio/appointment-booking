@extends('layouts.app')

@section('content')
<h1>Reschedule Appointment</h1>

@if ($errors->any())
    <div style="padding:8px;background:#f8d7da;color:#842029;margin-bottom:12px;">
        {{ $errors->first() }}
    </div>
@endif

<p><strong>Provider:</strong> {{ $appointment->provider->name ?? ('#'.$appointment->provider_id) }}</p>
<p><strong>Service:</strong> {{ $service->name ?? ('#'.$appointment->service_id) }} ({{ $service->duration_minutes }}m)</p>

<form method="GET" action="{{ route('my.appointments.edit', $appointment) }}" style="margin-bottom:10px;">
    <label>Date: <input type="date" name="date" value="{{ $date }}"></label>
    <button type="submit">Change date</button>
    <a href="{{ route('my.appointments.show', $appointment) }}" style="margin-left:8px;">Cancel</a>
    </form>

<form method="POST" action="{{ route('my.appointments.update', $appointment) }}">
    @csrf
    @method('PATCH')
    <input type="hidden" name="date" value="{{ $date }}" />

    @if (empty($slots))
        <p>No available slots for {{ $date }}.</p>
    @else
        <div>
            @foreach ($slots as $slot)
                <div>
                    <label>
                        <input type="radio" name="start_at" value="{{ $slot }}" {{ old('start_at')===$slot ? 'checked' : '' }}>
                        {{ \Carbon\Carbon::parse($slot)->format('H:i') }}
                    </label>
                </div>
            @endforeach
        </div>
        <button type="submit">Reschedule</button>
    @endif
    </form>

@endsection

