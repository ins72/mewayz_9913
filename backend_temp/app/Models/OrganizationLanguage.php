<?php

namespace App\Models;

use App\Yena\Languages;
use App\Models\Base\OrganizationLanguage as BaseOrganizationLanguage;

class OrganizationLanguage extends BaseOrganizationLanguage
{
	protected $fillable = [
		'_org',
		'default',
		'lang'
	];

	public function langName(){
		$languages = Languages::data();
		return ao($languages, $this->lang);
	}
}
