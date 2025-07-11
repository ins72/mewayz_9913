<?php

namespace App\Models;

use App\Models\Base\FolderSite as BaseFolderSite;

class FolderSite extends BaseFolderSite
{
	protected $fillable = [
		'folder_id',
		'site_id',
		'settings'
	];

	/**
	 * Get all of the sites for the FolderSite
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function sites()
	{
		return $this->hasMany(Site::class, 'id', 'site_id');
	}

	/**
	 * Get the site that owns the FolderSite
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function site()
	{
		return $this->belongsTo(Site::class, 'site_id', 'id');
	}

	/**
	 * Get the site that owns the FolderSite
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function folder()
	{
		return $this->belongsTo(Folder::class, 'folder_id', 'id');
	}
}
