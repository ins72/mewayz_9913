<?php

namespace App\Yena\Site;

use App\Models\Page;
use App\Models\Site;
use App\Models\Section;
use App\Models\SiteSocial;
use App\Models\SectionItem;

class Generate {
	public $owner;
	public $site;
	public $page;

	public $name = false;

	public function setOwner($owner){
		$this->owner = $owner;
		return $this;
	}

	public function setName($name){
		$this->name = $name;
		return $this;
	}

	public function create(){
		$randomSlug = str()->random(15);
		$addressSlug = str()->random(7);

		$name = 'Blank';
		$useSlug = true;
		if($this->name){
			$useSlug = false;
			$name = $this->name;
		}

		$slug_name = slugify($name, '-');
		$_slug = "$slug_name-$randomSlug";
		$address = "$slug_name-$addressSlug";

		if($useSlug) {
			$name = "$name-$addressSlug";
		}

		$styles = ['straight', 'round', 'rounded'];
		$style = $styles[array_rand($styles)];


		$header = [
			"style" => 1,
			"logo_type" => "text",
			"logo_text" => $name,
			"link" => "/",
			"button_one_text" => "",
			"button_two_text" => "Button",
			"links" => [],
			"width" => "fit",
			"sticky" => false,
			"_float" => false,
			"glass" => false,
			"shadow" => true,
			"mobile_burger" => "2",
			"logo__c" => "accent",
			"logo_width_mobile" => "20",
			"logo_width" => "20"
		];

		$footer = [
			"style" => 1,
			"copyright_one" => $name,
			"copyright_two" => "",
			"button_one_text" => "",
			"text" => "",
			"enable_logo" => true,
			"button_two_text" => "",
		];

		$settings = [
			'page_width' => '1000',
			'corner' => $style,
			'color' => str_replace('#', '', getRandomHexColor()),
			"currentTheme" => "simple",
			"fontHeadName" => "Yrsa",
			"fontHeadSettings" => [
				"category" => "serif",
				"variants" => "300,400,500,600,700",
				"subsets" => "latin,latin-ext",
			],
			"fontName" => "Quicksand",
			"fontSettings" => [
				"category" => "sans-serif",
				"variants" => "300,400,500,600,700",
				"subsets" => "latin,latin-ext,vietnamese",
			],
		];

		$seo = [
			'silence' => 'golden'
		];


		$new = new Site;
		$new->address = $address;
		$new->_slug = $_slug;
		$new->email = $this->owner->email;
		$new->name = $name;
		$new->user_id = $this->owner->id;
		$new->created_by = iam()->get_original_user()->id;
		$new->settings = $settings;
		$new->seo = $seo;
		$new->header = $header;
		$new->footer = $footer;
		$new->save();

		$this->site = $new;

		$this->createSocials();

		$this->createPage();

		$this->createSections();
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
	
	public function createSections(){
		$getSections = $this->getRandomLanding();

		foreach($getSections as $s){

			$section = $s;
			if(ao($section, 'items')) unset($section['items']);
			
			$_section = new Section;
			$_section->fill($section);
			$_section->site_id = $this->site->id;
			$_section->page_id = $this->page->uuid;
			$_section->published = 1;
			$_section->save();

			if(is_array($items = ao($s, 'items'))){
				foreach($items as $item){
					$_item = new SectionItem;
					$_item->fill($item);
					$_item->section_id = $_section->uuid;
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
		$this->create();
		
		return $this->site;
	}
}