<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun lu belum aktif wok, silahkan infaq seikhlasnya dulu buat aktivasi!',
                'error_code' => 'ACTIVATION_REQUIRED',
                'data' => [
                    'is_active' => false
                ]
            ], 403);
        }

        return $next($request);
    }
}
