<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckEmployeeAccess
{
    public function handle(Request $request, Closure $next)
    {
        // If logged in as employee
        if (Auth::guard('mfanyakazi')->check()) {
            $employee = Auth::guard('mfanyakazi')->user();
            
            // If employee has uwezo mdogo, they cannot access boss routes
            if ($employee->uwezo === 'mdogo') {
                $currentRoute = $request->route()->getName();
                
                // Boss-only routes that mdogo employees cannot access
                $bossOnlyRoutes = [
                    'dashboard',
                    'manunuzi.index',
                    'manunuzi.store',
                    'manunuzi.update',
                    'manunuzi.destroy',
                    'uchambuzi.index',
                    'uchambuzi.mwenendo.range',
                    'wafanyakazi.index',
                    'wafanyakazi.store',
                    'wafanyakazi.edit',
                    'wafanyakazi.update',
                    'wafanyakazi.destroy',
                    'wafanyakazi.export.pdf',
                    'masaplaya.index',
                    'masaplaya.store',
                    'masaplaya.update',
                    'masaplaya.destroy',
                    'user.reports.select',
                    'user.reports.generate',
                    'user.reports.download',
                ];
                
                if (in_array($currentRoute, $bossOnlyRoutes)) {
                    Auth::guard('mfanyakazi')->logout();
                    return redirect()->route('login')
                        ->with('error', 'Huna ruhusa ya kufikia ukurasa huu. Wasiliana na msimamizi.');
                }
            }
        }
        
        return $next($request);
    }
}