<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // For employee routes
        if ($role === 'mfanyakazi') {
            // Check employee guard
            if (!Auth::guard('mfanyakazi')->check()) {
                return redirect()->route('login')
                    ->withErrors(['auth' => 'Tafadhali ingia kama mfanyakazi.']);
            }

            $user = Auth::guard('mfanyakazi')->user();
            
            // Check if employee is allowed to login (using getini field)
            if (!isset($user->getini) || $user->getini !== 'ingia') {
                Auth::guard('mfanyakazi')->logout();
                return redirect()->route('login')
                    ->withErrors(['auth' => 'Akaunti yako haijaidhinishwa kuingia.']);
            }
            
            // Employee is valid, proceed
            return $next($request);
        }
        
        // For boss and admin routes (web guard)
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login')
                ->withErrors(['auth' => 'Tafadhali ingia kwanza.']);
        }

        $user = Auth::guard('web')->user();

        // Check if user has role property
        if (!isset($user->role) || !is_string($user->role)) {
            Auth::guard('web')->logout();
            return redirect()->route('login')
                ->withErrors(['auth' => 'Akaunti yako haina ruhusa sahihi.']);
        }

        // Check if role matches
        if (strtolower(trim($user->role)) !== strtolower(trim($role))) {
            Auth::guard('web')->logout();
            return redirect()->route('login')
                ->withErrors(['auth' => 'Huna ruhusa ya kufikia ukurasa huu.']);
        }

        return $next($request);
    }
}