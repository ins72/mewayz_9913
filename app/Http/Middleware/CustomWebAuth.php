<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class CustomWebAuth
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
        // Check if user is logged in via session
        $userId = Session::get('user_id');
        
        if (!$userId) {
            // If not authenticated, redirect to login
            return redirect()->route('login');
        }
        
        // Get the user
        $user = User::find($userId);
        
        if (!$user) {
            // User not found, clear session and redirect to login
            Session::forget('user_id');
            return redirect()->route('login');
        }
        
        // Set the authenticated user on the request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        
        return $next($request);
    }
}
