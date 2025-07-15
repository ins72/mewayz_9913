<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SitesVisitor
 * 
 * @property int $id
 * @property int|null $site_id
 * @property string|null $slug
 * @property string|null $session
 * @property string|null $ip
 * @property string|null $tracking
 * @property string|null $page_slug
 * @property int $views
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class SitesVisitor extends Model
{
	protected $table = 'sites_visitors';

	protected $casts = [
		'site_id' => 'int',
		'views' => 'int'
	];
}
