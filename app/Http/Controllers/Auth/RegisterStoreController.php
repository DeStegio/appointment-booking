<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class RegisterStoreController extends Controller
{
    public function __invoke(): JsonResponse
    {
        // Placeholder for register handling
        return response()->json(['message' => 'Not Implemented'], 501);
    }
}

