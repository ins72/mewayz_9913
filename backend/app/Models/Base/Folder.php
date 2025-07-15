<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Folder
 * 
 * @property int $id
 * @property int $owner_id
 * @property string|null $name
 * @property string|null $slug
 * @property int $published
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Folder extends Model
{
	protected $table = 'folders';

	protected $casts = [
		'owner_id' => 'int',
		'published' => 'int'
	];
}
