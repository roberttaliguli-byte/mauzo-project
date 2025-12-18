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
        // Determine which guard to use based on role
        $guard = $role === 'mfanyakazi' ? 'mfanyakazi' : 'web';

        $user = Auth::guard($guard)->user();

        // Not logged in
        if (!$user) {
            return redirect()->route('login')->with('error', 'Tafadhali ingia kwanza.');
        }

        // Role mismatch
        if ($user->role !== $role) {
            Auth::guard($guard)->logout(); // log out user if role mismatch
            return redirect()->route('login')->with('error', 'Huaruhusiwi kuingia hapa.');
        }

        // Everything ok
        return $next($request);
    }
}
