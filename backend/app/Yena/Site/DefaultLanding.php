<?php

namespace App\Yena\Site;

use App\Models\Page;
use App\Models\Site;
use App\Models\Section;
use App\Models\SiteSocial;
use App\Models\SectionItem;
use App\Models\SiteFooterGroup;
use App\Models\SiteHeaderLink;
use App\Models\User;

class DefaultLanding {
	public $site;
	public $page;
	public $owner;

	public function create(){
		$randomSlug = str()->random(15);
		$addressSlug = str()->random(7);

		$name = 'Mewayz';

		$slug_name = slugify($name, '-');
		$_slug = "$slug_name-$randomSlug";
		$address = "$slug_name-$addressSlug";
		$style = 'rounded';

		$header = [
			"style" => 3,
			"logo_type" => "image",
			"logo" => logo(),
			"logo_text" => $name,
			"link" => "/",
			"button_one_text" => "",
			"button_two_text" => "Sign Up",
			"button_two_link" => url('/register'),
			"links" => [],
			"width" => "fit",
			"sticky" => true,
			"_float" => false,
			"glass" => true,
			"shadow" => false,
			"mobile_burger" => "2",
			"logo__c" => "accent",
			"logo_width_mobile" => "20",
			"logo_width" => "19",
		];

		$footer = [
			"style" => 3,
			"copyright_one" => $name,
			"copyright_two" => "[Privacy](#) & [Terms](#)",
			"button_one_text" => "Get Started",
			"text" => "",
			"enable_logo" => true,
			"button_two_text" => "",
		];

		$settings = [
			"siteTheme" => "light",
			'page_width' => '1200',
			'corner' => $style,
			'color' => '3E3232',
			"currentTheme" => "simple",
			"fontHeadName" => "Limelight",
			"fontHeadSettings" => [
				"category" => "serif",
				"variants" => "400",
				"subsets" => "latin,latin-ext",
			],
			"fontName" => "Lato",
			"fontSettings" => [
				"category" => "sans-serif",
				"variants" => "100,100i,300,300i,400,400i,700,700i,900,900i",
				"subsets" => "latin,latin-ext",
			],
		];

		$seo = [
			'silence' => 'golden'
		];

        Site::where('is_admin_selected', 1)->update([
            'is_admin_selected' => 0,
        ]);

		$new = new Site;
		$new->address = $address;
		$new->_slug = $_slug;
		$new->email = $this->owner->email;
		$new->name = $name;
		$new->user_id = $this->owner->id;
		$new->created_by = $this->owner->id;
		$new->settings = $settings;
		$new->seo = $seo;
		$new->header = $header;
		$new->footer = $footer;
		$new->published = 1;
        $new->is_admin = 1;
        $new->is_admin_selected = 1;
		$new->save();

		$this->site = $new;

		$this->createSocials();
		$this->createPage();

		$this->createSections();

		$this->createHeaderLinks();
		$this->createFooter();
	}

	public function createPage(){
		$page = new Page;
		$page->site_id = $this->site->id;
		$page->name = 'Home';
		$page->slug = 'home';
		$page->default = 1;
		$page->published = 1;
		$page->save();
		
		$this->page = $page;
	}

	public function createHeaderLinks(){
		$menu = ['Pricing', 'Login'];
		$pricing_section = '#';
		if($pricing = $this->site->sections()->where('section', 'pricing')->first()){
			$pricing_section = '/#section-' . $pricing->id;
		}

		foreach ($menu as $index => $item){
			$link = $pricing_section;
			if($item == 'Login'){
				$link = url('/login');
			}

			$insert = new SiteHeaderLink;
			$insert->site_id = $this->site->id;
			$insert->uuid = str()->uuid();
			$insert->position = $index;
			$insert->title = $item;
			$insert->settings = [
				'silence' => 'golden',
			];
			$insert->link = $link;
			$insert->save();
		}
	}

	public function createFooter(){
		$menu = ['Contact', 'Pricing', 'About'];

		foreach ($menu as $index => $item){
			$parent_uuid = str()->uuid();
			$links = [
				[
					'uuid' => str()->uuid(),
					'parent_id' => $parent_uuid,
					'title' => $item,
					'link' => '#',
					'position' => 1,
					'links' => [],
				]
			];
			$insert = new SiteFooterGroup;
			$insert->uuid = $parent_uuid;
			$insert->site_id = $this->site->id;
			$insert->title = '';
			$insert->links = $links;
			$insert->position = $index;
			$insert->settings = [
				'silence' => 'golden',
			];
			$insert->save();
		}
	}

	public function createSocials(){
		$socials = ['instagram', 'facebook', 'phone'];

		foreach ($socials as $value) {
			$insert = new SiteSocial;
			$insert->site_id = $this->site->id;
			$insert->social = $value;
			$insert->save();
		}
	}
	
	public function createSections(){
		$section = config('defaults.landing.sections');

		foreach($section as $index => $s){

			$section = $s;
			if(ao($section, 'items')) unset($section['items']);
			
			$_section = new Section;
			$_section->fill($section);
			$_section->site_id = $this->site->id;
			$_section->page_id = $this->page->uuid;
			$_section->published = 1;
			$_section->position = $index;
			if(ao($s, 'image')){
				$_section->image = gs(ao($s, 'image'));
			}


			$_section->save();

			if(is_array($items = ao($s, 'items'))){
				foreach($items as $i => $item){
					$_item = new SectionItem;
					$_item->fill($item);
					$_item->section_id = $_section->uuid;
					$_item->position = $i;
					$_item->save();
				}
			}
		}
	}


	public function getRandomLanding(){
		$pages = config('sections.landing');
		$k = array_rand($pages);
		$pages = $pages[$k];

		return $pages['sections'];
	}

	public function build(){
		$this->owner = User::where('role', 1)->first();

		$this->create();
		
		return $this->site;
	}
}