@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Time Off</h1>

    <form method="POST" action="{{ route('provider.time-offs.update', $timeOff) }}">
        @include('provider.time_offs._form', ['timeOff' => $timeOff, 'buttonText' => 'Update', 'method' => 'PUT'])
    </form>
</div>
@endsection
