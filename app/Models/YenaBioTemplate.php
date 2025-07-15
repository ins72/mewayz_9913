<?php

namespace App\Models;

use App\Models\Base\YenaBioTemplate as BaseYenaBioTemplate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YenaBioTemplate extends BaseYenaBioTemplate
{
	protected $fillable = [
		'uuid',
		'site_id',
		'created_by',
		'name',
		'price',
		'extra'
	];

	protected $casts = [
		'extra' => 'array',
	];

	public function isFree(){

		return $this->price == 0.00 || $this->price == 0 ? true : false;
	}

	public function isPurchased(){
		return YenaBioTemplateAccess::where('template_id', $this->id)->where('user_id', auth()->user()->id)->exists();
	}
	
	/**
	 * Get the site that owns the YenaTemplate
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function site(): BelongsTo
	{
		return $this->belongsTo(BioSite::class, 'site_id', 'id');
	}

	/**
	 * Get the creator that owns the YenaTemplate
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function creator(): BelongsTo
	{
		return $this->belongsTo(User::class, 'created_by', 'id');
	}


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
