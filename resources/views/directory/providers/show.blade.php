@extends('layouts.app')
@section('content')
  <div class="primary-card">
    <div class="card-header">
      <h1 class="title">{{ $provider->name }}</h1>
      <div class="muted">{{ $provider->email }}</div>
    </div>
    <div class="card-body">
      @if($provider->services->isEmpty())
        <p>No active services yet.</p>
        <p class="mt-2"><a class="link" href="{{ route('providers.index') }}">&larr; Back to providers</a></p>
      @else
        <h2 class="h2">Active services</h2>
        <div class="grid mt-2">
          @foreach($provider->services as $s)
            <div class="card">
              <h3 class="h3">{{ $s->name }}</h3>
              <div class="muted">Duration: {{ $s->duration_minutes }}'</div>
              @if(!is_null($s->price))
                <div class="muted">Price: €{{ number_format($s->price,2) }}</div>
              @endif
              <div class="mt-2">
                <a class="btn btn-primary btn-sm focus-ring" href="{{ route('appointments.slots', ['provider' => $provider->id, 'service' => $s->id]) }}?date={{ now()->toDateString() }}">View slots</a>
              </div>
            </div>
          @endforeach
        </div>
        <p class="mt-3"><a class="link" href="{{ route('providers.index') }}">&larr; Back to providers</a></p>
      @endif
    </div>
  </div>
@endsection

