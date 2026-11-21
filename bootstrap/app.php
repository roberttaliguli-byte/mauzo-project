<?php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',   // ← ADDED
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\UpdateUserActivity::class,
            \App\Http\Middleware\CheckPackageExpiry::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'check.package' => \App\Http\Middleware\CheckPackageExpiry::class,
            'auth.any' => \App\Http\Middleware\AuthAnyGuard::class,
            'ImageHelper' => App\Helpers\ImageHelper::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'pesapal/ipn',
            'pesapal/callback',
            'payment/ipn',
            'payment/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();