<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <style>
        :root { --c1:#0d6efd; --c2:#198754; --muted:#6c757d; }
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin:0; }
        nav { display:flex; justify-content:space-between; align-items:center; padding:0.75rem 1rem; border-bottom:1px solid #eee; }
        nav a { margin-right:0.75rem; text-decoration:none; color:#0d6efd; }
        nav a:last-child { margin-right:0; }
        main { padding:1.25rem; }
        .brand { color:#000; text-decoration:none; font-weight:600; }
        form.inline { display:inline; margin:0; }
        button.linky { background:none; border:none; color:#dc3545; cursor:pointer; padding:0; font:inherit; }
        .user { color:#000; margin-right:0.75rem; }
    </style>
</head>
<body>
    <nav>
        <div>
            <a class="brand" href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>
        </div>
        <div>
            @auth
                <span class="user">{{ auth()->user()->name }}</span>
                @if (Route::has('dashboard'))
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a href="{{ url('/') }}">Dashboard</a>
                @endif

                @if (auth()->user()->isRole('provider'))
                    @if (Route::has('provider.services.index'))
                        <a href="{{ route('provider.services.index') }}">Services</a>
                    @endif
                    @if (Route::has('provider.schedules.index'))
                        <a href="{{ route('provider.schedules.index') }}">My Schedules</a>
                    @endif
                    @if (Route::has('provider.time-offs.index'))
                        <a href="{{ route('provider.time-offs.index') }}">Time Offs</a>
                    @endif
                @endif
                @if (auth()->user()->isRole('customer'))
                    <span class="user" style="color:#6c757d;">Find slots (manual): /providers/{providerId}/services/{serviceId}/slots?date=YYYY-MM-DD</span>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="linky">Logout</button>
                </form>
            @endauth

            @guest
                @if (Route::has('login'))
                    <a href="{{ route('login') }}">Login</a>
                @endif
                @if (Route::has('register.show'))
                    <a href="{{ route('register.show') }}">Register</a>
                @endif
            @endguest
        </div>
    </nav>

    <main>
        @yield('content')
    </main>
</body>
</html>
