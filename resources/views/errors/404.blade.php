@extends('layouts.app')

@section('content')
  <div class="container max-w-720">
    <div class="card">
      <div class="card-header"><h1 class="title">Page not found</h1></div>
      <div class="card-body">
        <p class="lead">We couldn’t find what you’re looking for.</p>
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
