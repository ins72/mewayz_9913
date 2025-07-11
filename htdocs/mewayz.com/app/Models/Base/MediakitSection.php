<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MediakitSection
 * 
 * @property int $id
 * @property string $uuid
 * @property int|null $site_id
 * @property string|null $page_id
 * @property string|null $section
 * @property string|null $section_settings
 * @property int $position
 * @property string|null $form
 * @property string|null $image
 * @property string|null $background
 * @property string|null $content
 * @property int $published
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class MediakitSection extends Model
{
	protected $table = 'mediakit_sections';

	protected $casts = [
		'site_id' => 'int',
		'position' => 'int',
		'published' => 'int'
	];
}
