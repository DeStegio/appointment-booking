@php
    $startValue = old('start_at', isset($timeOff) && $timeOff->start_at ? \Illuminate\Support\Carbon::parse($timeOff->start_at)->format('Y-m-d\TH:i') : '');
    $endValue = old('end_at', isset($timeOff) && $timeOff->end_at ? \Illuminate\Support\Carbon::parse($timeOff->end_at)->format('Y-m-d\TH:i') : '');
@endphp

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;">
    <div>
        <label for="start_at">Start at</label><br>
        <input type="datetime-local" id="start_at" name="start_at" value="{{ $startValue }}">
        @error('start_at')
            <div style="color:#dc3545;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="end_at">End at</label><br>
        <input type="datetime-local" id="end_at" name="end_at" value="{{ $endValue }}">
        @error('end_at')
            <div style="color:#dc3545;">{{ $message }}</div>
        @enderror
    </div>

    <div style="grid-column:1 / -1;">
        <label for="reason">Reason</label><br>
        <input type="text" id="reason" name="reason" maxlength="255" value="{{ old('reason', $timeOff->reason ?? '') }}" placeholder="Optional note">
        @error('reason')
            <div style="color:#dc3545;">{{ $message }}</div>
        @enderror
    </div>
</div>

<div style="margin-top:1rem;">
    <button type="submit" style="background:#0d6efd;border:1px solid #0d6efd;color:#fff;padding:0.5rem 0.75rem;border-radius:4px;cursor:pointer;">{{ $buttonText ?? 'Save' }}</button>
    <a href="{{ route('provider.time-offs.index') }}" style="margin-left:0.5rem;">Cancel</a>
    @csrf
    @isset($method)
        @method($method)
    @endisset
</div>

