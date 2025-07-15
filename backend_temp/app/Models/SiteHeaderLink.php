<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Base\SiteHeaderLink as BaseSiteHeaderLink;

class SiteHeaderLink extends BaseSiteHeaderLink
{
	protected $fillable = [
		'uuid',
		'site_id',
		'parent_id',
		'title',
		'link',
		'position',
		'settings'
	];

	protected $casts = [
		'settings' => 'array',	
	];

	protected $appends = [
		'children'
	];

    protected function children(): Attribute
    {

		$data = $this->getChildren();
		
        return new Attribute(
            get: fn () => $data,
        );
    }

	/**
	 * Get all of the children for the SiteHeaderLink
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function getChildren(){
		return $this->where('parent_id', $this->uuid)->orderBy('id', 'DESC')->orderBy('position', 'ASC')->get();
	}

    protected static function boot(){
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
