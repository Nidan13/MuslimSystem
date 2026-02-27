<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Allow all CORS for API routes (required for Flutter + ngrok)
        $middleware->statefulApi();
        $middleware->validateCsrfTokens(except: [
            'api/prismalink/webhook',
            'api/payments/callback',
        ]);

        // Register active_user middleware alias
        $middleware->alias([
            'active_user' => \App\Http\Middleware\ActiveUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
