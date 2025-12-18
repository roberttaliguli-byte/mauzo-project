<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckCompanyPackage
{
    public function handle(Request $request, Closure $next)
    {
        // ===============================
        // Routes that MUST bypass package check
        // ===============================
        $allowedRoutes = [
            'package.blocked',   // Blocked page
            'package.choose',    // Choose package page
            'package.pay',       // Start payment (POST)
            'package.success',   // Pesapal callback
            'package.payment-status',
            'package.check-status',
            'package.retry',
            'package.cancel',
            'pesapal.callback',
            'logout',
        ];

        // If route name exists and is allowed → bypass
        if ($request->route() && in_array($request->route()->getName(), $allowedRoutes)) {
            Log::info("PACKAGE CHECK BYPASSED FOR ROUTE: " . $request->route()->getName());
            return $next($request);
        }

        // ===============================
        // If not logged in → allow
        // ===============================
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // ===============================
        // Admin users bypass package check
        // ===============================
        if (!empty($user->role) && $user->role === 'admin') {
            Log::info("ADMIN USER BYPASSED PACKAGE CHECK (User ID {$user->id})");
            return $next($request);
        }

        // ===============================
        // Get user's company
        // ===============================
        $company = $user->company;

        if (!$company) {
            Log::warning("USER ID {$user->id} HAS NO COMPANY → REDIRECT TO BLOCKED");
            return redirect()->route('package.blocked');
        }

        // ===============================
        // No package → blocked
        // ===============================
        if (!$company->package_end) {
            Log::warning("COMPANY ID {$company->id} HAS NO ACTIVE PACKAGE → BLOCKED");
            return redirect()->route('package.blocked');
        }

        // ===============================
        // Package expired → blocked
        // ===============================
        if (Carbon::now()->greaterThan(Carbon::parse($company->package_end))) {
            Log::warning("PACKAGE EXPIRED FOR COMPANY ID {$company->id} → BLOCKED");
            return redirect()->route('package.blocked');
        }

        // ===============================
        // Package active → allow access
        // ===============================
        return $next($request);
    }
}
