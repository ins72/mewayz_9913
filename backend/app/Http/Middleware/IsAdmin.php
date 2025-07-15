<?php

namespace App\Http\Middleware;

use Closure;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {  
        // if (env('APP_DEMO') && isset($_POST['_token'])) {
        //     return back()->with('error', __('Action disabled in demo.'));
        // }
        // if ($request->user() && $request->user()->role == 1 && !\SandyTeam::is_set_team()) {
        //     return $next($request);
        // }
        
        header("Access-Control-Allow-Origin: *"); 
        if ($request->user() && $request->user()->role == 1) {
            return $next($request);
        }
        return abort(404);
    }
}
