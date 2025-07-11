<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BioSitesLinker
 * 
 * @property int $id
 * @property int|null $site_id
 * @property string|null $url
 * @property string|null $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class BioSitesLinker extends Model
{
	protected $table = 'bio_sites_linker';

	protected $casts = [
		'site_id' => 'int'
	];
}
