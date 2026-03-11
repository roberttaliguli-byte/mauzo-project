<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ClearSessionOnLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // If user is trying to access login page but already authenticated
        if ($request->routeIs('login') && Auth::check()) {
            // Logout current user before showing login page
            $this->logoutCurrentUser($request);
        }
        
        // Check if this is a new login (has remember token but session expired)
        if ($request->hasCookie('remember_web_*') && !Auth::check()) {
            // Clear any stale session data
            Session::flush();
        }

        return $next($request);
    }

    /**
     * Logout current user and clear session
     */
    private function logoutCurrentUser(Request $request)
    {
        // Logout from all guards
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }
        if (Auth::guard('mfanyakazi')->check()) {
            Auth::guard('mfanyakazi')->logout();
        }
        
        // Clear all session data
        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}