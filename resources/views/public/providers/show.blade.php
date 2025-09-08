@extends('layouts.app')

@section('content')
    <h1 class="title">{{ $provider->name }}</h1>
    <p class="muted">{{ $provider->email }}</p>

    <h2 class="title mt-2">Services</h2>

    @if ($services->count() === 0)
        <p>No services available.</p>
    @else
        <div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Duration (min)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($services as $service)
                    <tr>
                        <td>{{ $service->name }}</td>
                        <td>{{ $service->duration_minutes ?? '-' }}</td>
                        <td>
                            <a class="link" href="{{ route('appointments.slots', ['provider' => $provider->id, 'service' => $service->id]) }}?date={{ now()->toDateString() }}">View slots</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-2">{{ $services->links() }}</div>
    @endif
@endsection
