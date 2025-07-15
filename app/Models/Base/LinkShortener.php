<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LinkShortener
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string|null $slug
 * @property string|null $link
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class LinkShortener extends Model
{
	protected $table = 'link_shortener';

	protected $casts = [
		'user_id' => 'int'
	];
}
