<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class YenaTeamsUserTable
 * 
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $team_id
 * @property int $can_update
 * @property int $can_delete
 * @property int $can_create
 * @property string $role
 * @property string|null $settings
 * @property int $is_accepted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class YenaTeamsUserTable extends Model
{
	protected $table = 'yena_teams_user_table';

	protected $casts = [
		'user_id' => 'int',
		'team_id' => 'int',
		'can_update' => 'int',
		'can_delete' => 'int',
		'can_create' => 'int',
		'is_accepted' => 'int'
	];
}
