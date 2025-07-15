<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AudienceFoldersUser
 * 
 * @property int $id
 * @property int|null $folder_id
 * @property int|null $audience_id
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class AudienceFoldersUser extends Model
{
	protected $table = 'audience_folders_users';

	protected $casts = [
		'folder_id' => 'int',
		'audience_id' => 'int'
	];
}
