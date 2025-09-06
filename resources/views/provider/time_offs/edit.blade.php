@extends('layouts.app')

@section('content')
<div class="container" style="max-width:720px;margin:0 auto;">
    <h1>Edit Time Off</h1>

    <form method="POST" action="{{ route('provider.time-offs.update', $timeOff) }}">
        @include('provider.time_offs._form', ['timeOff' => $timeOff, 'buttonText' => 'Update', 'method' => 'PUT'])
    </form>
</div>
@endsection

