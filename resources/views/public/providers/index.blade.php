@extends('layouts.app')

@section('content')
    <h1 class="title">Providers</h1>

    <form method="GET" action="{{ route('providers.index') }}" class="mb-2 inline-actions">
        <input class="form-control" type="text" name="q" value="{{ $q }}" placeholder="Search by name/email" />
        <button class="btn btn-primary btn-sm" type="submit">Search</button>
    </form>

    @if ($providers->count() === 0)
        <p>No providers found.</p>
    @else
        <div class="grid">
            @foreach ($providers as $provider)
                <div class="card">
                    <h3>{{ $provider->name }}</h3>
                    <div class="muted">{{ $provider->email }}</div>
                    <div class="mt-2">
                        <a class="link" href="{{ route('providers.show', $provider) }}">View profile</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-2">{{ $providers->links() }}</div>
    @endif
@endsection
