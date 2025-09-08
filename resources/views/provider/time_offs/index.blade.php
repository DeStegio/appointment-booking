@extends('layouts.app')

@section('content')
<div class="container">
    <div class="inline-actions">
        <h1 class="title">Time Offs</h1>
        <a class="btn btn-primary btn-sm" href="{{ route('provider.time-offs.create') }}">Add time off</a>
    </div>

    @if (session('success'))
        <div class="card mt-2">{{ session('success') }}</div>
    @endif

    <div class="mt-2">
        <table class="table">
            <thead>
                <tr>
                    <th>Start</th>
                    <th>End</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($timeOffs as $timeOff)
                    <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($timeOff->start_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($timeOff->end_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $timeOff->reason }}</td>
                        <td>
                            <div class="inline-actions">
                            <a class="btn btn-sm" href="{{ route('provider.time-offs.edit', $timeOff) }}">Edit</a>
                            <form action="{{ route('provider.time-offs.destroy', $timeOff) }}" method="POST" onsubmit="return confirm('Delete this time off?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">No time offs yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">{{ $timeOffs->links() }}</div>
</div>
@endsection
