@extends('layouts.app')

@section('content')
<div class="container">
  <div class="inline-actions">
    <h1 class="title">Services</h1>
    <a class="btn btn-primary btn-sm focus-ring" href="{{ route('provider.services.create') }}">Create service</a>
  </div>

  @if (session('status'))
    <div class="alert alert-success fade-in" role="status">{{ session('status') }}</div>
  @endif

  <div class="table-responsive mt-2">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Duration (minutes)</th>
          <th scope="col">Price</th>
          <th scope="col">Active</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
      @forelse ($services as $service)
        <tr>
          <td>{{ $service->name }}</td>
          <td>{{ $service->duration_minutes }}</td>
          <td>{{ number_format((float) $service->price, 2) }}</td>
          <td>{{ $service->is_active ? 'Yes' : 'No' }}</td>
          <td>
            <div class="inline-actions">
              <a class="btn btn-sm focus-ring" href="{{ route('provider.services.edit', $service) }}">Edit</a>
              <form action="{{ route('provider.services.destroy', $service) }}" method="POST" data-confirm="Delete this service?">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm focus-ring">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5">No services found.</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-2">
    {{ $services->links() }}
  </div>
</div>
@endsection

