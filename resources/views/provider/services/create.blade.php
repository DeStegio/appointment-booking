@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header"><h1 class="title">Create Service</h1></div>
    <div class="card-body">
      <form action="{{ route('provider.services.store') }}" method="POST" class="form">
        @csrf
        @include('provider.services._form')
        <div class="card-footer">
          <a class="btn btn-ghost" href="{{ route('provider.services.index') }}">Cancel</a>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
@endsection
