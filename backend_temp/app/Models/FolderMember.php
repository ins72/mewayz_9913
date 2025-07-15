<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Base\FolderMember as BaseFolderMember;

class FolderMember extends BaseFolderMember
{
	protected $fillable = [
		'folder_id',
		'user_id',
		'settings'
	];

    protected function userJson(): Attribute {
		$data = $this->user()->first()->append('avatar_json')->toArray();
        return new Attribute(
            get: fn () => $data,
        );
    }

	/**
	 * Get the folder that owns the Folder
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function folder()
	{
		return $this->belongsTo(Folder::class, 'folder_id', 'id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
}
