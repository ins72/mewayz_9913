<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BioSiteStory
 * 
 * @property int $id
 * @property string $uuid
 * @property int $site_id
 * @property string|null $thumbnail
 * @property string|null $name
 * @property string|null $link
 * @property string|null $settings
 * @property int $position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class BioSiteStory extends Model
{
	protected $table = 'bio_site_story';

	protected $casts = [
		'site_id' => 'int',
		'position' => 'int'
	];
}
