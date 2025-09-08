@php
    $startValue = old('start_at', isset($timeOff) && $timeOff->start_at ? \Illuminate\Support\Carbon::parse($timeOff->start_at)->format('Y-m-d\TH:i') : '');
    $endValue = old('end_at', isset($timeOff) && $timeOff->end_at ? \Illuminate\Support\Carbon::parse($timeOff->end_at)->format('Y-m-d\TH:i') : '');
@endphp

<div class="grid">
    <div class="form-group">
        <label class="form-label" for="start_at">Start at</label>
        <input class="form-control" type="datetime-local" id="start_at" name="start_at" value="{{ $startValue }}">
        @error('start_at')
            <div class="badge badge-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="end_at">End at</label>
        <input class="form-control" type="datetime-local" id="end_at" name="end_at" value="{{ $endValue }}">
        @error('end_at')
            <div class="badge badge-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="reason">Reason</label>
        <input class="form-control" type="text" id="reason" name="reason" maxlength="255" value="{{ old('reason', $timeOff->reason ?? '') }}" placeholder="Optional note">
        @error('reason')
            <div class="badge badge-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="inline-actions mt-2">
    <button type="submit" class="btn btn-primary btn-sm focus-ring">{{ $buttonText ?? 'Save' }}</button>
    <a class="link" href="{{ route('provider.time-offs.index') }}">Cancel</a>
    @csrf
    @isset($method)
        @method($method)
    @endisset
</div>
