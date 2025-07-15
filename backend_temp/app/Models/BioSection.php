<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Base\BioSection as BaseBioSection;

class BioSection extends BaseBioSection
{
	protected $fillable = [
		'uuid',
		'site_id',
		'page_id',
		'section',
		'section_settings',
		'position',
		'form',
		'image',
		'background',
		'content',
		'published',
		'settings'
	];
	protected $casts = [
		'background' => 'array',
		'content' => 'array',
		'settings' => 'array',
		'form' => 'array',
		'section_settings' => 'array',
	];

	protected $appends = [
		'get_image',
		'items'
	];
	
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
            $model->published = $model->published ? $model->published : 1;
        });
    }

    protected function getImage(): Attribute
    {
		$data = $this->getMedia();
        return new Attribute(
            get: fn () => $data,
        );
    }

    protected function items(): Attribute
    {
		$data = $this->getItems()->orderBy('id', 'ASC')->orderBy('position', 'DESC')->get()->toArray();

        return new Attribute(
            get: fn () => $data,
        );
    }

	public function getMedia(){
		$media = null;
		if(!empty($this->image) && mediaExists('media/bio/images', $this->image)) $media = gs('media/bio/images', $this->image);
        if(validate_url($this->image)) $media = $this->image;

		return $media;
	}

	public function getConfig(){
		return config("bio.sections.$this->section");
	}
	
	/**
	 * Get all of the items for the Section
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function getItems()
	{
		return $this->hasMany(BioSectionItem::class, 'section_id', 'uuid');
	}
}
