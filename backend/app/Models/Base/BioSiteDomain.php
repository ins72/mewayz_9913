<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BioSiteDomain
 * 
 * @property int $id
 * @property int|null $site_id
 * @property int $is_active
 * @property int $is_connected
 * @property string|null $scheme
 * @property string|null $host
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class BioSiteDomain extends Model
{
	protected $table = 'bio_site_domains';

	protected $casts = [
		'site_id' => 'int',
		'is_active' => 'int',
		'is_connected' => 'int'
	];
}
