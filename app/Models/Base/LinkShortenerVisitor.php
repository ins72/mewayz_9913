<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LinkShortenerVisitor
 * 
 * @property int $id
 * @property int|null $link_id
 * @property string|null $slug
 * @property string|null $session
 * @property string|null $ip
 * @property string|null $tracking
 * @property string|null $link
 * @property int $views
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class LinkShortenerVisitor extends Model
{
	protected $table = 'link_shortener_visitors';

	protected $casts = [
		'link_id' => 'int',
		'views' => 'int'
	];
}
