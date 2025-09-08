<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav class="p-2" >
        <div>
            <a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>
        </div>
        <div class="inline-actions">
            @if (Route::has('providers.index'))
                <a class="link" href="{{ route('providers.index') }}">Find a Provider</a>
            @endif
            @auth
                <span class="muted">{{ auth()->user()->name }}</span>
                @if (Route::has('dashboard'))
                    <a class="link" href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a class="link" href="{{ url('/') }}">Dashboard</a>
                @endif

                @if (auth()->user()->isRole('admin'))
                    @if (Route::has('admin.dashboard'))
                        <a class="link" href="{{ route('admin.dashboard') }}">Admin</a>
                    @endif
                @endif

                @if (auth()->user()->isRole('provider'))
                    @if (Route::has('provider.services.index'))
                        <a class="link" href="{{ route('provider.services.index') }}">Services</a>
                    @endif
                    @if (Route::has('calendar.day'))
                        <a class="link" href="{{ route('calendar.day') }}">Calendar</a>
                    @endif
                    @if (Route::has('provider.appointments.index'))
                        <a class="link" href="{{ route('provider.appointments.index') }}">Appointments</a>
                    @endif
                    @if (Route::has('provider.schedules.index'))
                        <a class="link" href="{{ route('provider.schedules.index') }}">My Schedules</a>
                    @endif
                    @if (Route::has('provider.time-offs.index'))
                        <a class="link" href="{{ route('provider.time-offs.index') }}">Time Offs</a>
                    @endif
                @endif
                @if (auth()->user()->isRole('customer'))
                    @if (Route::has('my.appointments.index'))
                        <a class="link" href="{{ route('my.appointments.index') }}">My Appointments</a>
                    @endif
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                </form>
            @endauth

            @guest
                @if (Route::has('login'))
                    <a class="link" href="{{ route('login') }}">Login</a>
                @endif
                @if (Route::has('register.show'))
                    <a class="link" href="{{ route('register.show') }}">Register</a>
                @endif
            @endguest
        </div>
    </nav>

    <main>
        @yield('content')
    </main>
</body>
</html>
