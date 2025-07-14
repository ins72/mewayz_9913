<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SitesUpload
 * 
 * @property int $id
 * @property int|null $site_id
 * @property string $size
 * @property int $trashed
 * @property string|null $name
 * @property string|null $path
 * @property int $saved_ai
 * @property int $ai_uploaded
 * @property string|null $temp_ai_url
 * @property int $is_ai
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class SitesUpload extends Model
{
	use SoftDeletes;
	protected $table = 'sites_uploads';

	protected $casts = [
		'site_id' => 'int',
		'trashed' => 'int',
		'saved_ai' => 'int',
		'ai_uploaded' => 'int',
		'is_ai' => 'int'
	];
}
