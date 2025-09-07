@extends('layouts.app')

@section('content')
<div style="max-width:900px;margin:0 auto;">
    <h1 style="margin-bottom:1rem;">Admin Dashboard</h1>

    @if (session('status'))
        <div style="padding:8px 10px;margin:10px 0;border:1px solid #b2dfdb;background:#e0f2f1;color:#004d40;">
            {{ session('status') }}
        </div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
        <div style="border:1px solid #eee;padding:12px;border-radius:6px;">
            <div style="color:#6c757d;">Providers (Total)</div>
            <div style="font-size:24px;font-weight:700;">{{ $totalProviders }}</div>
        </div>
        <div style="border:1px solid #eee;padding:12px;border-radius:6px;">
            <div style="color:#6c757d;">Providers (Active)</div>
            <div style="font-size:24px;font-weight:700;">{{ $activeProviders }}</div>
        </div>
        <div style="border:1px solid #eee;padding:12px;border-radius:6px;">
            <div style="color:#6c757d;">Providers (Inactive)</div>
            <div style="font-size:24px;font-weight:700;">{{ $inactiveProviders }}</div>
        </div>
        <div style="border:1px solid #eee;padding:12px;border-radius:6px;">
            <div style="color:#6c757d;">Customers</div>
            <div style="font-size:24px;font-weight:700;">{{ $customers }}</div>
        </div>
        <div style="border:1px solid #eee;padding:12px;border-radius:6px;">
            <div style="color:#6c757d;">Appointments (Today)</div>
            <div style="font-size:24px;font-weight:700;">{{ $appointmentsToday }}</div>
        </div>
        <div style="border:1px solid #eee;padding:12px;border-radius:6px;">
            <div style="color:#6c757d;">Appointments (This Week)</div>
            <div style="font-size:24px;font-weight:700;">{{ $appointmentsThisWeek }}</div>
        </div>
    </div>

    <div style="margin-top:1rem;">
        <a href="{{ route('admin.providers.index') }}">Manage Providers</a>
    </div>
</div>
@endsection

