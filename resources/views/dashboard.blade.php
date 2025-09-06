@extends('layouts.app')

@section('content')
<div class="container" style="max-width:720px;margin:0 auto;">
    <h1 style="margin-bottom:0.5rem;">Dashboard</h1>
    <p>Welcome, <strong>{{ $user->name ?? 'User' }}</strong>!</p>

    <div style="margin-top:1rem;">
        <p>Your role: <code>{{ $user->role ?? 'n/a' }}</code></p>
    </div>
</div>
@endsection

