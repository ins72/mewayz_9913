<?php

namespace App\Models;

use App\Models\Base\BioPage as BaseBioPage;

class BioPage extends BaseBioPage
{
	protected $fillable = [
		'uuid',
		'site_id',
		'name',
		'slug',
		'published',
		'settings',
		'default'
	];

	protected $casts = [
		'settings' => 'array'
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
        $layouts = BioSection::where('page_id', $this->uuid)->get();


		foreach($layouts as $_item){
			$_item->getItems()->delete();

			$_item->delete();
		}
		$this->forceDelete();
	}

	public function site()
	{
		return $this->belongsTo(BioSite::class, 'site_id', 'id');
	}

	/**
	 * Get all of the sections for the Site
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function sections()
	{
		return $this->hasMany(BioSection::class, 'page_id', 'id');
	}

    protected static function boot(){
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
