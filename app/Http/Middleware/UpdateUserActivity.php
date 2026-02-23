<?php
// app/Http/Middleware/UpdateUserActivity.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdateUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Update last_activity_at
            $user->last_activity_at = now();
            $user->save();
            
            Log::info('Updated activity for user: ' . $user->id . ' at ' . now());
        }

        // Also check for employee guard
        if (Auth::guard('mfanyakazi')->check()) {
            $employee = Auth::guard('mfanyakazi')->user();
            
            // If employee model has last_activity_at column
            if (isset($employee->last_activity_at)) {
                $employee->last_activity_at = now();
                $employee->save();
                
                Log::info('Updated activity for employee: ' . $employee->id . ' at ' . now());
            }
        }

        return $next($request);
    }
}