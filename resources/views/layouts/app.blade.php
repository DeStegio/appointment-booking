<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/theme.js') }}" defer></script>
    <script src="{{ asset('js/ui.js') }}" defer></script>
</head>
<body>
    <a href="#main" class="sr-only focus-ring">Skip to content</a>
    <div class="navbar" role="navigation" aria-label="Primary">
      <div class="container inner">
        <div class="brand"><a class="link focus-ring" href="{{ url('/') }}">{{ config('app.name', 'Appointment Booking') }}</a></div>
        <div class="nav">
          @if (Route::has('providers.index'))
            <a class="focus-ring nav-link" href="{{ route('providers.index') }}" @if(request()->routeIs('providers.*')) aria-current="page" @endif>Find a Provider</a>
          @endif
          @auth
            @if (Route::has('dashboard'))
              <a class="focus-ring nav-link" href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>Dashboard</a>
            @else
              <a class="focus-ring nav-link" href="{{ url('/') }}">Dashboard</a>
            @endif

            @if (auth()->user()->isRole('admin'))
              @if (Route::has('admin.dashboard'))
                <a class="focus-ring nav-link" href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.*')) aria-current="page" @endif>Admin</a>
              @endif
            @endif

            @if (auth()->user()->isRole('provider'))
              @if (Route::has('provider.services.index'))
                <a class="focus-ring nav-link" href="{{ route('provider.services.index') }}" @if(request()->routeIs('provider.services.*')) aria-current="page" @endif>Services</a>
              @endif
              @if (Route::has('calendar.day'))
                <a class="focus-ring nav-link" href="{{ route('calendar.day') }}" @if(request()->routeIs('calendar.day')) aria-current="page" @endif>Calendar</a>
              @endif
              @if (Route::has('provider.appointments.index'))
                <a class="focus-ring nav-link" href="{{ route('provider.appointments.index') }}" @if(request()->routeIs('provider.appointments.*')) aria-current="page" @endif>Appointments</a>
              @endif
              @if (Route::has('provider.schedules.index'))
                <a class="focus-ring nav-link" href="{{ route('provider.schedules.index') }}" @if(request()->routeIs('provider.schedules.*')) aria-current="page" @endif>My Schedules</a>
              @endif
              @if (Route::has('provider.time-offs.index'))
                <a class="focus-ring nav-link" href="{{ route('provider.time-offs.index') }}" @if(request()->routeIs('provider.time-offs.*')) aria-current="page" @endif>Time Offs</a>
              @endif
            @endif
            @if (auth()->user()->isRole('customer'))
              @if (Route::has('my.appointments.index'))
                <a class="focus-ring nav-link" href="{{ route('my.appointments.index') }}" @if(request()->routeIs('my.appointments.*')) aria-current="page" @endif>My Appointments</a>
              @endif
            @endif
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="btn btn-sm btn-muted focus-ring" type="submit">Logout</button>
            </form>
          @endauth

          @guest
            @if (Route::has('login'))
              <a class="focus-ring nav-link" href="{{ route('login') }}" @if(request()->routeIs('login')) aria-current="page" @endif>Login</a>
            @endif
            @if (Route::has('register.show'))
              <a class="focus-ring nav-link" href="{{ route('register.show') }}" @if(request()->routeIs('register.show')) aria-current="page" @endif>Register</a>
            @endif
          @endguest
          <button id="themeToggle" class="btn btn-sm btn-muted focus-ring" type="button" aria-pressed="false">Theme: System</button>
        </div>
      </div>
    </div>

    @include('partials.alerts')

    <main id="main" class="container section">
      @yield('content')
    </main>

    <footer class="footer">
      <div class="container">
        <div class="flex items-center justify-between">
          <div class="muted">&copy; {{ date('Y') }} {{ config('app.name', 'Appointment Booking') }}</div>
          <div><a class="link" href="{{ route('healthz') }}">Status</a></div>
        </div>
      </div>
    </footer>
</body>
</html>

