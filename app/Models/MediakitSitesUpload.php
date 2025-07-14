<?php

namespace App\Models;

use App\Models\Base\MediakitSitesUpload as BaseMediakitSitesUpload;

class MediakitSitesUpload extends BaseMediakitSitesUpload
{
	protected $fillable = [
		'site_id',
		'size',
		'trashed',
		'name',
		'path',
		'ai_uploaded',
		'temp_ai_url',
		'saved_ai',
		'is_ai'
	];
	
	public function getMedia(){
		$media = gs('media/mediakit/images', $this->path);
		if($this->is_ai && !$this->ai_uploaded && !$this->path){
		   $media = $this->temp_ai_url;
		}
		return $media;
	}
}
