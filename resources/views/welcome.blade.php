@extends('layouts.app')

@section('content')
  <div class="container max-w-760">
    <div class="card">
      <div class="card-header"><h1 class="title">Welcome</h1></div>
      <div class="card-body">
        <p class="lead">{{ config('app.name', 'Appointment Booking') }}</p>
        <p class="muted">A simple appointment booking demo app.</p>
        <div class="mt-2 inline-actions">
          @auth
            <a class="btn btn-primary focus-ring" href="{{ route('dashboard') }}">Go to Dashboard</a>
          @else
            <a class="btn btn-primary focus-ring" href="{{ route('providers.index') }}">Find a Provider</a>
            <a class="btn focus-ring" href="{{ route('login') }}">Login</a>
            <a class="btn focus-ring" href="{{ route('register.show') }}">Register</a>
          @endauth
        </div>
      </div>
    </div>
  </div>
@endsection
