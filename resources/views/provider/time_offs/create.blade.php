@extends('layouts.app')

@section('content')
<div class="container" style="max-width:720px;margin:0 auto;">
    <h1>Add Time Off</h1>

    <form method="POST" action="{{ route('provider.time-offs.store') }}">
        @include('provider.time_offs._form', ['timeOff' => $timeOff, 'buttonText' => 'Create'])
    </form>
</div>
@endsection

