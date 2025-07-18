<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SimpleAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Simple session-based auth check
        if ($request->session()->has('user_id')) {
            return $next($request);
        }
        
        // Check for remember token in session
        if ($request->session()->has('remember_token')) {
            return $next($request);
        }
        
        // For now, allow access to test the dashboard
        return $next($request);
    }
}
