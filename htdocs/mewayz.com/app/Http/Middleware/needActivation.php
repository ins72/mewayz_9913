<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Userdomain;
use Route;

class needActivation{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        if ($user = $request->user()) {
            if (settings('user.email_verification') && !$user->role && !empty($user->emailToken) && !Route::is('user-need-activate-email')) {
                return redirect()->route('user-need-activate-email')->send();
            }
        }

        return $next($request);
    }
}
