@extends('layouts.app')

@section('content')
<h1>Day View — {{ $date->toDateString() }}</h1>

<div class="mb-3">
  <a href="{{ $prevUrl }}">Prev</a>
  <a href="{{ $todayUrl }}" style="margin: 0 0.5rem;">Today</a>
  <a href="{{ $nextUrl }}">Next</a>
</div>

<form method="GET" action="{{ route('calendar.day') }}" class="mb-3">
  <input type="hidden" name="date" value="{{ $date->toDateString() }}">
  <label for="service_id">Service:</label>
  <select id="service_id" name="service_id" onchange="this.form.submit()">
    @foreach($services as $svc)
      <option value="{{ $svc->id }}" @selected($service && $service->id===$svc->id)>{{ $svc->name }} ({{ $svc->duration_minutes }}m)</option>
    @endforeach
  </select>
  @if(!$services->count())
    <span style="color:#888; margin-left:0.5rem;">No services yet.</span>
  @endif
  <noscript><button type="submit">Apply</button></noscript>
  <div class="mb-2" style="color:#666; font-size:0.9rem;">Timezone: {{ config('app.timezone') }}</div>
</form>

<div class="grid3">
  <div class="col">
    <h3>Working hours</h3>
    @forelse ($schedules as $sch)
      <div>{{ $sch->start_time }}–{{ $sch->end_time }} ({{ $sch->slot_interval_minutes }}m)</div>
    @empty
      <div>No working hours for this weekday.</div>
    @endforelse

    <h3 style="margin-top:1rem;">Time-offs</h3>
    @forelse ($timeOffs as $to)
      <div>{{ $to->start_at }} → {{ $to->end_at }}</div>
    @empty
      <div>No time-offs today.</div>
    @endforelse
  </div>

  <div class="col">
    <h3>Appointments (today)</h3>
    @forelse($appointments as $a)
      <div class="card" data-appt="{{ $a->id }}">
        <div><strong>{{ $a->start_at }} → {{ $a->end_at }}</strong></div>
        <div>Service: {{ $a->service?->name ?? '-' }}</div>
        <div>Customer: {{ $a->customer?->name ?? '-' }} ({{ $a->customer?->email }})</div>
        <div>Status: <strong>{{ $a->status }}</strong></div>
        <div class="mt1">
          @if($a->status==='pending')
            <form method="POST" action="{{ route('appointments.confirm',$a) }}" style="display:inline">@csrf @method('PATCH') <button>Confirm</button></form>
            <form method="POST" action="{{ route('appointments.cancel',$a) }}" style="display:inline">@csrf @method('PATCH') <button>Cancel</button></form>
          @elseif($a->status==='confirmed')
            <form method="POST" action="{{ route('appointments.complete',$a) }}" style="display:inline">@csrf @method('PATCH') <button>Complete</button></form>
            <form method="POST" action="{{ route('appointments.cancel',$a) }}" style="display:inline">@csrf @method('PATCH') <button>Cancel</button></form>
          @endif
        </div>
      </div>
    @empty
      <div>No appointments today.</div>
    @endforelse
  </div>

  <div class="col">
    <h3>Available slots</h3>
    @if($service)
      @forelse($slots as $s)
        <div class="slot" data-slot="{{ $s }}">{{ $s }}</div>
      @empty
        <div>No available slots for {{ $service->name }}.</div>
      @endforelse
    @else
      <div>Create a service to see slots.</div>
    @endif
  </div>
</div>

<style>
  .grid3 { display:grid; grid-template-columns: repeat(3, 1fr); gap:1rem; }
  @media (max-width: 900px) { .grid3 { grid-template-columns: 1fr; } }
  .col { border:1px solid #eee; padding:0.75rem; border-radius:6px; }
  .card { border:1px solid #ddd; padding:0.5rem; border-radius:6px; margin-bottom:0.5rem; }
  .slot { border:1px dashed #bbb; padding:0.5rem; border-radius:6px; margin-bottom:0.4rem; }
  .mt1 { margin-top:0.5rem; }
  .mb-3 { margin-bottom:1rem; }
</style>
@endsection

