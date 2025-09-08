@extends('layouts.app')

@section('content')
  <div class="container max-w-720">
    <div class="card">
      <div class="card-header"><h1 class="title">Something went wrong</h1></div>
      <div class="card-body">
        <p class="lead">An unexpected error occurred. Please try again.</p>
        <div class="mt-2 inline-actions">
          @auth
            <a class="btn btn-primary" href="{{ route('dashboard') }}">Back to dashboard</a>
          @else
            <a class="btn btn-primary" href="{{ route('home') }}">Back to home</a>
          @endauth
        </div>
      </div>
    </div>
  </div>
@endsection
