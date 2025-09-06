@extends('layouts.app')

@section('content')
<div class="container" style="max-width:720px;margin:0 auto;">
    <h1 style="margin-bottom:0.5rem;">Dashboard</h1>
    <p>Welcome, <strong>{{ $user->name ?? 'User' }}</strong>!</p>

    <div style="margin-top:1rem;">
        <p>Your role: <code>{{ $user->role ?? 'n/a' }}</code></p>
        @if (($user->role ?? null) === 'provider')
            <div style="margin-top:0.5rem;">
                <a href="{{ route('provider.services.index') }}" style="margin-right:0.75rem;">Services</a>
                <a href="{{ route('provider.schedules.index') }}">My Schedules</a>
            </div>
        @endif
    </div>
</div>
@endsection
