@extends('layouts.app')

@section('content')
<div class="container">
    <div class="inline-actions">
        <h1 class="title">My Schedules</h1>
        <a class="btn btn-primary btn-sm focus-ring" href="{{ route('provider.schedules.create') }}">Create schedule</a>
    </div>

    @if (session('success'))
        <div class="card mt-2">{{ session('success') }}</div>
    @endif

    <div class="mt-2">
        <div class="table-responsive"><table class="table">
            <thead>
                <tr>
                    <th>Weekday</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Interval (min)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $weekdayLabels = [
                        0 => 'Sunday',
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                    ];
                @endphp
                @forelse ($schedules as $schedule)
                    <tr>
                        <td>{{ $weekdayLabels[$schedule->weekday] ?? $schedule->weekday }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                        <td>{{ $schedule->slot_interval_minutes }}</td>
                        <td>
                            <div class="inline-actions">
                            <a class="btn btn-sm focus-ring" href="{{ route('provider.schedules.edit', $schedule) }}">Edit</a>
                            <form action="{{ route('provider.schedules.destroy', $schedule) }}" method="POST" data-confirm="Delete this schedule?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm focus-ring">Delete</button>
                            </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No schedules yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table></div>
    </div>

    <div class="mt-2">{{ $schedules->links() }}</div>
</div>
@endsection
