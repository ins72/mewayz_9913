<?php

namespace App\Models;

use App\Models\Base\Organization as BaseOrganization;

class Organization extends BaseOrganization
{
	protected $fillable = [
		'user_id',
		'name',
		'address',
		'logo',
		'favicon',
		'_cta',
		'_colors',
		'settings',
		'status'
	];

	protected $casts = [
		'_colors' => 'array',
		'_cta' => 'array',
		'settings' => 'array',	
	];

	public function brandColor(){
		$brand_color = ao($this->_colors, 'brand_color');

		if(!$brand_color) return '#000000';

		return $brand_color;
	}

	public function getNameInitial(){

		return mb_substr($this->name, 0, 1, 'utf-8');
	}

	public function currentLang(){
        if(!$lang = OrganizationLanguage::where('_org', $this->id)->first()) {
            $lang = $this->createDefaultLanguage($this);
        }

		if($__last_lang = OrganizationLanguage::where('_org', $this->id)->where('default', 1)->first()) $lang = $__last_lang;

        //$_last_lang_id = $this->_last_lang_id;
        //if($__last_lang = OrganizationLanguage::find($_last_lang_id)) $lang = $__last_lang;

        return $lang;
	}

    public function createDefaultLanguage($_org){
        $lang = new OrganizationLanguage;
        $lang->_org = $_org->id;
        $lang->default = 1;
        $lang->lang = 'en';
        $lang->save();

        return $lang;
    }

	public function owner(){
        return $this->belongsTo(User::class, 'user_id');
	}
}
