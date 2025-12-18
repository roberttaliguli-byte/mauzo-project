<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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
        // Check if user is authenticated and has admin role
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Optionally, check boss role if you want bosses to access some admin pages
        // if (Auth::check() && Auth::user()->role === 'boss') {
        //     return $next($request);
        // }

        // If not admin, redirect to login or show 403
        if ($request->expectsJson()) {
            // API request → return JSON 403
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        // Web request → redirect to login with error
        return redirect()->route('login')->withErrors([
            'login' => 'Huna ruhusa ya kufikia ukurasa huu.'
        ]);
    }
}
