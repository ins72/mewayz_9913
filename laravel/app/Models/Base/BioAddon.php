<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BioAddon
 * 
 * @property int $id
 * @property string $uuid
 * @property int $site_id
 * @property string $slug
 * @property string|null $name
 * @property string|null $thumbnail
 * @property string|null $addon
 * @property string|null $content
 * @property string|null $settings
 * @property int $position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class BioAddon extends Model
{
	protected $table = 'bio_addons';

	protected $casts = [
		'site_id' => 'int',
		'position' => 'int'
	];
}
