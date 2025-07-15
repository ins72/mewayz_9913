<?php
    use Darryldecode\Cart\Facades\CartFacade as Cart;
    use App\Models\ProductOption;
    use App\Models\ProductReview;
    use App\Models\Product;
    use function Livewire\Volt\{state, mount, on};

    on([
        'updateCart' => fn() => $this->getCart(),
    ]);
    state([
        'product'
    ]);

    state([
        'owner' => fn() => $this->product->user()->first(),
    ]);

    state([
        'sizes' => fn() => $this->product->variant()->where('type', 'size')->get(),
        'colors' => fn() => $this->product->variant()->where('type', 'color')->get(),
        'images' => fn() => $this->product->variant()->where('type', 'image')->get(),
    ]);

    state([
        'color' => null,
        'size' => null,
        'image' => null,
    ]);

    state([
        'cart' => [],
        'cartCount' => 0,
        'ratings' => 0,
        'totalProducts' => function(){
            return Product::where('user_id', $this->owner->id)->count(); 
        },
        'totalAvgRating' => function(){
            return ProductReview::where('user_id', $this->product->user_id)->avg('rating'); 
        },
        'totalReviews' => function(){
            return ProductReview::where('user_id', $this->product->user_id)->count(); 
        },
    ]);

    state([
        'gallery' => function(){
            return $this->product->allMedia();
        },
    ]);

    mount(function(){
        $this->getCart();
        $this->_get();
    });

    $_get = function(){
        $this->ratings = ProductReview::where('product_id', $this->product->id)->where('user_id', $this->product->user_id)->avg('rating') ?: 0;  
    };

    $getCart = function(){
        $this->cart = Cart::session($this->owner->id)->getContent()->sort();
    };

    $addCart = function(){
        $options = [];
        $user_id = $this->owner->id;
        $product = $this->product;
        $quantity = 1;

        // if(!empty($min_qty = ao($product->extra, 'min_quantity')) && $request->quantity < ao($product->extra, 'min_quantity')){
        //     return back()->with('error', __('Min quantity is :min_qty', ['min_qty' => $min_qty]));
        // }

        $stock = !empty($product->stock) ? (int) $product->stock : 99999;

        // Check if is paywhat you want
        $price = $product->price;
        $variant_price = 0;


        // if (isset($request->membership_price)) {
        //     $price = ao($product->extra, "price_$request->membership_price");
        // }

        $price = (int) $price;

        foreach (['size', 'image', 'color'] as $key => $value) {
            if(!empty($this->{$value})){
    
                if ($variant = ProductOption::find($this->{$value})) {
                    $options[] = [
                        'id'    => $variant->id,
                        'name' => $variant->name,
                        'image' => $variant->type == 'image' ? $variant->variation_value : null,
                    ];
    
                    $variant_price += $variant->price;
                    //$stock = $variant->stock;
                }
            }
        }

        if($variant_price !== 0) $price = $variant_price;



        // Check for stock availabilty
        // if (ao($product->stock_settings, 'enable')) {
        //     // Check for product stock
        //     $request->validate([
        //         'quantity' => 'required|numeric|max:' . $stock
        //     ]);
        //     $quantity = $request->quantity;
        // }

        $id = md5("product_id.{$product->id}.{$price}:options." . serialize(array_filter($options)));

        $cart = Cart::session($user_id)->add([
            'id' => $id,
            'name' => $product->name,
            'price' => $price,
            'quantity' => $quantity,
            'attributes' => [
                'product_id' => $product->id,
                'options' => $options,
            ],
            'associatedModel' => $product
        ]);

        $this->dispatch('openCart');
        $this->getCart();
    };
