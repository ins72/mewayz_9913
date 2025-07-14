<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FolderSite
 * 
 * @property int $id
 * @property int $folder_id
 * @property int|null $site_id
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class FolderSite extends Model
{
	protected $table = 'folder_sites';

	protected $casts = [
		'folder_id' => 'int',
		'site_id' => 'int'
	];
}
