<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SitesLinkerTrack
 * 
 * @property int $id
 * @property int|null $linker
 * @property int|null $site_id
 * @property string|null $session
 * @property string|null $link
 * @property string|null $slug
 * @property string|null $ip
 * @property string|null $tracking
 * @property int $views
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class SitesLinkerTrack extends Model
{
	protected $table = 'sites_linker_track';

	protected $casts = [
		'linker' => 'int',
		'site_id' => 'int',
		'views' => 'int'
	];
}
