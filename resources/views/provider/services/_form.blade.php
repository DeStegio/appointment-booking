<p>
    <label for="name">Name</label><br>
    <input type="text" id="name" name="name" value="{{ old('name', $service->name ?? '') }}" required>
    @error('name')
        <div role="alert">{{ $message }}</div>
    @enderror
</p>

<p>
    <label for="duration_minutes">Duration (minutes)</label><br>
    <input type="number" id="duration_minutes" name="duration_minutes" min="5" max="480" value="{{ old('duration_minutes', $service->duration_minutes ?? '') }}" required>
    @error('duration_minutes')
        <div role="alert">{{ $message }}</div>
    @enderror
</p>

<p>
    <label for="price">Price</label><br>
    <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $service->price ?? '') }}">
    @error('price')
        <div role="alert">{{ $message }}</div>
    @enderror
</p>

<p>
    <label>
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', isset($service) ? (bool) $service->is_active : false) ? 'checked' : '' }}>
        Active
    </label>
    @error('is_active')
        <div role="alert">{{ $message }}</div>
    @enderror
</p>

