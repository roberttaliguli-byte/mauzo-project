<?php
// app/Http/Middleware/UpdateUserActivity.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        // Update activity for boss (User)
        if (Auth::check()) {
            $user = Auth::user();
            $user->last_activity_at = now();
            $user->save();
        }

        // Update activity for employee (Wafanyakazi)
        if (Auth::guard('mfanyakazi')->check()) {
            $employee = Auth::guard('mfanyakazi')->user();
            if (isset($employee->last_activity_at)) {
                $employee->last_activity_at = now();
                $employee->save();
            }
        }

        return $next($request);
    }
}