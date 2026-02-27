<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthService
{
    /**
     * Handle user login attempt.
     */
    public function login(array $credentials, Request $request): bool
    {
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return true;
        }

        return false;
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
