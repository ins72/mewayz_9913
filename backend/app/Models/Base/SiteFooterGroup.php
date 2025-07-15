<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteFooterGroup
 * 
 * @property int $id
 * @property string $uuid
 * @property int|null $site_id
 * @property string|null $title
 * @property string|null $links
 * @property int $position
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class SiteFooterGroup extends Model
{
	protected $table = 'site_footer_groups';

	protected $casts = [
		'site_id' => 'int',
		'position' => 'int'
	];
}
