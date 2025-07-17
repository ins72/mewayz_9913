<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
// use Laravel\Folio\Folio;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            // Use safe IP-based rate limiting to avoid auth issues
            return Limit::perMinute(60)->by($request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        // Configure Folio routes for authenticated dashboard pages
        // Folio::path(resource_path('views/pages/dashboard'))->uri('/dashboard')->middleware([
        //     '*' => ['web', 'auth'],
        // ]);
        
        // Configure Folio routes for public pages
        // Folio::path(resource_path('views/pages'))->middleware([
        //     '*' => ['web'],
        // ]);
    }
}
