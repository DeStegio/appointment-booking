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

<div class="grid">
    <div class="form-group">
        <label class="form-label" for="weekday">Weekday</label>
        <select class="form-select" id="weekday" name="weekday">
            @foreach ($weekdayOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('weekday', $schedule->weekday ?? null) == $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('weekday')
            <div class="badge badge-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="start_time">Start time</label>
        <input class="form-control" type="time" id="start_time" name="start_time" value="{{ old('start_time', isset($schedule) && $schedule->start_time ? \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') : '') }}">
        @error('start_time')
            <div class="badge badge-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="end_time">End time</label>
        <input class="form-control" type="time" id="end_time" name="end_time" value="{{ old('end_time', isset($schedule) && $schedule->end_time ? \Illuminate\Support\Carbon::parse($schedule->end_time)->format('H:i') : '') }}">
        @error('end_time')
            <div class="badge badge-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="slot_interval_minutes">Slot interval (minutes)</label>
        <select class="form-select" id="slot_interval_minutes" name="slot_interval_minutes">
            @foreach ($intervalOptions as $minutes)
                <option value="{{ $minutes }}" @selected(old('slot_interval_minutes', $schedule->slot_interval_minutes ?? 30) == $minutes)>{{ $minutes }}</option>
            @endforeach
        </select>
        @error('slot_interval_minutes')
            <div class="badge badge-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="inline-actions mt-2">
    <button type="submit" class="btn btn-primary btn-sm focus-ring">{{ $buttonText ?? 'Save' }}</button>
    <a class="link" href="{{ route('provider.schedules.index') }}">Cancel</a>
    @csrf
    @isset($method)
        @method($method)
    @endisset
</div>
