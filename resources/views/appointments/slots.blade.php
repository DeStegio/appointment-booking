@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="title mb-1">Available Slots</h1>

    @if (session('status'))
        <div class="card mt-2">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="card mt-2">{{ $errors->first() }}</div>
    @endif

    <p>
        Provider: <strong>{{ $provider->name }}</strong><br>
        Service: <strong>{{ $service->name }}</strong><br>
        Duration: <strong>{{ (int)($service->duration_minutes ?? 30) }} minutes</strong>
    </p>

    @php
        $tz = config('app.timezone');
        $now = \Carbon\Carbon::now($tz);
        $today = $now->copy()->startOfDay();
        $leadMinutes = (int) config('appointments.lead_time_minutes', 120);
        $bookingWindowDays = (int) config('appointments.booking_window_days', 60);
        $leadDate = $now->copy()->addMinutes($leadMinutes)->startOfDay();
        $minDate = $leadDate->gt($today) ? $leadDate : $today;
        $maxDate = $today->copy()->addDays($bookingWindowDays);
        $selectedDate = old('date', $date ?? $today->toDateString());
    @endphp

    <form method="GET" action="{{ route('appointments.slots', ['provider' => $provider->id, 'service' => $service->id]) }}" class="mb-2 inline-actions">
        <label class="form-label" for="date">Date</label>
        <input class="form-control" type="date" id="date" name="date"
               min="{{ $minDate->toDateString() }}"
               max="{{ $maxDate->toDateString() }}"
               value="{{ $selectedDate }}" required>
        <button type="submit" class="btn btn-primary btn-sm">Check</button>
    </form>

    @error('date')
        <div class="badge badge-danger">{{ $message }}</div>
    @enderror

    <h3>Slots for {{ $date }}</h3>
    @if (empty($slots))
        <p>No available slots for this date.</p>
    @else
        <ul class="mt-2">
            @foreach ($slots as $slot)
                @php
                    $slotStart = \Carbon\Carbon::parse($slot, $tz);
                    $isPast = $slotStart->isPast();
                @endphp
                <li class="mt-1">
                    <form method="POST" action="{{ route('appointments.store') }}" class="inline-actions">
                        @csrf
                        <input type="hidden" name="provider_id" value="{{ $provider->id }}">
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <input type="hidden" name="start_at" value="{{ $slot }}">
                        <span>{{ $slotStart->format('H:i') }}</span>
                        <button class="btn btn-primary btn-sm" type="submit" data-start="{{ $slot }}" @if($isPast) disabled @endif>Book</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
