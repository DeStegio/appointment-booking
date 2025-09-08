@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header"><h1 class="title">Edit Service</h1></div>
    <div class="card-body">
      <form action="{{ route('provider.services.update', $service) }}" method="POST" class="form">
        @csrf
        @method('PUT')
        @include('provider.services._form', ['service' => $service])
        <div class="card-footer">
          <a class="btn btn-ghost" href="{{ route('provider.services.index') }}">Cancel</a>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
@endsection
