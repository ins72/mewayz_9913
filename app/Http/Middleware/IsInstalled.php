<?php

namespace App\Http\Middleware;

use Closure;

class IsInstalled
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
       if (!is_installed() && !\Str::is('install*', $request->path())) {
        return redirect()->route('install-index')->send();
       }
       return $next($request);
    }
}
