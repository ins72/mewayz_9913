<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FolderMember
 * 
 * @property int $id
 * @property int $folder_id
 * @property int $user_id
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class FolderMember extends Model
{
	protected $table = 'folder_members';

	protected $casts = [
		'folder_id' => 'int',
		'user_id' => 'int'
	];
}
