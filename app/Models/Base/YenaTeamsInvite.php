<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class YenaTeamsInvite
 * 
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $team_id
 * @property string|null $email
 * @property string|null $accept_token
 * @property string|null $deny_token
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class YenaTeamsInvite extends Model
{
	protected $table = 'yena_teams_invite';

	protected $casts = [
		'user_id' => 'int',
		'team_id' => 'int'
	];
}
