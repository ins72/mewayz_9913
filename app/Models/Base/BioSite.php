<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BioSite
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string $address
 * @property string|null $bio
 * @property string|null $background
 * @property string|null $settings
 * @property string|null $location
 * @property string|null $current_edit_page
 * @property int|null $created_by
 * @property string|null $colors
 * @property string|null $logo
 * @property string|null $_slug
 * @property string|null $membership
 * @property string|null $qr
 * @property string|null $seo_image
 * @property string|null $qr_bg
 * @property string|null $_domain
 * @property string|null $qr_logo
 * @property string|null $pwa
 * @property string|null $contact
 * @property string|null $seo
 * @property int $is_template
 * @property string|null $social
 * @property string|null $banner
 * @property string|null $interest
 * @property string|null $connect_u
 * @property int $banned
 * @property int $status
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class BioSite extends Model
{
	use SoftDeletes;
	protected $table = 'bio_sites';

	protected $casts = [
		'user_id' => 'int',
		'created_by' => 'int',
		'is_template' => 'int',
		'banned' => 'int',
		'status' => 'int'
	];
}
