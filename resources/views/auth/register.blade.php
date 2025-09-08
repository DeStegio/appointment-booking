@extends('layouts.app')

@section('content')
<div class="container max-w-640">
  <div class="card">
    <div class="card-header"><h1 class="title">Register</h1></div>
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

      <form method="POST" action="{{ route('register.store') }}" autocomplete="on" class="form">
        @csrf
        <div class="field">
          <label class="form-label" for="name">Name</label>
          <input class="form-control" id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" />
          @error('name')
            <div class="badge badge-danger mt-1">{{ $message }}</div>
          @enderror
        </div>
        <div class="field">
          <label class="form-label" for="email">Email</label>
          <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" inputmode="email" />
          @error('email')
            <div class="badge badge-danger mt-1">{{ $message }}</div>
          @enderror
        </div>
        <div class="field">
          <label class="form-label" for="password">Password</label>
          <input class="form-control" id="password" type="password" name="password" required autocomplete="new-password" />
          @error('password')
            <div class="badge badge-danger mt-1">{{ $message }}</div>
          @enderror
        </div>
        <div class="field">
          <label class="form-label" for="password_confirmation">Confirm Password</label>
          <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
          @error('password_confirmation')
            <div class="badge badge-danger mt-1">{{ $message }}</div>
          @enderror
        </div>
        <div class="field">
          <label class="form-label" for="role">Role</label>
          <select class="form-select" id="role" name="role">
            <option value="customer" {{ old('role', 'customer') === 'customer' ? 'selected' : '' }}>customer</option>
            <option value="provider" {{ old('role') === 'provider' ? 'selected' : '' }}>provider</option>
            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>admin</option>
          </select>
          @error('role')
            <div class="badge badge-danger mt-1">{{ $message }}</div>
          @enderror
        </div>
        <div class="card-footer">
          <a class="btn btn-ghost" href="{{ route('login') }}">Back to login</a>
          <button type="submit" class="btn btn-primary">Register</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
