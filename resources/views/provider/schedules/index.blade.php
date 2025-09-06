@extends('layouts.app')

@section('content')
<div class="container" style="max-width:960px;margin:0 auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h1 style="margin:0;">My Schedules</h1>
        <a href="{{ route('provider.schedules.create') }}">Create schedule</a>
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
                    <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Weekday</th>
                    <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Start</th>
                    <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">End</th>
                    <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Interval (min)</th>
                    <th style="text-align:left;border-bottom:1px solid #eee;padding:8px;">Actions</th>
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
                        <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $weekdayLabels[$schedule->weekday] ?? $schedule->weekday }}</td>
                        <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                        <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ \Illuminate\Support\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                        <td style="border-bottom:1px solid #f2f2f2;padding:8px;">{{ $schedule->slot_interval_minutes }}</td>
                        <td style="border-bottom:1px solid #f2f2f2;padding:8px;">
                            <a href="{{ route('provider.schedules.edit', $schedule) }}" style="margin-right:0.5rem;">Edit</a>
                            <form action="{{ route('provider.schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('Delete this schedule?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="linky">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:12px;color:#6c757d;">No schedules yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem;">
        {{ $schedules->links() }}
    </div>
</div>
@endsection

