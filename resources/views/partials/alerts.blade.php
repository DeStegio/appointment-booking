@php($status = session('status'))
@if ($status)
  <div class="container">
    <div class="alert alert-success fade-in" role="status">
      <div>{{ $status }}</div>
      <button type="button" class="btn btn-sm btn-muted focus-ring close" data-dismiss="alert" aria-label="Dismiss">×</button>
    </div>
  </div>
@endif

@if ($errors->any())
  <div class="container">
    <div class="alert alert-danger fade-in" role="alert">
      <div>
        <strong>There were errors:</strong>
        <ul class="mt-1">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
      <button type="button" class="btn btn-sm btn-muted focus-ring close" data-dismiss="alert" aria-label="Dismiss">×</button>
    </div>
  </div>
@endif

