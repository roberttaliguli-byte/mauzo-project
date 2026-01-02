<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // For mfanyakazi, use the mfanyakazi guard
        if ($role === 'mfanyakazi') {
            $user = Auth::guard('mfanyakazi')->user();
            $guard = 'mfanyakazi';
        } else {
            // For boss and admin, use web guard
            $user = Auth::guard('web')->user();
            $guard = 'web';
        }

        // Not logged in
        if (!$user) {
            return redirect()->route('login')->with('error', 'Tafadhali ingia kwanza.');
        }

        // Role mismatch (case-insensitive)
        if (strtolower($user->role) !== strtolower($role)) {
            Auth::guard($guard)->logout();
            return redirect()->route('login')->with('error', 'Huaruhusiwi kuingia hapa.');
        }

        // Everything ok
        return $next($request);
    }
}