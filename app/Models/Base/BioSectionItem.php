<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BioSectionItem
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
class BioSectionItem extends Model
{
	protected $table = 'bio_section_items';

	protected $casts = [
		'position' => 'int'
	];
}
