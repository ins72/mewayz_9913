<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BioPage
 * 
 * @property int $id
 * @property string $uuid
 * @property int $site_id
 * @property string|null $name
 * @property string|null $slug
 * @property int $published
 * @property string|null $settings
 * @property int $default
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class BioPage extends Model
{
	use SoftDeletes;
	protected $table = 'bio_pages';

	protected $casts = [
		'site_id' => 'int',
		'published' => 'int',
		'default' => 'int'
	];
}
