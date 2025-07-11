<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductReview
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $reviewer_id
 * @property int|null $product_id
 * @property string|null $rating
 * @property string|null $review
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ProductReview extends Model
{
	protected $table = 'product_reviews';

	protected $casts = [
		'user_id' => 'int',
		'reviewer_id' => 'int',
		'product_id' => 'int'
	];
}
