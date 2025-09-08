@extends('layouts.app')

@section('content')
<div class="container max-w-560">
  <div class="card">
    <div class="card-header"><h1 class="title">Login</h1></div>
    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger mb-2" role="alert">
          <div>
            <strong>There were some problems with your input:</strong>
            <ul class="mt-1">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      <form method="POST" action="{{ route('login.attempt') }}" autocomplete="on" class="form">
        @csrf
        <div class="field">
          <label class="form-label" for="email">Email</label>
          <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" inputmode="email" />
          @error('email')
            <div class="badge badge-danger mt-1">{{ $message }}</div>
          @enderror
        </div>
        <div class="field">
          <label class="form-label" for="password">Password</label>
          <input class="form-control" id="password" type="password" name="password" required autocomplete="current-password" />
          @error('password')
            <div class="badge badge-danger mt-1">{{ $message }}</div>
          @enderror
        </div>
        <div class="field">
          <label>
            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
            <span>Remember me</span>
          </label>
          @error('remember')
            <div class="badge badge-danger mt-1">{{ $message }}</div>
          @enderror
        </div>
        <div class="card-footer">
          <a class="btn btn-ghost" href="{{ url('/') }}">Cancel</a>
          <button type="submit" class="btn btn-primary">Login</button>
        </div>
      </form>
      <p class="mt-2"><a class="link" href="{{ route('register.show') }}">Create an account</a></p>
    </div>
  </div>
</div>
@endsection
