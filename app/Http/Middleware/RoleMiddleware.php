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
            if (!Auth::guard('mfanyakazi')->check()) {
                return redirect()->route('login')
                    ->withErrors(['auth' => 'Tafadhali ingia kama mfanyakazi.']);
            }

            $user = Auth::guard('mfanyakazi')->user();
            
            // Check if employee is allowed to login
            if (!isset($user->getini) || $user->getini !== 'ingia') {
                Auth::guard('mfanyakazi')->logout();
                return redirect()->route('login')
                    ->withErrors(['auth' => 'Akaunti yako haijaidhinishwa kuingia.']);
            }
            
            // Check if employee has full access (uwezo mkubwa)
            // If yes, they can access boss-level routes
            if ($user->uwezo === 'mkubwa') {
                return $next($request);
            }
            
            // Regular employee - proceed
            return $next($request);
        }
        
        // For boss and admin routes (web guard)
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login')
                ->withErrors(['auth' => 'Tafadhali ingia kwanza.']);
        }

        $user = Auth::guard('web')->user();

        if (!isset($user->role) || !is_string($user->role)) {
            Auth::guard('web')->logout();
            return redirect()->route('login')
                ->withErrors(['auth' => 'Akaunti yako haina ruhusa sahihi.']);
        }

        if (strtolower(trim($user->role)) !== strtolower(trim($role))) {
            Auth::guard('web')->logout();
            return redirect()->route('login')
                ->withErrors(['auth' => 'Huna ruhusa ya kufikia ukurasa huu.']);
        }

        return $next($request);
    }
}