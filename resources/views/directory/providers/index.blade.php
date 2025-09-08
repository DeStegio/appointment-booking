@extends('layouts.app')

@section('content')
    <h1>Find a Provider</h1>

    <form method="GET" action="{{ route('providers.index') }}" class="mb-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name or service">
        <button type="submit">Search</button>
    </form>

    @if ($providers->count() === 0)
        <p>No providers found.</p>
    @else
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 0.75rem;">
            @foreach ($providers as $provider)
                <div style="border:1px solid #eee; border-radius:6px; padding:0.75rem;">
                    <div style="font-weight:600;">{{ $provider->name }}</div>
                    <div style="color:#6c757d;">{{ $provider->email }}</div>
                    <div style="color:#6c757d; font-size: 0.9rem; margin-top:0.25rem;">
                        {{ $provider->services_count }} active services
                    </div>
                    <div style="margin-top:0.5rem;">
                        <a href="{{ route('providers.show', ['provider' => $provider]) }}">View profile</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:1rem;">
            {{ $providers->links() }}
        </div>
    @endif
@endsection
