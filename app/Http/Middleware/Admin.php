<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user()->load('rankTier');
        
        // Logika: Hanya ID 1 atau Rank S yang diperbolehkan masuk area admin
        if ($user->id === 1 || (optional($user->rankTier)->slug === 'S')) {
            return $next($request);
        }

        // Jika bukan admin, tendang ke home
        return redirect('/')->with('error', 'Maaf, Anda tidak memiliki akses ke halaman ini.');
    }
}
