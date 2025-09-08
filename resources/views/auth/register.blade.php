@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="title mb-2">Register</h1>

    @if ($errors->any())
        <div class="card mb-2">
            <strong>There were some problems with your input:</strong>
            <ul class="mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Name</label>
            <input class="form-control" id="name" type="text" name="name" value="{{ old('name') }}" required />
            @error('name')
                <div class="badge badge-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required />
            @error('email')
                <div class="badge badge-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input class="form-control" id="password" type="password" name="password" required />
            @error('password')
                <div class="badge badge-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm Password</label>
            <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required />
            @error('password_confirmation')
                <div class="badge badge-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
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

        <button type="submit" class="btn btn-primary focus-ring">Register</button>
    </form>

    <p class="mt-2"><a class="link" href="{{ route('login') }}">Back to login</a></p>
</div>
@endsection
