<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MediakitSiteSocial
 * 
 * @property int $id
 * @property string $uuid
 * @property int|null $site_id
 * @property string|null $social
 * @property string|null $link
 * @property int $position
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class MediakitSiteSocial extends Model
{
	protected $table = 'mediakit_site_socials';

	protected $casts = [
		'site_id' => 'int',
		'position' => 'int'
	];
}
