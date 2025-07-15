<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Unsplashable
 * 
 * @property int $unsplash_asset_id
 * @property int $unsplashables_id
 * @property string $unsplashables_type
 *
 * @package App\Models\Base
 */
class Unsplashable extends Model
{
	protected $table = 'unsplashables';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'unsplash_asset_id' => 'int',
		'unsplashables_id' => 'int'
	];
}
