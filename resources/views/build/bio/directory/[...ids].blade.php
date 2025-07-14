<?php

use Illuminate\View\View;
use App\Models\BioSite;
use App\Models\BioSitesVisitor;

use function Laravel\Folio\render;
 
render(function (View $view, $ids) {

    // dd($ids);
    if(!$site = BioSite::where('address', $ids[0])->first()) abort(404);

    // Check if team can view this
    // if(!$site->published) abort(404);

    $seo = bio_site_seo($site);
    $pageSlug = null;
    $page = null;
    if(empty($ids[1])){
        // Check for page
        $page = $site->pages()->where('default', 1)->first();
        // if($page) {
        //     $seo = site_seo($page);
        // }
    }
    if(!empty($ids[1])){
        // Check for page
        $pageSlug = $ids[1];
        
        // if($page = $site->pages()->where('slug', $pageSlug)->first()) {
        //     $seo = site_seo($page);
        // }
    }

    $ip = getIp(); //getIp() 102.89.2.139
    $tracking = tracking_log();
    

    $canVisit = true;

    if(__o_feature('consume.site_traffic', $site->user) != -1){
        $key = "site::cacheVisitors::$site->id";
        $month_visitors = cache()->remember($key, 60 * 24, function () use($site) {
            return BioSitesVisitor::whereMonth('created_at', \Carbon\Carbon::now()->month)->where('site_id', $site->id)->count();
        });

        if($month_visitors >= __o_feature('consume.site_traffic', $site->user)){
            $canVisit = false;
        }
    }


    // Track Visits
    if ($canVisit) {
        if ($vistor = BioSitesVisitor::where('session', Session::getId())->where('site_id', $site->id)->first()) {
            $vistor->views = ($vistor->views + 1);
            $vistor->save();
        }else{
            $new = new BioSitesVisitor;
            $new->site_id = $site->id;
            $new->session = \Session::getId();
            $new->ip = $ip;
            $new->page_slug = $page ? $page->slug : null;
            $new->tracking = $tracking;
            $new->views = 1;
            $new->save();
        }
    }


    if(!$check = \App\Models\MySession::where('id', \Session::getId())->where('page_id', $site->id)->first()){
        \App\Models\MySession::where('id', \Session::getId())->update([
            'page_id' => $site->id,
            'tracking' => $tracking
        ]);
    }

    if($site->is_template){
        $canVisit = true;
    }

    $canVisit = true;

    $view->with('canVisit', $canVisit);
    $view->with('seo', $seo);
    $view->with('site', $site);
});


?>
<x-layouts.bio.out :$site :$seo>
   <x-slot:title></x-slot>
   
   @if (!$canVisit)
   <x-empty-state :title="__('Whoops! Quota Exceeded.')" :desc="__t('This website has exceeded it monthly allow quota <br> Contact site admin.')" image="14.png">
        <div class="flex flex-row gap-4 mt-4 lg:flex-row">
            
            <a href="mailto:{{ $site->email }}" class="cursor-pointer cursor-pointer yena-button-stack">
                <div class="--icon">
                    {!! __icon('emails', 'email-chat-message', 'w-6 h-6') !!}
                </div>

                {{ __('Email') }}
            </a>
            <a href="{{ route('register') }}" class="yena-button-stack cursor-pointer --primary">
                <div class="--icon">
                    {!! __icon('interface-essential', 'plus-add.3', 'w-6 h-6') !!}
                </div>

                {{ __('Create your site') }}

                <div class="inline-flex self-center ml-2 shrink-0">
                    <div class="--badge">{{ __('AI') }}</div>
                </div>
            </a>
        </div>
    </x-empty-state>
   @endif
   @if ($canVisit)
   <div>
        <livewire:site.bio :$site :key="uukey('site-page', 'buildout')" />
    </div>
   @endif
    
    <script>
        window.initNavigate = '/{{ $site->address }}';
    </script>
</x-layouts.bio.out>