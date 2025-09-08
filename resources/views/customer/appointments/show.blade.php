@extends('layouts.app')

@section('content')
<h1>Appointment Details</h1>

@if (session('status'))
    <div style="padding:8px;background:#d1e7dd;color:#0f5132;margin-bottom:12px;">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div style="padding:8px;background:#f8d7da;color:#842029;margin-bottom:12px;">
        {{ $errors->first() }}
    </div>
@endif

<p><strong>Provider:</strong> {{ $appointment->provider->name ?? ('#'.$appointment->provider_id) }}</p>
<p><strong>Service:</strong> {{ $appointment->service->name ?? ('#'.$appointment->service_id) }}</p>
<p><strong>Start:</strong> {{ \Carbon\Carbon::parse($appointment->start_at)->format('Y-m-d H:i') }}</p>
<p><strong>End:</strong> {{ \Carbon\Carbon::parse($appointment->end_at)->format('Y-m-d H:i') }}</p>
<p><strong>Status:</strong> {{ ucfirst($appointment->status) }}</p>

<p>
    <a href="{{ route('my.appointments.index') }}">Back to list</a>
    @can('reschedule', $appointment)
        | <a href="{{ route('my.appointments.edit', $appointment) }}">Reschedule</a>
    @endcan
    @can('cancel', $appointment)
        | <form method="POST" action="{{ route('my.appointments.cancel', $appointment) }}" class="inline" onsubmit="return confirm('Cancel this appointment?');">
            @csrf
            @method('PATCH')
            <button type="submit" class="linky">Cancel</button>
        </form>
    @endcan
    </p>

@endsection

