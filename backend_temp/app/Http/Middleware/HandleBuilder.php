<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Site;

class HandleBuilder
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        // Check for current organization
        $_site = Site::where('_slug', $request->slug)->first();
        if(!$_site) abort(404);


        $access = $_site->canAccess();
        if($_site->user_id !== iam()->id) $access = false;
        if(iam()->isAdmin()) $access = true;


        // dd($_site->fullAccess());
        if(!$access) abort(404);


        $request->merge([
            '_site' => $_site
        ]);

        return $next($request);
    }
}
