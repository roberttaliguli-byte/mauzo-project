<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthAnyGuard
{
    public function handle(Request $request, Closure $next)
    {
        // Check web guard (boss/admin) first
        if (Auth::check()) {
            return $next($request);
        }
        
        // Check employee guard
        if (Auth::guard('mfanyakazi')->check()) {
            $employee = Auth::guard('mfanyakazi')->user();
            $currentRoute = $request->route()->getName();
            
            // DEBUG: Log to see what's happening
            \Log::info('Employee accessing route', [
                'route' => $currentRoute,
                'uwezo' => $employee->uwezo,
                'name' => $employee->jina
            ]);
            
            // List of routes that are RESTRICTED for mdogo employees only
            // Mkubwa employees can access these
            $restrictedRoutes = [
                // Dashboard
                'dashboard',
                
                // Manunuzi (Purchases)
                'manunuzi.index',
                'manunuzi.store',
                'manunuzi.update',
                'manunuzi.destroy',
                
                // Uchambuzi (Analysis)
                'uchambuzi.index',
                'uchambuzi.mwenendo.range',
                
                // Wafanyakazi (Employees)
                'wafanyakazi.index',
                'wafanyakazi.store',
                'wafanyakazi.edit',
                'wafanyakazi.update',
                'wafanyakazi.destroy',
                'wafanyakazi.export.pdf',
                
                // Masaplaya (Competitions)
                'masaplaya.index',
                'masaplaya.store',
                'masaplaya.update',
                'masaplaya.destroy',
                
                // Reports
                'user.reports.select',
                'user.reports.generate',
                'user.reports.download',
            ];
            
            // If this is a restricted route
            if (in_array($currentRoute, $restrictedRoutes)) {
                // Allow ONLY if employee has uwezo mkubwa
                if ($employee->uwezo === 'mkubwa') {
                    \Log::info('Mkubwa employee allowed access to restricted route', [
                        'route' => $currentRoute,
                        'employee' => $employee->jina
                    ]);
                    return $next($request);
                } else {
                    // Block mdogo employees
                    \Log::warning('Mdogo employee blocked from restricted route', [
                        'route' => $currentRoute,
                        'employee' => $employee->jina,
                        'id' => $employee->id
                    ]);
                    
                    // Logout and redirect
                    Auth::guard('mfanyakazi')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect()->route('login')
                        ->with('error', 'Huna ruhusa ya kufikia ukurasa huu. Ukurasa huu unahitaji uwezo mkubwa.');
                }
            }
            
            // Allow access for all employees to non-restricted routes
            return $next($request);
        }

        // If not authenticated in either guard, redirect to login
        return redirect()->route('login')
            ->withErrors(['auth' => 'Tafadhali ingia kwanza.']);
    }
}