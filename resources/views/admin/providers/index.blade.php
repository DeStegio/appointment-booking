@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="title mb-2">Providers</h1>

    @if (session('status'))
        <div class="card">{{ session('status') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.providers.index') }}" class="mb-2 inline-actions">
        <input class="form-control" type="text" name="q" value="{{ $q }}" placeholder="Search name or email">
        <button class="btn btn-primary btn-sm" type="submit">Search</button>
    </form>

    <div>
        <table class="table">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            @forelse ($providers as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->email }}</td>
                    <td>
                        @php
                            $active = (bool)($p->is_active ?? true);
                            $badgeClass = $active ? 'badge-success' : 'badge-danger';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $active ? 'active' : 'inactive' }}</span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.providers.toggle', $p) }}" class="inline-actions">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm" type="submit">{{ $active ? 'Disable' : 'Enable' }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">No providers found.</td></tr>
            @endforelse
        </table>
    </div>

    <div class="mt-2">{{ $providers->links() }}</div>
</div>
@endsection
