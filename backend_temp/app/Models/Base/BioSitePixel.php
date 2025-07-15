<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BioSitePixel
 * 
 * @property int $id
 * @property string $uuid
 * @property int $site_id
 * @property string|null $name
 * @property int $status
 * @property string|null $pixel_id
 * @property string|null $pixel_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class BioSitePixel extends Model
{
	protected $table = 'bio_site_pixels';

	protected $casts = [
		'site_id' => 'int',
		'status' => 'int'
	];
}
