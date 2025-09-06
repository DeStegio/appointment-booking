@extends('layouts.app')

@section('content')
<div class="container" style="max-width:480px;margin:0 auto;">
    <h1 style="margin-bottom:1rem;">Login</h1>

    @if ($errors->any())
        <div style="background:#ffe6e6;border:1px solid #ffb3b3;padding:0.75rem 1rem;margin-bottom:1rem;">
            <strong>There were some problems with your input:</strong>
            <ul style="margin:0.5rem 0 0 1.25rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}" style="display:block;">
        @csrf

        <div style="margin-bottom:0.75rem;">
            <label for="email" style="display:block;margin-bottom:0.25rem;">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus style="width:100%;padding:0.5rem;border:1px solid #ccc;border-radius:4px;" />
            @error('email')
                <div style="color:#b00020;margin-top:0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom:0.75rem;">
            <label for="password" style="display:block;margin-bottom:0.25rem;">Password</label>
            <input id="password" type="password" name="password" required style="width:100%;padding:0.5rem;border:1px solid #ccc;border-radius:4px;" />
            @error('password')
                <div style="color:#b00020;margin-top:0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom:1rem;">
            <label style="display:inline-flex;align-items:center;gap:0.5rem;">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
                <span>Remember me</span>
            </label>
            @error('remember')
                <div style="color:#b00020;margin-top:0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" style="padding:0.5rem 0.75rem;border:1px solid #0d6efd;background:#0d6efd;color:#fff;border-radius:4px;">Login</button>
    </form>

    <p style="margin-top:1rem;">
        <a href="{{ route('register.show') }}">Create an account</a>
    </p>
</div>
@endsection
