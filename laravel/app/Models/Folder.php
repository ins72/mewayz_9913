<?php

namespace App\Models;

use App\Models\Base\Folder as BaseFolder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Folder extends BaseFolder
{
	protected $fillable = [
		'owner_id',
		'name',
		'slug',
		'published',
		'settings'
	];

	// protected $appends = [
	// 	'members',
	// ];

    protected function membersJson(): Attribute {
		$data = $this->members()->orderBy('id', 'DESC')->get()->append('user_json')->toArray();
        return new Attribute(
            get: fn () => $data,
        );
    }
	

	public function isMember($user_id){
		if(FolderMember::where('user_id', $user_id)->where('folder_id', $this->id)->first()) return true;

		return false;
	}
	
	/**
	 * Get all of the members for the Folder
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function members()
	{
		return $this->hasMany(FolderMember::class, 'folder_id', 'id');
	}
	/**
	 * Get the connection that owns the Folder
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function connection()
	{
		return $this->belongsTo(FolderSite::class, 'id', 'folder_id');
	}

	public function folderSites()
	{
		return $this->hasMany(FolderSite::class, 'folder_id', 'id');
	}
}
