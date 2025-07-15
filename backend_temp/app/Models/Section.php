<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Base\Section as BaseSection;

class Section extends BaseSection
{
	protected $fillable = [
		// 'site_id',
		// 'page_id',
		'section',
		'image',
		'background',
		'content',
		'published',
		'settings',
		'position',
		'section_settings',
		'form'
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
		if(!empty($this->image) && mediaExists('media/site/images', $this->image)) $media = gs('media/site/images', $this->image);
        if(validate_url($this->image)) $media = $this->image;

		return $media;
	}

	public function getConfig(){
		return config("yena.sections.$this->section");
	}
	
	public function site()
	{
		return $this->belongsTo(Site::class, 'site_id', 'id');
	}
	
	/**
	 * Get all of the items for the Section
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function getItems()
	{
		return $this->hasMany(SectionItem::class, 'section_id', 'uuid');
	}
}
