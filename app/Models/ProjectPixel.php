<?php

namespace App\Models;

use App\Models\Base\ProjectPixel as BaseProjectPixel;

class ProjectPixel extends BaseProjectPixel
{
	protected $fillable = [
		'user_id',
		'name',
		'domain',
		'pixel',
		'logo',
		'_cta',
		'_colors',
		'settings',
		'status'
	];

	protected $casts = [
		'_cta' => 'array',
		'_colors' => 'array',
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

	public function owner(){
        return $this->belongsTo(User::class, 'user_id');
	}
}
