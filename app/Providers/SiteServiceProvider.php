<?php

namespace App\Providers;

use App\Models\BioSiteDomain;
use App\Models\SiteDomain;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Laravel\Folio\Folio;

class SiteServiceProvider extends ServiceProvider{
    
    protected $namespace = 'Modules\Bio\Http\Controllers';//'Modules\Bio\Http\Controllers';
    protected $routeName = 'page-';
    public $prefix = '@{_page}';
    public $domainOrPrefix = 'prefix';
    
    public $customDomain = false;


    public function registerRoutes(){
        Folio::path(resource_path('views/pages'))->uri('/')->middleware([
            '*' => [
                'web'
            ],
            'console/*' => [
                'auth',
                'verified',
                'handleProject'
            ],

            'console/admin/*' => [
                // 'password.confirm'
            ],

            'console/builder/*' => [
                'handleBuilder'
            ],

            'console/bio/*' => [
                'handleBio'
            ],
            'console/mediakit/*' => [
                'handleMediakit'
            ],
        ]);
    }

    public function boot(){

        if (!config('app.INSTALLED')) {
            $this->registerRoutes();
            return;
        }
    
        $prefix = config('app.site_prefix');
        $bio_prefix = config('app.bio_prefix');
        $domain = request()->getHost();
    
        $siteDomainModel = null;
        $bioDomainModel = null;
        try {
            $siteDomainModel = SiteDomain::where('host', $domain)->first();
            $bioDomainModel = BioSiteDomain::where('host', $domain)->first();
        } catch (\Throwable $th) {
            //throw $th;
        }
        
        $customDomain = $siteDomainModel || $bioDomainModel; // Check if either model exists
    
        if (!$customDomain) {
            $this->registerRoutes();
        }
    
        // Process for bio
        $path = resource_path('views/build/bio/directory');
        if (is_dir($path)) {
            $folio = Folio::middleware([]);
            
            if (!$customDomain) {
                $folio->uri("/$bio_prefix");
            }
            
            $folio->path($path);
        }
    
        // Process for main directory
        $path = resource_path('views/build/directory');
        if (is_dir($path)) {
            $folio = Folio::middleware([]);
            $folio->path($path);
        }

        if($customDomain){
            $folio = Folio::middleware([]);
            $folio->domain($domain);
            $folio->uri("/");
            $path = resource_path('views/build/domain');
            if (is_dir($path)) {
                $folio->path($path);
            }
        }
    }

    public function register(){
        // Check if system is subdomain or subdirectory
        // if (config('app.BIO_WILDCARD')) {
        //     $this->prefix = '{_page}.' . parse(config('app.BIO_WILDCARD_DOMAIN'), 'host');
        //     $this->domainOrPrefix = 'domain';
        // }

        // Check for custom domain
    }
}
