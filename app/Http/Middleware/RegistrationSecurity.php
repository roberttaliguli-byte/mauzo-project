<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RegistrationSecurity
{
    public function handle(Request $request, Closure $next)
    {
        // Check if IP is blacklisted
        $ip = $request->ip();
        $blacklistKey = 'ip_blacklist:' . $ip;
        
        if (Cache::has($blacklistKey)) {
            \Log::warning('Blocked blacklisted IP from registration', ['ip' => $ip]);
            return redirect()->route('login')
                ->with('error', 'Imeshindikana kusajili. Tafadhali wasiliana na msimamizi.');
        }
        
        // Check user agent - block suspicious ones
        $userAgent = $request->userAgent();
        $suspiciousAgents = [
            'curl', 'wget', 'python', 'java', 'perl', 'ruby', 
            'php', 'go', 'node', 'axel', 'aria2', 'wget'
        ];
        
        foreach ($suspiciousAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                \Log::warning('Blocked suspicious user agent', [
                    'ip' => $ip,
                    'user_agent' => $userAgent
                ]);
                return redirect()->route('login')
                    ->with('error', 'Imeshindikana kusajili. Tafadhali wasiliana na msimamizi.');
            }
        }
        
        return $next($request);
    }
}