?>
<div>
    <div class="yena-single-product" x-data="store_product">
        <div wire:ignore>
            <div class="modal-modal header-modal" :class="{
                '!hidden': !openCart
            }" @click="openCart = false;" x-cloak> 
                <livewire:products.cart :$cart :$product :$owner :key="uukey('app', 'products.single')"/>
            </div>
        </div>
        
        <div wire:ignore>
            <div class="modal-modal header-modal" :class="{
                '!hidden': !openReview
            }" @click="openReview = false;" x-cloak> 
                <livewire:products.review :$product :$owner :key="uukey('app', 'products.review')"/>
            </div>
        </div>
        
        <div wire:ignore>
            <div class="modal-modal header-modal" :class="{
                '!hidden': !openOrders
            }" @click="openOrders = false;" x-cloak> 
                <livewire:products.orders.orders :$owner :key="uukey('app', 'orders.orders')"/>
            </div>
        </div>
         
        <header class="header-header">
            <div class="w-[100%] max-w-screen-xl mx-auto px-5 md:!px-[80px] header-container">
                @if (request()->get('redirect'))
                <a class="bg-[#f1f5f9] mb-2 w-10 h-10 rounded-full flex items-center justify-center cursor-pointer" href="{{ request()->get('redirect') }}">
                    <i class="ph ph-caret-left text-lg"></i>
                </a>
                @else
                <a class="header-logo" href="/">
                     <img src="{{ $owner->getAvatar() }}" class="w-8 h-8 object-contain" alt="">
                 </a>
                @endif
               <nav class="header-nav">
                  {{-- <ul class="header-links">
                     <li><a class="label-medium header-link" href="/shop/all-products">Shop</a></li>
                     <li><a class="label-medium header-link" href="/collections">Collections</a></li>
                     <li><a class="label-medium header-link" href="/about">Explore</a></li>
                     <li><a class="label-medium header-link" href="/contact-us">Contact</a></li>
                     <li><a class="label-medium header-link" href="/theme-features">Theme features</a></li>
                  </ul> --}}
                  {{-- <div class="header-footer">
                     <a class="button-small" href="/login">Login</a>
                     <div class="socials-socials">
                        <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" class="socials-social"></a>
                        <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" class="socials-social"></a>
                        <a href="https://www.tiktok.com/" target="_blank" rel="noopener noreferrer" class="socials-social"></a>
                        <a href="https://www.youtube.com/" target="_blank" rel="noopener noreferrer" class="socials-social"></a>
                     </div>
                  </div> --}}
               </nav>
               <div class="header-btns">
                  {{-- <button class="header-search">Search</button> --}}
                  <a class="header-user !flex" @click="openOrders=true;">
                    {!! __i('Users', 'single-user', 'w-6 h-6') !!}
                  </a>
                  <button class="header-cart" @click="openCart = true;">
                    {!! __i('shopping-ecommerce', 'Basket, Pack.2', 'w-6 h-6') !!}
                    <span class="label-x-small header-cart-indicator">{{ count($cart) }}</span>
                  </button>
                  {{-- <div class="header-menu-button"><button class="header-burger"></button></div> --}}
               </div>
            </div>
         </header>
         
        <div class="w-[100%] max-w-screen-xl mx-auto px-5 md:!px-[80px] py-10 md:!py-[80px] product-view-container">
            <div class="product-view-col">
               <img :src="selectedGallery" x-init="gallery[0] ? selectedGallery = gallery[0] : ''" x-cloak alt=" " class="product-view-image">

               <div class="image-cells-container">
                    <template x-for="(item, index) in gallery" :key="index">
                        <button class="image-cells-button image-cells-selected" @click="selectedGallery=item" :class="{
                            'image-cells-selected': selectedGallery == item,
                        }" type="button">
                           <img :src="item" alt="Product view" class="image-cells-image">
                        </button>
                    </template>
               </div>
            </div>
            <div class="product-view-col">
               <div class="product-view-heading">
                  <div class="label-small">{{ __('Product') }}</div>
                  <div class="heading-2 product-view-title">{{ $product->name }}</div>
                  <div class="product-view-wrapper">
                     <div class="paragraph-x-large">{!! $owner->price($product->price) !!}</div>
                     <div class="ratings-container">
                        <div class="ratings-stars">
                            <div x-rating="ratings" data-size="18px"></div>
                        </div>
                        <div class="paragraph-small">{{ number_format($ratings, 1) }}</div>
                     </div>
                  </div>
                  <div class="paragraph-medium product-view-description textarea-content">{!! $product->description !!}</div>
               </div>
               <div>
                  <div class="flex flex-col gap-4">
                    <div>
                        @if (!$colors->isEmpty())
                            @if ($_color = $product->variant()->where('type', 'color')->where('id', $color)->first())
                            <div class="paragraph-medium choose-color-title">
                                {{__('Color:')}} <span>{{ $_color->name }}</span>
                            </div>
                            @else
                            <div class="paragraph-medium choose-color-title">
                                {{__('Select Color:')}}
                            </div>
                            @endif
                            <div class="choose-color-colors">
                                <button class="choose-color-button {{ !$color ? 'choose-color-selected' : '' }}" type="button" wire:click="$set('color', null)">
                                    <div class="choose-color-color"></div>
                                </button>
                                @foreach ($colors as $item)
                                    <button class="choose-color-button {{ $color == $item->id ? 'choose-color-selected' : '' }}" type="button" wire:click="$set('color', '{{ $item->id }}')">
                                        <div class="choose-color-color" style="background-color: {{ $item->variation_value }};"></div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                      </div>
    
                      <div>
                        @if (!$images->isEmpty())
                            @if ($_image = $product->variant()->where('type', 'image')->where('id', $image)->first())
                            <div class="paragraph-medium choose-color-title">
                                {{__('Variant:')}} <span>{{ $_image->name }}</span>
                            </div>
                            @else
                            <div class="paragraph-medium choose-color-title">
                                {{__('Select Variant:')}}
                            </div>
                            @endif
                            <div class="choose-color-colors">
                                <button class="choose-color-button {{ !$image ? 'choose-color-selected' : '' }}" type="button" wire:click="$set('image', null)">
                                    <div class="choose-color-color"></div>
                                </button>
                                @foreach ($images as $item)
                                    <button class="choose-color-button {{ $image == $item->id ? 'choose-color-selected' : '' }}" type="button" @click="$wire.set('image', '{{ $item->id }}'); selectedGallery='{{ gs('media/store/variant', $item->variation_value) }}'">
                                        <div class="choose-color-color">
                                            <img src="{{ gs('media/store/variant', $item->variation_value) }}" class="w-[100%] h-full object-cover rounded-full" alt="">
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                      </div>
    
                      <div>
                        @if (!$sizes->isEmpty())
                            @if ($_size = $product->variant()->where('type', 'size')->where('id', $size)->first())
                            <div class="paragraph-medium choose-color-title">
                                {{__('Size:')}} <span>{{ $_size->name }}</span>
                            </div>
                            @else
                            <div class="paragraph-medium choose-color-title">
                                {{__('Select Size:')}}
                            </div>
                            @endif
                            <div class="choose-color-colors">
                                <button class="choose-color-button {{ !$size ? 'choose-color-selected' : '' }}" type="button" wire:click="$set('size', null)">
                                    <div class="choose-color-color"></div>
                                </button>
                                @foreach ($sizes as $item)
                                    <button class="choose-color-button {{ $size == $item->id ? 'choose-color-selected' : '' }}" type="button" @click="$wire.set('size', '{{ $item->id }}');">
                                        <div class="choose-color-color !flex items-center justify-center font-bold">
                                            {{ $item->name }}
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                      </div>
                  </div>
               </div>
               {{-- <div>
                  <div class="paragraph-small items-left-title">Hurry, only <span>4</span> items left in stock!</div>
                  <div class="items-left-progress-bar">
                     <div class="items-left-progress"></div>
                  </div>
               </div> --}}
               <button class="button product-view-button" wire:click="addCart">
                {{ __('Add to cart') }}
               </button>
               <div class="wrapper__profile-mentor info-card-container flex-col">

                <div class="head w-[100%] mb-1">
                    <h4 class="font-bold text-[20px] lg:!text-[20px] mb-2">{{ __('About Creator') }}</h4>
                    <div class="flex flex-wrap items-center justify-between">
                        <div class="flex items-center mb-3 sm:mb-0 lg:!mb-3 xl:mb-0">
                            <img src="{{ $owner->getAvatar() }}" class="w-[50px] h-[50px] rounded-[50%] object-cover" alt="">
                            <div class="ml-3">
                                <h5 class="font-bold text-[14px] lg:!text-[14px] mb-0">{{ $owner->name }}</h5>
                                <p class="mb-0 medium text-[12px] lg:!text-[12px]">{{ $owner->title }}</p>
                            </div>
                        </div>
                        {{-- <a class="font-bold text-[12px] lg:!text-[12px] btn btn__purple text-white shadow btn__profile" href="/mentor">See Full Profile</a> --}}
                    </div>
                </div>
                <hr class="my-0">
                <div class="flex flex-wrap items-center justify-between w-[100%]">
                    <div class="items">
                        <h5 class="medium text-[12px] lg:!text-[12px] text-gray-400 mb-0">{{__('Total Product')}}</h5>
                        <p class="mb-0 font-bold text-[14px] lg:!text-[14px]">{{ __(':count Product', ['count' => number_format($totalProducts)]) }}</p>
                    </div>
                    <div class="items">
                        <h5 class="medium text-[12px] lg:!text-[12px] text-gray-400 mb-0">{{ __('Rating') }}</h5>
                        <p class="mb-0 font-bold text-[14px] lg:!text-[14px]">{{ __(':avg (:count Reviews)', ['avg' => number_format($totalAvgRating, 1), 'count' => number_format($totalReviews)]) }}</p>
                    </div>
                </div>
               </div>
               {{-- <div class="info-card-container">
                  <div class="info-card-row">
                     <div>
                        <div class="paragraph-small info-card-title">Pick up from Store</div>
                        <div class="paragraph-small">Usually ready in 24 hours</div>
                     </div>
                  </div>
                  <button class="label-x-small info-card-button">Check availability at other stores</button>
               </div> --}}
               <div class="product-view-wrapper">
                  <div class="share-share">
                     <div class="paragraph-medium share-title">{{ __('Share:') }}</div>
                     <div class="socials-socials share-socials items-center">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ route('out-products-single-page', ['slug' => $product->slug]) }}" target="_blank" rel="noopener noreferrer" class="socials-social flex">
                            <i class="ph ph-facebook-logo text-2xl"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ route('out-products-single-page', ['slug' => $product->slug]) }}" target="_blank" rel="noopener noreferrer" class="socials-social flex">
                            <i class="ph ph-x-logo text-2xl"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send?text={{ route('out-products-single-page', ['slug' => $product->slug]) }}" target="_blank" rel="noopener noreferrer" class="socials-social flex">
                            <i class="ph ph-whatsapp-logo text-2xl"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ route('out-products-single-page', ['slug' => $product->slug]) }}" target="_blank" rel="noopener noreferrer" class="socials-social flex">
                            <i class="ph ph-linkedin-logo text-2xl"></i>
                        </a>
                     </div>
                     {{-- <button class="label-medium share-button">{{ __('Share') }}</button> --}}
                  </div>
                  <a class="need-help-button" @click="openReview=true">
                     <div class="label-medium need-help-title">{{ __('Reviews (:count)', ['count' => $ratings]) }}</div>
                  </a>
               </div>
            </div>
         </div>
         
        
    </div>
    @script
    <script>
        Alpine.data('store_product', () => {
           return {
            selectedGallery: null,
            gallery: @entangle('gallery'),
            ratings: @entangle('ratings'),
            openCart: false,
            openReview: false,
            openOrders: false,

            
            init(){
               let $this = this;

               window.addEventListener('openCart', (event) => {
                $this.openCart = true;
               });

                function toggleBodyClass(value) {
                    document.body.classList.toggle('overflow-y-hidden', value);
                }

                ['openCart', 'openReview', 'openOrders'].forEach(prop => {
                    $this.$watch(prop, toggleBodyClass);
                });
            },
           }
        });
    </script>
    @endscript
</div>