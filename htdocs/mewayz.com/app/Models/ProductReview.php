<?php

namespace App\Models;

use App\Models\Base\ProductReview as BaseProductReview;

class ProductReview extends BaseProductReview
{
	protected $fillable = [
		'user',
		'reviewer_id',
		'product_id',
		'rating',
		'review'
	];
	
	public function reviewer(){
		return $this->belongsTo(User::class, 'reviewer_id', 'id');
	}
}
