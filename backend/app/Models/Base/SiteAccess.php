<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteAccess
 * 
 * @property int $id
 * @property string $uuid
 * @property int|null $team_id
 * @property int|null $user_id
 * @property int|null $site_id
 * @property string $permission
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class SiteAccess extends Model
{
	protected $table = 'site_access';

	protected $casts = [
		'team_id' => 'int',
		'user_id' => 'int',
		'site_id' => 'int'
	];
}
