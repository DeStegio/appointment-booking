@extends('layouts.app')

@section('content')
    <h1>{{ $provider->name }}</h1>
    <p style="color:#6c757d;">{{ $provider->email }}</p>

    <form method="GET" action="{{ route('providers.show', $provider) }}" style="margin:1rem 0; display:flex; gap:0.5rem; align-items:center;">
        <label>
            Service:
            <select name="service">
                @foreach ($services as $svc)
                    <option value="{{ $svc->slug }}" {{ $service && $service->id === $svc->id ? 'selected' : '' }}>
                        {{ $svc->name }}
                    </option>
                @endforeach
            </select>
        </label>
        <label>
            Date:
            <input type="date" name="date" value="{{ $date }}" />
        </label>
        <button type="submit">Show slots</button>
    </form>

    <h2>Available slots</h2>
    @if (!$service)
        <p>No services available for this provider.</p>
    @else
        @if (empty($slots))
            <p>No available slots on {{ $date }} for {{ $service->name }}.</p>
        @else
            <ul style="list-style:none; padding-left:0; display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:0.5rem;">
                @foreach ($slots as $slot)
                    <li style="border:1px solid #eee; padding:0.5rem; border-radius:6px; display:flex; justify-content:space-between; align-items:center;">
                        <span>{{ \Carbon\Carbon::parse($slot)->format('D, M j Y H:i') }}</span>
                        @auth
                            <form method="POST" action="{{ route('appointments.store') }}" style="margin:0;">
                                @csrf
                                <input type="hidden" name="provider_id" value="{{ $provider->id }}" />
                                <input type="hidden" name="service_id" value="{{ $service->id }}" />
                                <input type="hidden" name="start_at" value="{{ $slot }}" />
                                <button type="submit">Book</button>
                            </form>
                        @endauth
                        @guest
                            <a href="{{ route('login') }}">Login to book</a>
                        @endguest
                    </li>
                @endforeach
            </ul>
        @endif
    @endif
@endsection
