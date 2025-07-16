<?php

namespace App\Models;

use App\Models\Base\Product as BaseProduct;

class Product extends BaseProduct
{
	protected $fillable = [
		'user_id',
		'name',
		'slug',
		'status',
		'price_type',
		'price',
		'price_pwyw',
		'comparePrice',
		'enableOptions',
		'isDeal',
		'dealPrice',
		'dealEnds',
		'enableBid',
		'stock',
		'stock_settings',
		'productType',
		'banner',
		'media',
		'description',
		'ribbon',
		'seo',
		'api',
		'files',
		'extra',
		'position'
	];

	
	protected $casts = [
		'user' => 'int',
		'status' => 'int',
		'price_type' => 'int',
		'price' => 'float',
		'enableOptions' => 'int',
		'isDeal' => 'int',
		'enableBid' => 'int',
		'stock' => 'int',
		'productType' => 'int',
		'position' => 'int',

		
		'media' => 'array',
		'extra' => 'array',
		'banner' => 'array',
		'stock_settings' => 'array',
		'seo' => 'array',
		'files' => 'array'
	];

	protected $dates = [
		'dealEnds'
	];

	public function getFeaturedImage(){
		
		$gallery = $this->banner;
		$gallery = !empty($gallery) && is_array($gallery) ? gs('media/store/image', array_values($gallery)[0] ?? '') : null;

		$image = null;

		if($this->featured_img){
			$image = gs('media/store/image', $this->featured_img);
		}

		if(empty($this->featured_img)) $image = $gallery;

		return $image;
	}
	public function reviews() {
		return $this->hasMany(ProductReview::class, 'product_id', 'id');
	}

	public function user(){
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function getPrice(){
		$price = $this->user()->first()->price($this->price);
		// $price = Bio::price($this->price, $this->user);

		// if(ao($this->extra, 'amazon_product')){
		// 	$price = ao($this->extra, 'price');
		// }

		// if($this->linked_ref){
		// 	if($linked = Product::where('id', $this->linked_ref)->first()){
		// 		$price = Bio::price($linked->price, $linked->user);
		// 	}
		// }

		return $price;
	}

	
    public function hasPurchased($payee){
		$product_id = $this->id;
        $order = ProductOrder::where('user_id', $this->user_id)->where('payee_user_id', $payee);

        $has_order = function() use($order, $product_id){
            foreach ($order->get() as $item) {
               if (in_array($product_id, $item->products)) {
                   return true;
               }
            }


            return false;
        };

        // $enrollment = ProductOrder::where('user', $bio_id)->where('payee_user_id', $payee->id)->orderBy('id', 'DESC')->first();
        // $now = \Carbon\Carbon::now();
        // if ($has_order() && $enrollment) {
        //     if (is_array(ao($enrollment->extra, 'cart'))) {

        //         // code...


        //         foreach (ao($enrollment->extra, 'cart') as $key => $value) {
        //             $product = Product::find(ao($value, 'attributes.product_id'));

        //             if ($product && $product->price_type == 2  && ao($value, 'attributes.product_id') == $product_id) {

        //                 $expiry = \Carbon\Carbon::parse(ao($value, 'attributes.membership.expire'));
        //                 $expired = \Carbon\Carbon::parse($now)->isAfter($expiry);

        //                 if (ao($value, 'attributes.membership.status') && !$expired) {
        //                     return true;
        //                 }

        //                 return false;
        //             }
        //         }
        //     }
        // }

        if ($has_order()) {
            return true;
        }


        return false;
    }
	
	public function variant(){
        return $this->hasMany(ProductOption::class, 'product_id', 'id');
    }

	public function allMedia(){
		$media = $this->media ?: [];

		array_push($media, $this->featured_img);

		$process = [];

		foreach($media as $item){
			if(!mediaExists('media/store/image', $item)) return;

			$process[] = gs('media/store/image', $item);
		}

		return $process;
	}

    protected static function boot(){
        parent::boot();

        static::creating(function ($model) {
            // Auto-assign user_id if not set
            if (empty($model->user_id) && auth()->check()) {
                $model->user_id = auth()->id();
            }
            $model->slug = $model->slug ? $model->slug : (string) str()->random(17);
        });
		
        static::updated(function ($model) {
            $model->slug = $model->slug ? $model->slug : (string) str()->random(17);
        });
    }

    // protected static function boot(){
    //     parent::boot();

    //     static::creating(function ($model) {
    //         $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
    //     });
    // }
}
