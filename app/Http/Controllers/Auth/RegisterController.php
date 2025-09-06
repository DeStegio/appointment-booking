<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function show()
    {
        return view('auth.register');
    }

    /**
     * Handle the registration submission (placeholder).
     */
    public function store(\Illuminate\Http\Request $request)
    {
        // Intentionally minimal: redirect to login until implemented
        return redirect()->route('login');
    }
}
