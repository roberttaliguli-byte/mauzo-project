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
                // Note: manunuzi.index/store allowed for mdogo (can view and create), only update/destroy restricted
                $bossOnlyRoutes = [
                    'dashboard',
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

                // Manunuzi: mdogo can view and create, but not edit/delete
                $manunuziRestrictedRoutes = [
                    'manunuzi.update',
                    'manunuzi.destroy',
                ];
                
                if (in_array($currentRoute, $bossOnlyRoutes)) {
                    Auth::guard('mfanyakazi')->logout();
                    return redirect()->route('login')
                        ->with('error', 'Huna ruhusa ya kufikia ukurasa huu. Wasiliana na msimamizi.');
                }

                if (in_array($currentRoute, $manunuziRestrictedRoutes)) {
                    // Allow page access, but block edit/delete — return 403 JSON or redirect back
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Huna ruhusa ya kuhariri/kufuta manunuzi. Ruhusa ya kuingiza tu.'], 403);
                    }
                    return redirect()->back()->with('error', 'Huna ruhusa ya kuhariri/kufuta manunuzi. Ruhusa ya kuingiza tu.');
                }
            }
        }
        
        return $next($request);
    }
}