<?php

namespace App\Models;

use App\Models\Base\SitesUpload as BaseSitesUpload;

class SitesUpload extends BaseSitesUpload
{
	protected $fillable = [
		'site_id',
		'name',
		'path'
	];

	public function getMedia(){
		$media = gs('media/site/images', $this->path);
		if($this->is_ai && !$this->ai_uploaded && !$this->path){
		   $media = $this->temp_ai_url;
		}
		return $media;
	}
}
