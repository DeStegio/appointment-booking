@extends('layouts.app')

@section('content')
<h1 class="title">Day View — {{ $date->toDateString() }}</h1>

<div class="mb-3 inline-actions">
  <a class="btn btn-sm focus-ring" href="{{ $prevUrl }}">Prev</a>
  <a class="btn btn-sm focus-ring" href="{{ $todayUrl }}">Today</a>
  <a class="btn btn-sm focus-ring" href="{{ $nextUrl }}">Next</a>
</div>

<form method="GET" action="{{ route('calendar.day') }}" class="mb-3">
  <input type="hidden" name="date" value="{{ $date->toDateString() }}">
  <label class="form-label" for="service_id">Service</label>
  <select class="form-select" id="service_id" name="service_id" onchange="this.form.submit()">
    @foreach($services as $svc)
      <option value="{{ $svc->id }}" @selected($service && $service->id===$svc->id)>{{ $svc->name }} ({{ $svc->duration_minutes }}m)</option>
    @endforeach
  </select>
  @if(!$services->count())
    <span class="muted">No services yet.</span>
  @endif
  <noscript><button class="btn btn-sm focus-ring" type="submit">Apply</button></noscript>
  <div class="mb-2 muted">Timezone: {{ config('app.timezone') }}</div>
</form>

<div class="grid">
  <div class="card">
    <h3>Working hours</h3>
    @forelse ($schedules as $sch)
      <div>{{ $sch->start_time }}–{{ $sch->end_time }} ({{ $sch->slot_interval_minutes }}m)</div>
    @empty
      <div>No working hours for this weekday.</div>
    @endforelse

    <h3 class="mt-2">Time-offs</h3>
    @forelse ($timeOffs as $to)
      <div>{{ $to->start_at }} → {{ $to->end_at }}</div>
    @empty
      <div>No time-offs today.</div>
    @endforelse
  </div>

  <div class="card">
    <h3>Appointments (today)</h3>
    @forelse($appointments as $a)
      <div class="card" data-appt="{{ $a->id }}">
        <div><strong>{{ $a->start_at }} → {{ $a->end_at }}</strong></div>
        <div>Service: {{ $a->service?->name ?? '-' }}</div>
        <div>Customer: {{ $a->customer?->name ?? '-' }} ({{ $a->customer?->email }})</div>
        <div>Status: <strong>{{ $a->status }}</strong></div>
        <div class="mt-1 inline-actions">
          @if($a->status==='pending')
            <form method="POST" action="{{ route('appointments.confirm',$a) }}">@csrf @method('PATCH') <button class="btn btn-primary btn-sm focus-ring" type="submit">Confirm</button></form>
            <form method="POST" action="{{ route('appointments.cancel',$a) }}">@csrf @method('PATCH') <button class="btn btn-danger btn-sm focus-ring" type="submit">Cancel</button></form>
          @elseif($a->status==='confirmed')
            <form method="POST" action="{{ route('appointments.complete',$a) }}">@csrf @method('PATCH') <button class="btn btn-primary btn-sm focus-ring" type="submit">Complete</button></form>
            <form method="POST" action="{{ route('appointments.cancel',$a) }}">@csrf @method('PATCH') <button class="btn btn-danger btn-sm focus-ring" type="submit">Cancel</button></form>
          @endif
        </div>
      </div>
    @empty
      <div>No appointments today.</div>
    @endforelse
  </div>

  <div class="card">
    <h3>Available slots</h3>
    @if($service)
      @forelse($slots as $s)
        <div class="slot-btn" data-slot="{{ $s }}">{{ $s }}</div>
      @empty
        <div>No available slots for {{ $service->name }}.</div>
      @endforelse
    @else
      <div>Create a service to see slots.</div>
    @endif
  </div>
</div>

{{-- styles moved to app.css via utility classes --}}
@endsection
