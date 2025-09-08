@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="title mb-2">Admin Dashboard</h1>

    @if (session('status'))
        <div class="card">{{ session('status') }}</div>
    @endif

    <div class="grid">
        <div class="card">
            <div class="muted">Providers (Total)</div>
            <div class="title">{{ $totalProviders }}</div>
        </div>
        <div class="card">
            <div class="muted">Providers (Active)</div>
            <div class="title">{{ $activeProviders }}</div>
        </div>
        <div class="card">
            <div class="muted">Providers (Inactive)</div>
            <div class="title">{{ $inactiveProviders }}</div>
        </div>
        <div class="card">
            <div class="muted">Customers</div>
            <div class="title">{{ $customers }}</div>
        </div>
        <div class="card">
            <div class="muted">Appointments (Today)</div>
            <div class="title">{{ $appointmentsToday }}</div>
        </div>
        <div class="card">
            <div class="muted">Appointments (This Week)</div>
            <div class="title">{{ $appointmentsThisWeek }}</div>
        </div>
    </div>

    <div class="mt-2">
        <a class="btn btn-sm focus-ring" href="{{ route('admin.providers.index') }}">Manage Providers</a>
    </div>
</div>
@endsection
