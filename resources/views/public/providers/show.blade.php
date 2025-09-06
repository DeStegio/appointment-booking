@extends('layouts.app')

@section('content')
    <h1>{{ $provider->name }}</h1>
    <p style="color:#6c757d;">{{ $provider->email }}</p>

    <h2 style="margin-top:1rem;">Services</h2>

    @if ($services->count() === 0)
        <p>No services available.</p>
    @else
        <div style="overflow:auto;">
            <table style="border-collapse:collapse; width:100%; min-width:480px;">
                <thead>
                    <tr>
                        <th style="text-align:left; border-bottom:1px solid #eee; padding:0.5rem;">Name</th>
                        <th style="text-align:left; border-bottom:1px solid #eee; padding:0.5rem;">Duration (min)</th>
                        <th style="text-align:left; border-bottom:1px solid #eee; padding:0.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($services as $service)
                    <tr>
                        <td style="border-bottom:1px solid #f5f5f5; padding:0.5rem;">{{ $service->name }}</td>
                        <td style="border-bottom:1px solid #f5f5f5; padding:0.5rem;">{{ $service->duration_minutes ?? '-' }}</td>
                        <td style="border-bottom:1px solid #f5f5f5; padding:0.5rem;">
                            <a href="{{ route('appointments.slots', ['provider' => $provider->id, 'service' => $service->id]) }}?date={{ now()->toDateString() }}">View slots</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:1rem;">
            {{ $services->links() }}
        </div>
    @endif
@endsection

