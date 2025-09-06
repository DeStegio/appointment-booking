@extends('layouts.app')

@section('content')
<div class="container" style="max-width:520px;margin:0 auto;">
    <h1 style="margin-bottom:1rem;">Register</h1>

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

    <form method="POST" action="{{ route('register.store') }}" style="display:block;">
        @csrf

        <div style="margin-bottom:0.75rem;">
            <label for="name" style="display:block;margin-bottom:0.25rem;">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required style="width:100%;padding:0.5rem;border:1px solid #ccc;border-radius:4px;" />
            @error('name')
                <div style="color:#b00020;margin-top:0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom:0.75rem;">
            <label for="email" style="display:block;margin-bottom:0.25rem;">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required style="width:100%;padding:0.5rem;border:1px solid #ccc;border-radius:4px;" />
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

        <div style="margin-bottom:0.75rem;">
            <label for="password_confirmation" style="display:block;margin-bottom:0.25rem;">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required style="width:100%;padding:0.5rem;border:1px solid #ccc;border-radius:4px;" />
            @error('password_confirmation')
                <div style="color:#b00020;margin-top:0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom:1rem;">
            <label for="role" style="display:block;margin-bottom:0.25rem;">Role</label>
            <select id="role" name="role" style="width:100%;padding:0.5rem;border:1px solid #ccc;border-radius:4px;">
                <option value="customer" {{ old('role', 'customer') === 'customer' ? 'selected' : '' }}>customer</option>
                <option value="provider" {{ old('role') === 'provider' ? 'selected' : '' }}>provider</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>admin</option>
            </select>
            @error('role')
                <div style="color:#b00020;margin-top:0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" style="padding:0.5rem 0.75rem;border:1px solid #198754;background:#198754;color:#fff;border-radius:4px;">Register</button>
    </form>

    <p style="margin-top:1rem;">
        <a href="{{ route('login') }}">Back to login</a>
    </p>
</div>
@endsection

