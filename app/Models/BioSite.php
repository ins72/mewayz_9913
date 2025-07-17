<?php

namespace App\Models;

use App\Models\Base\BioSite as BaseBioSite;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BioSite extends BaseBioSite
{
	protected $fillable = [
		'user_id',
		'name',
		'title',
		'slug',
		'address',
		'bio',
		'description',
		'theme_config',
		'background',
		'settings',
		'location',
		'current_edit_page',
		'created_by',
		'colors',
		'logo',
		'_slug',
		'membership',
		'qr',
		'seo_image',
		'qr_bg',
		'_domain',
		'qr_logo',
		'pwa',
		'contact',
		'seo',
		'is_template',
		'social',
		'banner',
		'interest',
		'connect_u',
		'banned',
		'status'
	];

	protected $casts = [
		'background' => 'array',
		'settings' => 'array',
		'header' => 'array',
		'footer' => 'array',
        'seo' => 'array',
        'ai_generate_prompt' => 'array',
	];

	protected $appends = [
		'socials',
	];

    protected function socials(): Attribute
    {
		$data = $this->getSocials()->get()->toArray();
        return new Attribute(
            get: fn () => $data,
        );
    }

    public function getEditingPage(){
        $page = $this->current_edit_page;
        if(!$this->pages()->where('uuid', $page)->first()){
            $page = false;
        }


        if(!$page){
            if($p = $this->pages()->first()){
               $page = $p->uuid;
            }
        }

        return $page;
    }

    public function deleteCompletely(){
        foreach ($this->sections()->get() as $section) {
           $section->getItems()->delete();
           $section->delete();
        }
  
        $this->pages()->forceDelete();
        // $this->header_links()->delete();
        // $this->footer_groups()->delete();
        $this->getSocials()->delete();

        BioSitesLinkerTrack::where('site_id', $this->id)->delete();
        BioSitesVisitor::where('site_id', $this->id)->delete();
        BioSiteDomain::where('site_id', $this->id)->delete();
        // BioSiteForm::where('site_id', $this->id)->delete();
        BioSiteSocial::where('site_id', $this->id)->delete();
        // BioSitesStaticThumbnail::where('site_id', $this->id)->delete();

        $uploads = BioSitesUpload::where('site_id', $this->id)->get();
        foreach ($uploads as $value) {
            storageDelete('media/site/images', $value->path);
            $value->delete();
        }

        $this->forceDelete();
    }

    /**
     * Get the components for the bio site
     */
    public function components()
    {
        return $this->hasMany(BioSiteComponent::class, 'bio_site_id');
    }

    protected static function boot(){
        parent::boot();

        static::creating(function ($model) {
            // Auto-assign user_id if not set
            if (empty($model->user_id) && auth()->check()) {
                $model->user_id = auth()->id();
            }
            // Auto-assign slug if not set
            if (empty($model->_slug)) {
                $model->_slug = (string) str()->random(17);
            }
        });
    }

    public function duplicateSite($surfix = '_copy'){
		$site = $this->replicate();
        $site->name = $this->name.str()->random(3).$surfix;
        $site->address = $this->address.str()->random(3).$surfix;
        $site->_slug = $this->_slug.str()->random(3).$surfix;

        $site->save();

        $pages = $this->pages()->get();
        // $headerLinks = $this->header_links()->where('parent_id', '=', null)->get();
        // $footerGroups = $this->footer_groups()->get();
        $socials = $this->getSocials()->get();

        
        // Duplicate Pages
        foreach($pages as $_page){
            $page = $_page->replicate();
            $page->site_id = $site->id;
            $page->uuid = str()->uuid();
            $page->save();

            $layouts = BioSection::where('page_id', $_page->uuid)->get();
            foreach($layouts as $_layout){
                $layout = $_layout->replicate();
                $layout->uuid = str()->uuid();
                $layout->site_id = $site->id;
                $layout->page_id = $page->uuid;
                $layout->position = $_layout->position;
                $layout->save();


                foreach ($_layout->getItems()->get() as $sec) {
                    $section = $sec->replicate();
                    $section->uuid = str()->uuid();
                    $section->section_id = $layout->uuid;
                    $section->position = $sec->position;
                    $section->save();
                }
            }
        }
        
        // Duplicate Header Links
        // foreach ($headerLinks as $_header) {
        //     $header = $_header->replicate();
        //     $header->site_id = $site->id;
        //     $header->uuid = str()->uuid();
        //     $header->save();

        //     foreach ($_header->getChildren() as $_value) {
        //         $item = $_value->replicate();
        //         $item->uuid = str()->uuid();
        //         $item->parent_id = $header->uuid;
        //         $item->site_id = $site->id;
        //         $item->save();
        //     }
        // }

        // // Duplicate footer links
        // foreach ($footerGroups as $_item) {
        //     $item = $_item->replicate();
        //     $item->site_id = $site->id;
        //     $item->uuid = str()->uuid();
        //     $item->save();
        // }

        // Duplicate Social
        foreach ($socials as $_item) {
            $item = $_item->replicate();
            $item->site_id = $site->id;
            $item->uuid = str()->uuid();
            $item->save();
        }

        return $site;
    }

    public function createdBy(){
        return User::find($this->created_by);
    }

	public function user(){
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

    public function getFavorite($id){
        return YenaFavorite::where('owner_id', iam()->id)->where('user_id', iam()->get_original_user()->id)->where('site_id', $id)->first();
    }

    public function getAddress(){
        $sitePrefix = config('app.bio_prefix');
        return url("/") . "/$sitePrefix{$this->address}";
    }

    public function workspaceAccess(){

        return $this->workspace_permission;
    }

    public function canEdit(){
        return true;
        $access = $this->canAccess();
        // Check if is superadmin
        if(iam()->isAdmin()) return true;

        if($access == 'edit' || $access == 'full_access') return true;
        return false;
    }

    public function fullAccess(){
        $access = $this->canAccess();
        if(Teams::permission('ce')) $access = 'full_access';
        // if(iam()->isAdmin()) $access = false;

        if($access == 'full_access') return true;

        return false;
    }

    public function canAccess(){
        $check = false;
        $team = \App\Yena\Teams::init();
        $siteAccess = SiteAccess::where('team_id', $team->id)->where('site_id', $this->id)->where('user_id', $this->user()->first()->get_original_user()->id)->first();
        // $folderSite = FolderSite::where('site_id', $this->id)->get();

        $folderCheck = false;
        // foreach ($folderSite as $value) {
        //     if(FolderMember::where('user_id', $this->user()->first()->get_original_user()->id)->where('folder_id', $value->folder_id)->first()){
        //         $folderCheck = true;
        //     }
        // }


        $check = $this->workspace_permission;
        // Check if is in folder.
        if($folderCheck){
            // $check = $this->workspace_permission;
        }

        // Check for access 
        if($siteAccess){
            $check = $siteAccess->permission;
        }

        // Check if i am the owner of the site;
        if($this->user_id ==  $this->user()->first()->get_original_user()->id){
            $check = 'full_access';
        }

        if($check == 'no_access') $check = false;

        return $check;
    }

	/**
	 * Get all of the getSocials for the Site
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function getSocials()
	{
		return $this->hasMany(BioSiteSocial::class, 'site_id', 'id');
	}

	public function getLogo(){
        $avatar = $this->logo;
        $default = "https://api.dicebear.com/6.x/bottts-neutral/svg?seed=$this->name";
        $check = mediaExists('media/bio/images', $avatar);
        $path = getStorage('media/bio/images', $avatar);

        $avatar = (!empty($avatar) && $check) ? $path : $default;

        if(validate_url($this->logo)) return $this->logo;

        return $avatar;
	}

	public function toRoute($route, $param = []){
		$param['slug'] = $this->_slug;
		return route($route, $param);
	}

	public function getUploadedSizesMB(){
        return formatBytes(get_used_storage($this), true);
		$sizes = 0;

		$model = $this->uploads()->select(['size'])->get();
		foreach($model as $item){
			$sizes += $item->size;
		}

		

		return number_format($sizes / 1048576, 2);
	}

	public function getEditSections(){


		return $this->sections()->where('page_id', $this->current_edit_page)->orderBy('id', 'ASC')->get();
	}

	public function uploads()
	{
		return $this->hasMany(BioSitesUpload::class, 'site_id', 'id');
	}

	// /**
	//  * Get all of the header_links for the Site
	//  *
	//  * @return \Illuminate\Database\Eloquent\Relations\HasMany
	//  */
	// public function footer_groups(){
	// 	return $this->hasMany(SiteFooterGroup::class, 'site_id', 'id');
	// }

	// /**
	//  * Get all of the header_links for the Site
	//  *
	//  * @return \Illuminate\Database\Eloquent\Relations\HasMany
	//  */
	// public function header_links(){
	// 	return $this->hasMany(SiteHeaderLink::class, 'site_id', 'id');
	// }

	public function currentPage(){
		$page = $this->pages()->where('uuid', $this->current_edit_page)->first();

		if(!$page) $page = $this->pages()->first();

		return $page;
	}

	/**
	 * Get all of the pages for the Site
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function pages()
	{
		return $this->hasMany(BioPage::class, 'site_id', 'id');
	}

	/**
	 * Get all of the sections for the Site
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function sections()
	{
		return $this->hasMany(BioSection::class, 'site_id', 'id');
	}

	public function folderSites(){
		return $this->hasMany(FolderSite::class, 'site_id', 'id');
	}

    public function staticSitePreview(){
        return SitesStaticThumbnail::where('site_id', $this->id)->first();
    }
	
	
	/**
	 * Get all of the getStory for the Site
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function getStory()
	{
		return $this->hasMany(BioSiteStory::class, 'site_id', 'id');
	}

	/**
	 * Get all of the getAddons for the Site
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function getAddons()
	{
		return $this->hasMany(BioAddon::class, 'site_id', 'id');
	}

	public function getInsight(){
        // Get Model
        $visitors = BioSitesVisitor::where('site_id', $this->id)->get();

        // Empty array of visits
        $returned = [];


        // Get All Countries
        $countries = [];
        foreach ($visitors as $item) {
            $iso = (string) ao($item->tracking, 'country.iso');
            $name = ao($item->tracking, 'country.name');

            if (!empty($iso) && !array_key_exists($iso, $countries)) {
                $countries[$iso] = [
                    'visits' => 0,
                    'unique' => 0,
                    'name' => $name,
                ];
            }

            if (array_key_exists($iso, $countries)) {
                $countries[$iso]['unique']++;
                $countries[$iso]['visits'] += $item->views;
            }
        }

        // Get ALL State

        $state = [];
        foreach ($visitors as $item) {
            $city = (string) ao($item->tracking, 'country.city');
            $iso = ao($item->tracking, 'country.iso');
            $iso = strtoupper($iso);

            $check = "$city, $iso";

            if (!empty($city) && !array_key_exists($check, $state)) {
                $state[$check] = [
                    'visits' => 0,
                    'unique' => 0,
                    'name' => $city,
                    'iso' => $iso,
                ];
            }

            if (array_key_exists($check, $state)) {
                $state[$check]['unique']++;
                $state[$check]['visits'] += $item->views;
            }
        }

        // Get ALL Devices
        $devices = [];
        foreach ($visitors as $item) {
            $os = (string) ao($item->tracking, 'agent.os');
            if (!empty($os) && !array_key_exists($os, $devices)) {
                $devices[$os] = [
                    'visits' => 0,
                    'unique' => 0,
                    'name' => $os,
                ];
            }

            if (array_key_exists($os, $devices)) {
                $devices[$os]['unique']++;
                $devices[$os]['visits'] += $item->views;
            }
        }

        // Get AlL Browser
        $browsers = [];
        foreach ($visitors as $item) {
            $browser = (string) ao($item->tracking, 'agent.browser');
            if (!empty($browser) && !array_key_exists($browser, $browsers)) {
                $browsers[$browser] = [
                    'visits' => 0,
                    'unique' => 0,
                    'name' => $browser,
                ];
            }

            if (array_key_exists($browser, $browsers)) {
                $browsers[$browser]['unique']++;
                $browsers[$browser]['visits'] += $item->views;
            }
        }

        // Views
        $getviews = [];
        $views = 0;
        $unique = 0;
        foreach ($visitors as $item) {
            $unique++;
            $views += $item->views;
        }

        $getviews = [
            'visits' => $views,
            'unique' => $unique,
        ];


        $start_of_year = \Carbon\Carbon::now()->startOfYear()->toDateString();
        $visitors_this_year = BioSitesVisitor::where('site_id', $this->id)->where('created_at', '>=', $start_of_year)->get();


        // Get This Year Views
        $thisyear = [];
        foreach ($visitors_this_year as $item) {
            $date = (string) \Carbon\Carbon::parse($item->created_at)->format('M');
            if (!empty($date) && !array_key_exists($date, $thisyear)) {
                $thisyear[$date] = [
                    'visits' => 0,
                    'unique' => 0,
                ];
            }

            if (array_key_exists($date, $thisyear)) {
                $thisyear[$date]['unique']++;
                $thisyear[$date]['visits'] += $item->views;
            }
        }
        $thisyear = get_chart_data($thisyear);

        $returned = ['countries' => $countries, 'cities' => $state, 'devices' => $devices, 'browsers' => $browsers, 'getviews' => $getviews, 'thisyear' => $thisyear];


        return $returned;
	}
}
