<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Page
 * 
 * @property int $id
 * @property string $uuid
 * @property int $site_id
 * @property string|null $name
 * @property string|null $slug
 * @property int $published
 * @property string|null $settings
 * @property int $hide_header
 * @property string|null $seo
 * @property string|null $footer
 * @property string|null $header
 * @property int $default
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Page extends Model
{
	use SoftDeletes;
	protected $table = 'pages';

	protected $casts = [
		'site_id' => 'int',
		'published' => 'int',
		'hide_header' => 'int',
		'default' => 'int'
	];
}
