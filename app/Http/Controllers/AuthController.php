<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show the login view.
     */
    public function showLogin()
    {
        return view('login'); 
    }

    /**
     * Process Login.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if ($this->authService->login($credentials, $request)) {
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Access Denied: Hunter Identity not found.',
        ])->withInput($request->only('email'));
    }

    /**
     * Logout system.
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request);
        return redirect('/login');
    }
}