<?php
// app/Http/Middleware/CheckPackageExpiry.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckPackageExpiry
{
    public function handle(Request $request, Closure $next)
    {
        // If not logged in, just continue
        if (!Auth::check()) {
            return $next($request);
        }
        
        $user = Auth::user();
        
        // SAFETY CHECK: Make sure user object has role property
        if (!isset($user->role)) {
            return $next($request);
        }
        
        // Skip for admin users
        if ($user->role === 'admin') {
            return $next($request);
        }

        // ===== IMPORTANT: Skip package check for payment-related routes =====
        $currentRoute = $request->route() ? $request->route()->getName() : '';
        
        $excludedRoutes = [
            'payment.package.selection',
            'payment.form',
            'payment.process',
            'payment.success',
            'payment.failed',
            'payment.status',
            'payment.retry',
            'pesapal.callback',
            'pesapal.ipn',
            'logout'
        ];
        
        if (in_array($currentRoute, $excludedRoutes)) {
            return $next($request);
        }
        
        // Also skip if URL contains payment-related paths
        $currentPath = $request->path();
        $excludedPaths = [
            'package-selection',
            'payment',
            'pesapal'
        ];
        
        foreach ($excludedPaths as $path) {
            if (str_contains($currentPath, $path)) {
                return $next($request);
            }
        }

        // Check if user has company
        if ($user && $user->company) {
            $company = $user->company;
            
            // If package_end is null, redirect to package selection
            if (is_null($company->package_end)) {
                return redirect()->route('payment.package.selection')
                    ->with('warning', 'Tafadhali chagua pakiti ili kuendelea.');
            }
            
            // Check if package is expired
            $packageEnd = Carbon::parse($company->package_end);
            if ($packageEnd->isPast()) {
                return redirect()->route('payment.package.selection')
                    ->with('error', 'Pakiti yako imeisha muda. Tafadhali fanya malipo ili kuendelea kutumia mfumo.');
            }
            
            // Optional: Warn if package expires soon (within 7 days)
            $daysLeft = Carbon::now()->diffInDays($packageEnd, false);
            if ($daysLeft <= 7 && $daysLeft > 0) {
                session()->flash('warning', "Pakiti yako itaisha muda baada ya siku {$daysLeft}. Tafadhali fanya malipo ili kuepuka usumbufu.");
            }
        }

        return $next($request);
    }
}