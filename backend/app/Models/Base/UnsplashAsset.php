<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UnsplashAsset
 * 
 * @property int $id
 * @property string $unsplash_id
 * @property string $name
 * @property string $author
 * @property string $author_link
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class UnsplashAsset extends Model
{
	protected $table = 'unsplash_assets';
}
