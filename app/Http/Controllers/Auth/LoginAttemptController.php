<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginAttemptController extends Controller
{
    public function __invoke(Request $request)
    {
        // Delegate to the same behavior as the main login method
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = (bool) ($credentials['remember'] ?? false);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();

            // If JSON is expected, send JSON; otherwise redirect
            if ($request->expectsJson()) {
                return response()->json(['ok' => true, 'redirect' => route('dashboard')]);
            }

            return redirect()->route('dashboard');
        }

        $errors = ['email' => __('auth.failed')];

        if ($request->expectsJson()) {
            return response()->json(['ok' => false, 'errors' => $errors], 422);
        }

        return back()->withErrors($errors)->onlyInput('email');
    }
}
