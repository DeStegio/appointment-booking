<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/theme.js') }}" defer></script>
</head>
<body>
    <div class="navbar">
      <div class="inner">
        <div class="brand"><a class="link focus-ring" href="{{ url('/') }}">{{ config('app.name', 'Appointment Booking') }}</a></div>
        <div class="nav">
          @if (Route::has('providers.index'))
            <a class="focus-ring" href="{{ route('providers.index') }}">Find a Provider</a>
          @endif
          @auth
            @if (Route::has('dashboard'))
              <a class="focus-ring" href="{{ route('dashboard') }}">Dashboard</a>
            @else
              <a class="focus-ring" href="{{ url('/') }}">Dashboard</a>
            @endif

            @if (auth()->user()->isRole('admin'))
              @if (Route::has('admin.dashboard'))
                <a class="focus-ring" href="{{ route('admin.dashboard') }}">Admin</a>
              @endif
            @endif

            @if (auth()->user()->isRole('provider'))
              @if (Route::has('provider.services.index'))
                <a class="focus-ring" href="{{ route('provider.services.index') }}">Services</a>
              @endif
              @if (Route::has('calendar.day'))
                <a class="focus-ring" href="{{ route('calendar.day') }}">Calendar</a>
              @endif
              @if (Route::has('provider.appointments.index'))
                <a class="focus-ring" href="{{ route('provider.appointments.index') }}">Appointments</a>
              @endif
              @if (Route::has('provider.schedules.index'))
                <a class="focus-ring" href="{{ route('provider.schedules.index') }}">My Schedules</a>
              @endif
              @if (Route::has('provider.time-offs.index'))
                <a class="focus-ring" href="{{ route('provider.time-offs.index') }}">Time Offs</a>
              @endif
            @endif
            @if (auth()->user()->isRole('customer'))
              @if (Route::has('my.appointments.index'))
                <a class="focus-ring" href="{{ route('my.appointments.index') }}">My Appointments</a>
              @endif
            @endif
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="btn btn-sm btn-muted focus-ring" type="submit">Logout</button>
            </form>
          @endauth

          @guest
            @if (Route::has('login'))
              <a class="focus-ring" href="{{ route('login') }}">Login</a>
            @endif
            @if (Route::has('register.show'))
              <a class="focus-ring" href="{{ route('register.show') }}">Register</a>
            @endif
          @endguest
          <button id="themeToggle" class="btn btn-sm btn-muted focus-ring" type="button" aria-pressed="false">🖥️ System</button>
        </div>
      </div>
    </div>

    @if (session('status'))
      <div class="container"><div class="alert alert-success fade-in">{{ session('status') }}</div></div>
    @endif
    @if ($errors->any())
      <div class="container"><div class="alert alert-danger fade-in">
        <strong>There were errors:</strong>
        <ul class="mt-1">
          @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div></div>
    @endif

    <main>
        @yield('content')
    </main>
</body>
</html>
