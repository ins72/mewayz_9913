<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SitePost
 * 
 * @property int $id
 * @property string $uuid
 * @property int|null $site_id
 * @property string|null $slug
 * @property string|null $name
 * @property int $published
 * @property string|null $seo
 * @property string|null $content
 * @property string|null $description
 * @property string|null $settings
 * @property string|null $section_settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class SitePost extends Model
{
	protected $table = 'site_post';

	protected $casts = [
		'site_id' => 'int',
		'published' => 'int'
	];
}
