<?php

namespace App\Yena\Mediakit;

use App\Models\MediakitSiteSocial;
use App\Models\BioPage;
use App\Models\MediakitSite;
use App\Models\Section;
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

		$settings = [
			'page_width' => '1000',
			'corner' => $style,
			'enable_cover' => true,
			'color' => getRandomHexColor(),
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
			'banner' => 'x',
			'style' => 'webpage',
		];

		$seo = [
			'silence' => 'golden'
		];

		$default = [
			'silence' => 'golden'
		];

		$new = new MediakitSite;
		$new->address = $address;
		$new->_slug = $_slug;
		// $new->email = $this->owner->email;
		$new->name = $name;
		$new->user_id = $this->owner->id;
		$new->created_by = iam()->get_original_user()->id;
		$new->settings = $settings;
		$new->seo = $seo;
		$new->background = $default;
		$new->bio = __('Click here to add a brief summary about your page to get your audience interested in what you do.');
  
		$new->save();

		$this->site = $new;

		$this->createSocials();
	}

	public function createSocials(){
		$socials = ['instagram', 'facebook', 'phone'];


		foreach ($socials as $value) {
			$insert = new MediakitSiteSocial;
			$insert->site_id = $this->site->id;
			$insert->social = $value;
			$insert->save();
		}
	}

	public function build(){
		$this->create();
		
		return $this->site;
	}
}