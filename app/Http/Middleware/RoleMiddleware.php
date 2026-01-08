<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
public function handle(Request $request, Closure $next, $role)
{
    // Only mfanyakazi needs separate guard
    $guard = $role === 'mfanyakazi' ? 'mfanyakazi' : 'web';

    $user = Auth::guard($guard)->user();

    if (!$user) {
        return redirect()->route('login')
            ->withErrors(['auth' => 'Tafadhali ingia kwanza.']);
    }

    if (strtolower($user->role) !== strtolower($role)) {
        Auth::guard($guard)->logout();

        return redirect()->route('login')
            ->withErrors(['auth' => 'Huna ruhusa ya kufikia ukurasa huu.']);
    }

    return $next($request);
}

}
