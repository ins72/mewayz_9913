<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\MediakitSite;

class HandleMediakit
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
        $_site = MediakitSite::where('_slug', $request->slug)->first();
        if(!$_site) abort(404);

        $request->merge([
            '_site' => $_site
        ]);

        return $next($request);
    }
}
