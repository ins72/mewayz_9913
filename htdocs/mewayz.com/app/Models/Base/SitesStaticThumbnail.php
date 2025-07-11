<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SitesStaticThumbnail
 * 
 * @property int $id
 * @property string $uuid
 * @property int $site_id
 * @property string|null $thumbnail
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class SitesStaticThumbnail extends Model
{
	protected $table = 'sites_static_thumbnail';

	protected $casts = [
		'site_id' => 'int'
	];
}
