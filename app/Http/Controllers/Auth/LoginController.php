<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function show()
    {
        return view('auth.login');
    }
}

