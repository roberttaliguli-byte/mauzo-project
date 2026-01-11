<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Determine guard
        $guard = $role === 'mfanyakazi' ? 'mfanyakazi' : 'web';

        if (!Auth::guard($guard)->check()) {
            return redirect()->route('login')
                ->withErrors(['auth' => 'Tafadhali ingia kwanza.']);
        }

        $user = Auth::guard($guard)->user();

        // SAFETY CHECK (CRITICAL)
        if (!isset($user->role) || !is_string($user->role)) {
            Auth::guard($guard)->logout();

            return redirect()->route('login')
                ->withErrors(['auth' => 'Akaunti yako haina ruhusa sahihi.']);
        }

        if (strtolower(trim($user->role)) !== strtolower(trim($role))) {
            Auth::guard($guard)->logout();

            return redirect()->route('login')
                ->withErrors(['auth' => 'Huna ruhusa ya kufikia ukurasa huu.']);
        }

        return $next($request);
    }
}
