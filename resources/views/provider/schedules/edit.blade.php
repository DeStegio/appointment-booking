@extends('layouts.app')

@section('content')
<div class="container" style="max-width:720px;margin:0 auto;">
    <h1>Edit Schedule</h1>

    <form method="POST" action="{{ route('provider.schedules.update', $schedule) }}">
        @include('provider.schedules._form', ['schedule' => $schedule, 'buttonText' => 'Update', 'method' => 'PUT'])
    </form>
</div>
@endsection

