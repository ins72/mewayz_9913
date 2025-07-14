<?php

namespace App\Models;

use App\Models\Base\Page as BasePage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends BasePage
{
	use SoftDeletes;
	protected $fillable = [
		// 'site_id',
		'name',
		'published',
		'slug',
		'default',
		'settings',
		'header',
		'footer',
		'position',
		'hide_header',
		'seo',
	];

	protected $casts = [
		'header' => 'array',
		'footer' => 'array',
		'seo' => 'array',
	];
	
	public function duplicatePage(){
		$page = $this->replicate();
		$page->site_id = $this->site_id;
		$page->uuid = str()->uuid();
		$page->name = $this->name . '_copy';
		$page->default = 0;
		$page->save();


        $layouts = Section::where('page_id', $this->uuid)->get();
		foreach($layouts as $_layout){
			$layout = $_layout->replicate();
			$layout->uuid = str()->uuid();
			$layout->site_id = $this->site_id;
			$layout->page_id = $page->uuid;
			$layout->position = $_layout->position;
			$layout->save();


			foreach ($_layout->getItems()->get() as $sec) {
				$section = $sec->replicate();
				$section->uuid = str()->uuid();
				$section->section_id = $layout->uuid;
				$section->position = $sec->position;
				$section->save();
			}
		}
		
		return $page;
	}

	public function processDelete(){
        $layouts = Section::where('page_id', $this->id)->get();


		foreach($layouts as $_item){
			$_item->getItems()->delete();

			$_item->delete();
		}
		$this->forceDelete();
	}

	/**
	 * Get all of the sections for the Site
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function sections()
	{
		return $this->hasMany(Section::class, 'page_id', 'id');
	}

	public function site()
	{
		return $this->belongsTo(Site::class, 'site_id', 'id');
	}

    protected static function boot(){
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
		
        static::updating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
