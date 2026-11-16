<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Only the authenticated Boss (web guard) may pass. Employees using the
 * 'mfanyakazi' guard are rejected even if they have the "mkubwa" ability.
 */
class BossOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('web')->user();

        if (!$user || !$user->company_id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Boss access only.'], 403);
            }
            abort(403, 'This page is for the Boss only.');
        }

        return $next($request);
    }
}
