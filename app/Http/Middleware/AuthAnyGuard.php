<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthAnyGuard
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated in either guard
        if (Auth::check() || Auth::guard('mfanyakazi')->check()) {
            return $next($request);
        }

        // If not authenticated in either guard, redirect to login
        return redirect()->route('login')
            ->withErrors(['auth' => 'Tafadhali ingia kwanza.']);
    }
}