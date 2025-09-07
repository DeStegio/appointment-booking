@extends('layouts.app')

@section('content')
<div style="max-width:920px;margin:0 auto;">
    <h1 style="margin-bottom:0.75rem;">Providers</h1>

    @if (session('status'))
        <div style="padding:8px 10px;margin:10px 0;border:1px solid #b2dfdb;background:#e0f2f1;color:#004d40;">
            {{ session('status') }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.providers.index') }}" style="margin-bottom:1rem;">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search name or email" style="padding:6px;width:260px;">
        <button type="submit">Search</button>
    </form>

    <div style="overflow:auto;">
        <table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse; width:100%; min-width:680px;">
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
                            $badgeBg = $active ? '#e8f5e9' : '#ffebee';
                            $badgeColor = $active ? '#1b5e20' : '#b71c1c';
                        @endphp
                        <span style="padding:2px 6px;border-radius:10px;background:{{ $badgeBg }};color:{{ $badgeColor }};font-size:12px;">
                            {{ $active ? 'active' : 'inactive' }}
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.providers.toggle', $p) }}" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit">{{ $active ? 'Disable' : 'Enable' }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:#6c757d;">No providers found.</td></tr>
            @endforelse
        </table>
    </div>

    <div style="margin-top:1rem;">
        {{ $providers->links() }}
    </div>
</div>
@endsection

