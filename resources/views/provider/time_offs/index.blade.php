@extends('layouts.app')

@section('content')
<div class="container" style="max-width:960px;margin:0 auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h1 style="margin:0;">Time Offs</h1>
        <a href="{{ route('provider.time-offs.create') }}">Add time off</a>
    </div>

    @if (session('success'))
        <div style="margin-top:1rem;padding:0.75rem 1rem;background:#d1e7dd;color:#0f5132;border:1px solid #badbcc;border-radius:4px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="overflow-x:auto;margin-top:1rem;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Start</th>
                    <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">End</th>
                    <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Reason</th>
                    <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($timeOffs as $timeOff)
                    <tr>
                        <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ \Illuminate\Support\Carbon::parse($timeOff->start_at)->format('Y-m-d H:i') }}</td>
                        <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ \Illuminate\Support\Carbon::parse($timeOff->end_at)->format('Y-m-d H:i') }}</td>
                        <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $timeOff->reason }}</td>
                        <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
                            <a href="{{ route('provider.time-offs.edit', $timeOff) }}" style="margin-right:0.5rem;">Edit</a>
                            <form action="{{ route('provider.time-offs.destroy', $timeOff) }}" method="POST" class="inline" onsubmit="return confirm('Delete this time off?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="linky">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding:12px;color:#6c757d;">No time offs yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem;">
        {{ $timeOffs->links() }}
    </div>
</div>
@endsection

