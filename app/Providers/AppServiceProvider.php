<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\YenaOauth\ProviderRedirector;
use App\YenaOauth\ProviderRepository;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Socialite\UserProviderRedirector;
use App\Socialite\UserProviderRepository;
use Livewire\Livewire;
use App\Livewire\Pages\Auth\LoginModal;
use App\Livewire\Pages\Auth\RegisterModal;
use App\Livewire\Pages\Auth\ForgotPasswordModal;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        config([
            'filesystems.disks.local.url' => url('/'),
        ]);
        // //
        // $this->app->bind(ProviderRedirector::class, UserProviderRedirector::class);
        // $this->app->bind(ProviderRepository::class, UserProviderRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Directives
        $this->registerDirectives();

        // Register annoymous component
        Blade::anonymousComponentPath(resource_path('views/livewire'), 'livewire');
        //

        $this->registerBioSections();

        
        // Register Sections
        $this->registerSections();
        
        // Register Payments
        $this->registerPayments();

        // Register custom Livewire components
        Livewire::component('pages.auth.login-modal', LoginModal::class);
        Livewire::component('pages.auth.register-modal', RegisterModal::class);
        Livewire::component('pages.auth.forgot-password-modal', ForgotPasswordModal::class);

        // URL::forceScheme('https');
    }


    public function registerDirectives(){
        Blade::directive('wirekey', function ($key = null) {
            $microtime = microtime();
            $key = md5("$microtime + $key");
            return 'wire:key="'. $key .'"';
        });
        
        Blade::directive('navigate', function () {

            return 'x-link.prefetch';
            return "wire:navigate";
        });
        
        Blade::directive('active', function ($expression) {
            list($pattern, $class) = explode(',', str_replace(['(',')', ' ', "'"], '', $expression));
            return "<?= request()->is('$pattern') ? '$class' : ''; ?>";
        });
    }

    public function registerPayments(){
        foreach (new \DirectoryIterator(base_path('app/Payments')) as $info){
            if (!$info->isDot()) {
                // Important info
                $method = $info->getFilename();
                $path = $info->getPathname();

                if (is_dir($servicePath = base_path("app/Payments/$method/Services"))) {
                    $services = new \DirectoryIterator($servicePath);
                    // Loop services folder
                    foreach ($services as $service) {
                        if (!$service->isDot()) {
                            $serviceName = $service->getFilename();
                            $serviceName = basename($serviceName, '.php');
                            $serviceName = "App\Payments\\$method\Services\\$serviceName";

                            // Register the service
                            if (!app()->getProviders($serviceName)) {
                                $this->app->register("$serviceName");
                            }
                        }
                    }
                }
                if (is_dir("$path/Views")) View::addNamespace("payment:$method", "{$path}/Views");


                // Register Routes
                Route::middleware(['web'])->prefix("payments/{$method}")->namespace("\App\Payments\\$method")->name("yena-payments-$method-")->group("{$path}/routes.php");
            }
        }
    }

    public function registerSections(){
        
        foreach (new \DirectoryIterator(base_path('app/Sections')) as $info){
            if (!$info->isDot()) {
                $method = $info->getFilename();
                $path = $info->getPathname();

                // Loop services folder
                foreach (new \DirectoryIterator($path) as $service) {
                    if (!$service->isDot() && !is_dir($service->getPathname())) {
                        $serviceName = $service->getFilename();
                        $serviceName = basename($serviceName, '.php');
                        $serviceName = "App\Sections\\$method\\$serviceName";

                        // Register the service
                        if (!app()->getProviders($serviceName)) {
                            $this->app->register("$serviceName");
                        }
                    }
                }

                // Register views
                \View::addNamespace('section:' . strtolower($method), "{$path}/Views");
            }
        }

    }

    public function registerBioSections(){
        foreach (new \DirectoryIterator(base_path('app/Bio/Sections')) as $info){
            if (!$info->isDot()) {
                $method = $info->getFilename();
                $path = $info->getPathname();

                // Loop services folder
                foreach (new \DirectoryIterator($path) as $service) {
                    if (!$service->isDot() && !is_dir($service->getPathname())) {
                        $serviceName = $service->getFilename();
                        $serviceName = basename($serviceName, '.php');
                        $serviceName = "App\Bio\Sections\\$method\\$serviceName";

                        // Register the service
                        if (!app()->getProviders($serviceName)) {
                            $this->app->register("$serviceName");
                        }
                    }
                }

                // Register views
                // \View::addNamespace('section:' . strtolower($method), "{$path}/Views");
            }
        }
    }

    public function registerAddons(){
        foreach (new \DirectoryIterator(base_path('app/Bio/Addons')) as $info){
            if (!$info->isDot()) {
                $method = $info->getFilename();
                $path = $info->getPathname();

                // Loop services folder
                foreach (new \DirectoryIterator($path) as $service) {
                    if (!$service->isDot() && !is_dir($service->getPathname())) {
                        $serviceName = $service->getFilename();
                        $serviceName = basename($serviceName, '.php');
                        $serviceName = "App\Bio\Addons\\$method\\$serviceName";

                        // Register the service
                        if (!app()->getProviders($serviceName)) {
                            $this->app->register("$serviceName");
                        }
                    }
                }
            }
        }
    }
}
