<?php

namespace App\Models;

use App\Models\Base\BioSitesUpload as BaseBioSitesUpload;

class BioSitesUpload extends BaseBioSitesUpload
{
	protected $fillable = [
		'site_id',
		'size',
		'trashed',
		'name',
		'path'
	];
	public function getMedia(){
		$media = gs('media/bio/images', $this->path);
		if($this->is_ai && !$this->ai_uploaded && !$this->path){
		   $media = $this->temp_ai_url;
		}
		return $media;
	}
}
