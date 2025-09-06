@php
    $weekdayOptions = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];
    $intervalOptions = [15, 20, 30, 45, 60];
@endphp

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;">
    <div>
        <label for="weekday">Weekday</label><br>
        <select id="weekday" name="weekday">
            @foreach ($weekdayOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('weekday', $schedule->weekday ?? null) == $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('weekday')
            <div style="color:#dc3545;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="start_time">Start time</label><br>
        <input type="time" id="start_time" name="start_time" value="{{ old('start_time', isset($schedule) && $schedule->start_time ? \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') : '') }}">
        @error('start_time')
            <div style="color:#dc3545;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="end_time">End time</label><br>
        <input type="time" id="end_time" name="end_time" value="{{ old('end_time', isset($schedule) && $schedule->end_time ? \Illuminate\Support\Carbon::parse($schedule->end_time)->format('H:i') : '') }}">
        @error('end_time')
            <div style="color:#dc3545;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="slot_interval_minutes">Slot interval (minutes)</label><br>
        <select id="slot_interval_minutes" name="slot_interval_minutes">
            @foreach ($intervalOptions as $minutes)
                <option value="{{ $minutes }}" @selected(old('slot_interval_minutes', $schedule->slot_interval_minutes ?? 30) == $minutes)>{{ $minutes }}</option>
            @endforeach
        </select>
        @error('slot_interval_minutes')
            <div style="color:#dc3545;">{{ $message }}</div>
        @enderror
    </div>
</div>

<div style="margin-top:1rem;">
    <button type="submit" style="background:#0d6efd;border:1px solid #0d6efd;color:#fff;padding:0.5rem 0.75rem;border-radius:4px;cursor:pointer;">{{ $buttonText ?? 'Save' }}</button>
    <a href="{{ route('provider.schedules.index') }}" style="margin-left:0.5rem;">Cancel</a>
    @csrf
    @isset($method)
        @method($method)
    @endisset
</div>
