<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SectionItem
 * 
 * @property int $id
 * @property string $uuid
 * @property string|null $section_id
 * @property string|null $image
 * @property string|null $content
 * @property string|null $settings
 * @property int $generated_ai_image
 * @property int $generated_ai
 * @property int $position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class SectionItem extends Model
{
	protected $table = 'section_items';

	protected $casts = [
		'generated_ai_image' => 'int',
		'generated_ai' => 'int',
		'position' => 'int'
	];
}
