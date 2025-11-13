<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
        // Check if session has admin flag
        if ($request->session()->get('is_admin', false) === true) {
            return $next($request);
        }

        // Otherwise, redirect to login with error
        return redirect()->route('login')->withErrors([
            'login' => 'Huna ruhusa ya kufikia ukurasa huu.'
        ]);
    }
}
