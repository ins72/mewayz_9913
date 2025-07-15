<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MediakitSectionItem
 * 
 * @property int $id
 * @property string $uuid
 * @property string|null $section_id
 * @property string|null $image
 * @property string|null $content
 * @property string|null $settings
 * @property int $position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class MediakitSectionItem extends Model
{
	protected $table = 'mediakit_section_items';

	protected $casts = [
		'position' => 'int'
	];
}
