<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Usage: ->middleware(['auth','role:boss']) or ->middleware(['auth','role:mfanyakazi'])
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            // not logged in
            return redirect()->route('login');
        }

        if (!in_array($user->role, $roles)) {
            // you can redirect to their dashboard instead of abort
            if ($user->role === 'mfanyakazi') {
                return redirect()->route('mfanyakazi.dashboard')->with('error', 'Huna ruhusa kufikia ukurasa huu.');
            }
            return redirect()->route('dashboard')->with('error', 'Huna ruhusa kufikia ukurasa huu.');
        }

        return $next($request);
    }
}
