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
}

