@extends('layouts.app')

@section('content')
    <h1 class="title">Find a Provider</h1>

    <form method="GET" action="{{ route('providers.index') }}" class="mb-2 inline-actions">
        <input class="form-control" type="text" name="q" value="{{ request('q') }}" placeholder="Search by name or service">
        <button type="submit" class="btn btn-primary btn-sm focus-ring">Search</button>
    </form>

    @if ($providers->count() === 0)
        <p>No providers found.</p>
    @else
        <div class="grid">
            @foreach ($providers as $provider)
                <div class="card">
                    <h3>{{ $provider->name }}</h3>
                    <div class="muted">{{ $provider->email }}</div>
                    <div class="muted mt-1">
                        {{ $provider->services_count }} active services
                    </div>
                    <div class="mt-2">
                        <a class="link" href="{{ route('providers.show', ['provider' => $provider]) }}">View profile</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-2">
            {{ $providers->links() }}
        </div>
    @endif
@endsection
