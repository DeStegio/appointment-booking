@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="title mb-2">Login</h1>

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

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus />
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
            <label>
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
                <span>Remember me</span>
            </label>
            @error('remember')
                <div class="badge badge-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary focus-ring">Login</button>
    </form>

    <p class="mt-2"><a class="link" href="{{ route('register.show') }}">Create an account</a></p>
</div>
@endsection
