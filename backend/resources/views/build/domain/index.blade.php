<?php

use Illuminate\View\View;
use App\Models\Site;
use App\Models\SiteDomain;
use App\Models\BioSite;
use App\Models\BioSiteDomain;

use function Laravel\Folio\render;
 
render(function (View $view) {
    $host = request()->getHost();
    $domain = BioSiteDomain::where('host', $host)->first() ?: SiteDomain::where('host', $host)->first();

    if (!$domain) {
        abort(404);
    }

    $siteModel = $domain instanceof BioSiteDomain ? BioSite::class : Site::class;
    $site = $siteModel::where('id', $domain->site_id)->first();

    if (!$site) {
        abort(404);
    }

    // Check if team can view this
    $siteType = 'bio';
    if($site instanceof Site){
        $siteType = 'site';
        if(!$site->published) abort(404);
    }
  
    $view->with('domain', $domain);
    $view->with('site', $site);
    $view->with('siteType', $siteType);
});


?>
@if ($siteType == 'site')
<x-layouts.site :$site>
    <x-slot:title>{{ __('Build') }}</x-slot>
    
 
    <div>
        <livewire:site.generate :$site :$domain :key="uukey('site-page', 'buildout')" />
     </div>
 
     <script>
         window.initNavigate = true;
     </script>
 </x-layouts.site>
@else
<x-layouts.bio.out :$site>
    <x-slot:title>{{ __('Build') }}</x-slot>
    
 
    <div>
        <livewire:site.bio :$site :$domain :key="uukey('site-page', 'buildout')" />
     </div>
 
     <script>
         window.initNavigate = true;
     </script>
 </x-layouts.bio.out>
@endif