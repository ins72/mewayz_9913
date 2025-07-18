<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\App;

class SwitchLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {  
        try {
            $locale = Cookie::get('yenaLocale');
            
            if ($locale && is_string($locale) && in_array($locale, ['en', 'ar', 'af', 'ja', 'sq'])) {
                App::setLocale($locale);
            } else {
                // Set default locale if cookie is invalid
                App::setLocale('en');
            }
        } catch (\Exception $e) {
            // Fallback to default locale on any error
            App::setLocale('en');
        }
        
        return $next($request);
    }
}
