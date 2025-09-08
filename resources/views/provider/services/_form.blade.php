<div class="form">
  <div class="field">
    <label class="form-label" for="name">Name</label>
    <input class="form-control" type="text" id="name" name="name" value="{{ old('name', $service->name ?? '') }}" required autocomplete="off">
    @error('name')
      <div class="badge badge-danger mt-1" role="alert">{{ $message }}</div>
    @enderror
  </div>

  <div class="field">
    <label class="form-label" for="duration_minutes">Duration (minutes)</label>
    <input class="form-control" type="number" id="duration_minutes" name="duration_minutes" min="5" max="480" value="{{ old('duration_minutes', $service->duration_minutes ?? '') }}" required inputmode="numeric">
    @error('duration_minutes')
      <div class="badge badge-danger mt-1" role="alert">{{ $message }}</div>
    @enderror
  </div>

  <div class="field">
    <label class="form-label" for="price">Price</label>
    <input class="form-control" type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $service->price ?? '') }}" inputmode="decimal">
    @error('price')
      <div class="badge badge-danger mt-1" role="alert">{{ $message }}</div>
    @enderror
  </div>

  <div class="field">
    <label class="form-label">
      <input type="checkbox" name="is_active" value="1" {{ old('is_active', isset($service) ? (bool) $service->is_active : false) ? 'checked' : '' }}>
      Active
    </label>
    @error('is_active')
      <div class="badge badge-danger mt-1" role="alert">{{ $message }}</div>
    @enderror
  </div>
</div>
