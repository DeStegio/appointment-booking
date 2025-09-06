@extends('layouts.app')

@section('content')
<div style="max-width:720px;margin:0 auto;">
    <h1 style="margin-bottom:0.5rem;">Available Slots</h1>
    <p>
        Provider: <strong>{{ $provider->name }}</strong><br>
        Service: <strong>{{ $service->name }}</strong>
    </p>

    <form method="GET" action="{{ route('appointments.slots', ['provider' => $provider->id, 'service' => $service->id]) }}" style="margin-bottom:1rem;">
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="{{ old('date', $date ?? now()->format('Y-m-d')) }}" required>
        <button type="submit">Check</button>
    </form>

    @error('date')
        <div style="color:#dc3545;">{{ $message }}</div>
    @enderror

    <h3>Slots for {{ $date }}</h3>
    @if (empty($slots))
        <p>No available slots.</p>
    @else
        <ul style="list-style:none; padding-left:0;">
            @foreach ($slots as $slot)
                <li style="margin-bottom:0.5rem;">
                    <form method="POST" action="{{ route('appointments.store') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="provider_id" value="{{ $provider->id }}">
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <input type="hidden" name="start_at" value="{{ $slot }}">
                        <span style="display:inline-block; min-width:180px;">{{ \Carbon\Carbon::parse($slot)->format('H:i') }}</span>
                        <button type="submit">Book</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
    @if (session('status'))
        <div style="margin-top:1rem;color:#198754;">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div style="margin-top:1rem;color:#dc3545;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection

