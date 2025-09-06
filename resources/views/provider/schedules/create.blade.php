@extends('layouts.app')

@section('content')
<div class="container" style="max-width:720px;margin:0 auto;">
    <h1>Create Schedule</h1>

    <form method="POST" action="{{ route('provider.schedules.store') }}">
        @include('provider.schedules._form', ['schedule' => $schedule, 'buttonText' => 'Create'])
    </form>
</div>
@endsection

