<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class LoginPlainController extends Controller
{
    public function __invoke(): Response
    {
        return response('<h1>login-plain</h1>', 200, ['Content-Type' => 'text/html']);
    }
}

