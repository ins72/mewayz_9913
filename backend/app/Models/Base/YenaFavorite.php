<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class YenaFavorite
 * 
 * @property int $id
 * @property string $uuid
 * @property int|null $owner_id
 * @property int|null $user_id
 * @property int|null $site_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class YenaFavorite extends Model
{
	protected $table = 'yena_favorites';

	protected $casts = [
		'owner_id' => 'int',
		'user_id' => 'int',
		'site_id' => 'int'
	];
}
