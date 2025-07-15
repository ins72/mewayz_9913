<?php

namespace App\Yena\Pexels;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

final class PexelsServiceProvider extends BaseServiceProvider
{
    /**
     * Boot publishable resources
     */
    public function boot(): void
    {
        // $this->bootPublishes();
    }

    /**
     * Register package resources
     */
    public function register(): void
    {
        $this->registerFacade();
    }

    /**
     * Register related facade
     */
    protected function registerFacade(): void
    {
        $this->app->bind('pexels', function ($app) {
            return new Pexels();
        });
    }
}
