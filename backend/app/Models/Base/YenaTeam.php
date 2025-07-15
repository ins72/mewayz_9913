<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class YenaTeam
 * 
 * @property int $id
 * @property string $uuid
 * @property string|null $slug
 * @property int|null $owner_id
 * @property string|null $logo
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class YenaTeam extends Model
{
	protected $table = 'yena_teams';

	protected $casts = [
		'owner_id' => 'int'
	];
}
