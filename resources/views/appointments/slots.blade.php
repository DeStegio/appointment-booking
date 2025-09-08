@extends('layouts.app')

@section('content')
<div style="max-width:720px;margin:0 auto;">
    <h1 style="margin-bottom:0.5rem;">Available Slots</h1>

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

    <form method="GET" action="{{ route('appointments.slots', ['provider' => $provider->id, 'service' => $service->id]) }}" style="margin-bottom:1rem;">
        <label for="date">Date:</label>
        <input type="date" id="date" name="date"
               min="{{ $minDate->toDateString() }}"
               max="{{ $maxDate->toDateString() }}"
               value="{{ $selectedDate }}" required>
        <button type="submit">Check</button>
    </form>

    @error('date')
        <div style="color:#dc3545;">{{ $message }}</div>
    @enderror

    <h3>Slots for {{ $date }}</h3>
    @if (empty($slots))
        <p>No available slots for this date.</p>
    @else
        <ul style="list-style:none; padding-left:0;">
            @foreach ($slots as $slot)
                @php
                    $slotStart = \Carbon\Carbon::parse($slot, $tz);
                    $isPast = $slotStart->isPast();
                @endphp
                <li style="margin-bottom:0.5rem;">
                    <form method="POST" action="{{ route('appointments.store') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="provider_id" value="{{ $provider->id }}">
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <input type="hidden" name="start_at" value="{{ $slot }}">
                        <span style="display:inline-block; min-width:180px;">{{ $slotStart->format('H:i') }}</span>
                        <button type="submit" data-start="{{ $slot }}" @if($isPast) disabled @endif>Book</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
