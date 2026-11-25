<?php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        // A stale session/CSRF token normally renders Laravel's "419 | Page Expired"
        // screen. Instead, end the session and return the user to the login page.
        $exceptions->render(function (HttpException $e, $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            // Log out of every guard and start a clean session.
            Auth::guard('web')->logout();
            Auth::guard('mfanyakazi')->logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            $message = 'Muda wako wa kuingia umeisha. Tafadhali ingia tena.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'session_expired' => true,
                    'redirect' => route('login'),
                ], 419);
            }

            return redirect()->route('login')->with('error', $message);
        });
    })
    ->create();