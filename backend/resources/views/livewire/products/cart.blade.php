<?php
    use App\Models\ProductShipping;
    use Darryldecode\Cart\Facades\CartFacade as Cart;
    use function Livewire\Volt\{state, mount};

    state([
        'cart'
    ])->reactive();

    state([
        'owner',
        'product'
    ]);

    state([
        'orderNote' => '',
        'countries' => fn() => \Country::list()
    ]);

    state([
        'email' => '',
        'shipping' => [
            'first_name' => '',
            'last_name' => '',
            'phone_number' => '',
            'country' => '',
            'location' => '',
        ],
        'shipping_location' => '',
    ]);

    state([
        'shippings' => [],
    ]);

    mount(function(){
        $this->_get();
    });

    $_get = function(){
      $this->shippings = ProductShipping::where('user_id', $this->owner->id)->get();
    };

    $removeCart = function($_id){
        Cart::session($this->owner->id)->remove($_id);

        $this->dispatch('updateCart');
    };

    $upQuantity = function($_id){
        $_cart = Cart::session($this->owner->id)->get($_id);

        // $quantity = $_cart->quantity++;
        Cart::session($this->owner->id)->update($_id, [
            'quantity' => 1,
        ]);

        $this->dispatch('updateCart');
    };

    $downQuantity = function($_id){
        $_cart = Cart::session($this->owner->id)->get($_id);
        if($_cart->quantity == 1) return;
        
        Cart::session($this->owner->id)->update($_id, [
            'quantity' => -1,
        ]);

        $this->dispatch('updateCart');
    };


    // Checkout
    $checkout = function(){
        if (Cart::session($this->owner->id)->isEmpty()) {
            session()->flash('error._error', __('Add items to cart to proceed'));
            return;
        }

        $this->validate([
            'email' => 'required'
        ]);

        if (ao($this->owner->store, 'shipping_enable') && ao($this->owner->store, 'shipping_type')) {
            $this->validate([
                'shipping_location' => 'required',
                'shipping.*' => 'required'
            ]);
        }
        $currency = settings('payment.currency');
        
        $products = [];
        $cart = [];

        $session_cart = Cart::session($this->owner->id)->getContent()->toArray();

        foreach ($session_cart as $key => $item) {
            $cart[$key] = (array) $item;
            $products[] = ao($item, 'attributes.product_id');
            unset($cart[$key]['associatedModel']);
        }

        $shipping = [];

        // Condition shipping
        if ($shipping_loop = $this->shipping) {
            foreach ($shipping_loop as $key => $value) {
                $shipping[$key] = $value;
            }
        }

        // Cart
        $price = $this->total_price();
        // $method = $request->payment_method;

        // $callback = route('sandy-blocks-shop-page-cart-callback', ['sxref' => $sxref, '_user' => $this->_user->id]);

        $item = [
            'name' => __(':num_of_product Products', ['num_of_product' => count($cart)]),
            'description' => __('Purchased :num_of_product product(s) from :page', ['page' => $this->owner->name, 'num_of_product' => count($cart)])
        ];

        $meta = [
            'user_id' => $this->owner->id,
            'payee_id' => auth()->check() ? auth()->user()->id : null,
            'shipping_location' => $this->shipping_array(),
            'item' => $item,
            'cart' => $cart,
            'shipping' => $shipping,
            'products' => $products,
            'order_note' => $this->orderNote,
            // 'return_url' => route('sandy-blocks-shop-page-cart-get', ['_user' => $this->_user->id]),
        ];

        $data = [
            'uref'  => md5(microtime()),
            'email' => $this->email,
            'price' => $price,
            'callback' => route('general-success', [
                'redirect' => route('out-products-single-page', ['slug' => $this->product->slug])
            ]),
            'frequency' => 'monthly',
            'currency' => $currency,
            'payment_type' => 'onetime',
            'meta' => $meta,
        ];

        //
        
        $call_function = \App\Yena\SandyCheckout::store_function($this->owner);
        $call = \App\Yena\SandyCheckout::cr(config('app.wallet.defaultMethod'), $data, $call_function);
        
        return $this->js("window.location.replace('$call');");
    };

    $shipping_array = function(){
      
        $location = ProductShipping::where('id', $this->shipping_location)->where('user_id', $this->owner->id)->first();
        
        if ($location) {
            $shipping = [
                'id' => $location->id,
                'iso_country'  => $location->country_iso,
                'country' => $location->country,
                'location_name' => $location->name,
                'location_description' => $location->description
            ];

            return $shipping;
        }

        return 'No Shipping';
    };

    $total_price = function(){
        $price = Cart::session($this->owner->id)->getTotal();

        // Get Shipping Price
        if ($location = ProductShipping::where('id', $this->shipping_location)->where('user_id', $this->owner->id)->first()) {
            $price = ($price + $location->price);
        }

        return $price;
    };
?>
<div>
    <div x-data="store_cart">
        <div class="modal-outer" @click="$event.stopPropagation()">
            <div>
               <div class="cart-container">
                <div class="cart-overlay" @click="page='-'" x-cloak x-show="page=='checkout'">
                    
                    <div class="bg-white w-[100%]" @click="$event.stopPropagation()">
                        <form wire:submit="checkout" class="p-[24px] flex flex-col items-start gap-[24px]">
                            <div class=" flex items-center justify-between w-[100%]">
                                <div class="text-base tracking-[1.2px] font-medium leading-[20px]">{{ __('Your info') }}</div>
                                <button class="close-button" @click="page='-'">
                                    <i class="ph ph-x text-xl"></i>
                                </button>
                            </div>

                            <div class="text-field w-[100%]">
                                <input type="text" wire:model="email" placeholder="{{ __('Email') }}">
                            </div>
                            @if (ao($owner->store, 'shipping_enable'))
                                <div class="text-field w-[100%]">
                                    <input type="text" wire:model="shipping.first_name" placeholder="{{ __('First Name') }}">
                                </div>
                                <div class="text-field w-[100%]">
                                    <input type="text" wire:model="shipping.last_name" placeholder="{{ __('Last Name') }}">
                                </div>
                                <div class="text-field w-[100%]">
                                    <input type="text" wire:model="shipping.phone_number" placeholder="{{ __('Mobile') }}">
                                </div>
                                <div class="text-field w-[100%]">
                                    <select x-model="country" class="text-sm">
                                        <option value="">{{ __('Country / Region') }}</option>
                                        @foreach ($countries as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="text-field w-[100%]">
                                    <input type="text" wire:model="shipping.location" placeholder="{{ __('Location') }}">
                                </div>
                            @endif

                            
                            @php
                                $error = false;

                                if(!$errors->isEmpty()){
                                    $error = $errors->first();
                                }

                                if(Session::get('error._error')){
                                    $error = Session::get('error._error');
                                }
                            @endphp
                            @if ($error)
                                <div class="mt-5 bg-red-200 font--11 p-1 px-2 rounded-md">
                                    <div class="flex items-center">
                                        <div>
                                            <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                        </div>
                                        <div class="flex-grow ml-1 text-xs">{{ str_replace('create.', '', $error) }}</div>
                                    </div>
                                </div>
                            @endif
                            <button class="button checkout-button !h-[56px] !bg-black !rounded-full">{{ __('Save') }}</button>
                        </form>
                    </div>
                </div>
                <div class="cart-overlay" @click="page='-'" x-cloak x-show="page=='note'">
                    <div class="bg-white w-[100%]" @click="$event.stopPropagation()">
                        <div class="p-[24px] flex flex-col items-start gap-[24px]">
                            <div class=" flex items-center justify-between w-[100%]">
                                <div class="text-base tracking-[1.2px] font-medium leading-[20px]">{{ __('Order special instructions') }}</div>
                                <button class="close-button" @click="page='-'">
                                    <i class="ph ph-x text-xl"></i>
                                </button>
                            </div>

                            <div class="text-field w-[100%]">
                                <textarea wire:model="orderNote" cols="30" rows="10"></textarea>
                            </div>
                            <button class="button checkout-button !h-[56px] !bg-black !rounded-full">{{ __('Apply') }}</button>
                        </div>
                    </div>
                </div>
                <div class="cart-overlay" @click="page='-'" x-cloak x-show="page=='shipping'">
                    <div class="bg-white w-[100%]" @click="$event.stopPropagation()">
                        <div class="p-[24px] flex flex-col items-start gap-[24px]">
                            <div class=" flex items-center justify-between w-[100%]">
                                <div class="text-base tracking-[1.2px] font-medium leading-[20px]">{{ __('Order special instructions') }}</div>
                                <button class="close-button" @click="page='-'">
                                    <i class="ph ph-x text-xl"></i>
                                </button>
                            </div>

                            <div class="text-field w-[100%]">
                                <select x-model="country" class="text-sm">
                                    <option value="">{{ __('Country / Region') }}</option>
                                    @foreach ($countries as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-1 md:!grid-cols-2 gap-4 w-[100%]">
                                @foreach ($shippings as $ship)
                                <div x-show="country == '{{ $ship->country_iso }}'">
                                    <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-start !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] w-[100%] {{ $shipping_location == $ship->id ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('shipping_location', '{{ $ship->id }}')">
                                          <div>
                                             <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                                                <img src="{{ \Country::icon($ship->country_iso) }}" class="rounded-full w-[100%] h-full object-cover" alt="">
                                             </div>
                                          </div>
                     
                                          <div class="flex flex-col">
                                             <p class="text-base font-bold text-[var(--yena-colors-gray-800)] truncate">{{ $ship->name }}</p>
                                             <p class="text-xs text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)]">{!! $owner->price($ship->price) !!}</p>
                                          </div>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            
                            <button class="button checkout-button !h-[56px] !bg-black !rounded-full" type="button" @click="page='-'">{{ __('Apply') }}</button>
                        </div>
                    </div>
                </div>
                
                  <div class="cart-head">
                     <div class="text-[40px] leading-[52px] tracking-[-.54px] font-bold cart-title">{{__('Cart')}}</div>
                     <button class="close-button" @click="openCart = false;">
                         <i class="ph ph-x text-xl"></i>
                     </button>
                  </div>
                  <div class="cart-items">
                     {{-- <div class="paragraph-small cart-progress-title">You've unlocked free shipping, <span>congratulations 🎉</span></div>
                     <div class="progress-bar">
                        <div class="progress-bar-progress" style="width: 100%;"></div>
                     </div> --}}
                    @if ($cart->isEmpty())
                    <div class="">
                        <div class="flex flex-col justify-center items-start px-0 py-[60px]">
                        {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
                        <p class="mt-3 text-base text-left md:!text-2xl font-bold">
                            {!! __t('Cart is empty. <br> Try adding a product to cart.') !!}
                        </p>
                        </div>
                    </div>
                    @endif

                     <div class="cart-cart-items">
                         @foreach ($cart as $item)
                         @php
                             $image = $item->associatedModel->getFeaturedImage();

                             if(!empty($op = ao($item, 'attributes.options'))){
                                foreach ($op as $key => $value) {
                                    if(ao($value, 'image')) $image = gs('media/store/variant', ao($value, 'image'));
                                }
                             }
                         @endphp
                         <div class="cart-item-product">
                            <div class="cart-item-wrapper">
                               <a href="{{ route('out-products-single-page', ['slug' => $item->associatedModel->slug]) }}">
                                 <img src="{{ $image }}" class="cart-item-image object-cover">
                               </a>
                               <div class="cart-item-heading">
                                  <div class="paragraph-small cart-item-brand">{{__('Product')}}</div>
                                  <a href="{{ route('out-products-single-page', ['slug' => $item->associatedModel->slug]) }}" class="subheading-medium cart-item-link">{{ $item->name }} 
                                        @if (!empty($op = ao($item, 'attributes.options')))
                                            @foreach ($op as $key => $v)
                                            <span class="text-xs font-normal">{{ ao($v, 'name') }}{{ $key === array_key_last($op) ? '' : ','  }}</span>
                                            @endforeach
                                        @endif
                                    </a>
                                  <div class="paragraph-small">{!! $owner->price($item->price) !!}</div>
                               </div>
                            </div>
                            <div class="cart-item-controls">
                               <div class="counter-container" wire:loading.class="fancy-disabled" wire:target="removeCart,upQuantity">
                                  <div class="label-medium counter-quantity">{{ $item->quantity }}</div>
                                  <div class="counter-buttons">
                                     <button class="counter-button" wire:click="upQuantity('{{ $item->id }}')" wire:loading.class="disabled" wire:target="upQuantity">
                                        <i class="ph ph-caret-up text-sm"></i>
                                     </button>
                                     <button class="counter-button" wire:click="downQuantity('{{ $item->id }}')" wire:loading.class="disabled" wire:target="downQuantity" type="button">
                                        <i class="ph ph-caret-down text-sm"></i>
                                    </button>
                                  </div>
                               </div>
                               <button class="label-small cart-item-button" wire:click="removeCart('{{ $item->id }}')" wire:loading.class="disabled" wire:target="removeCart" type="button">{{ __('Remove') }}</button>
                            </div>
                         </div>
                         @endforeach
                     </div>
                     {{-- <div class="cart-gift-wrapper">
                        <div class="cart-gift-heading">
                           <button type="button" class="checkbox-checkbox">Gift Wrap</button>
                           <div class="paragraph-small">For $10.00, gift wrap your entire order!</div>
                        </div>
                     </div> --}}
                  </div>
                  <div class="cart-checkout {{ $cart->isEmpty() ? '!hidden' : '' }}">
                     <div class="cart-buttons">
                        <button class="label-small" type="button" @click="page='note'">
                            {!! __i('Content Edit', 'Document.9', 'h-6 w-6 text-[#64748b]') !!}
                            {{ __('Order note') }}
                        </button>
                        @if (ao($owner->store, 'shipping_enable'))
                        <div class="cart-button-divider"></div>
                        <button class="label-small" type="button" @click="page='shipping'">
                            {!! __i('Delivery', 'package-box', 'h-6 w-6 text-[#64748b]') !!}
                            {{ __('Shipping') }}
                        </button>
                        @endif
                     </div>
                     <div class="checkout-container">
                        {{-- <div class="checkout-wrapper">
                           <div class="paragraph-medium">Order discount</div>
                           <div class="subheading-small checkout-discount">-$100.90</div>
                        </div> --}}
                        <div class="checkout-divider"></div>
                        <div class="checkout-wrappers">
                            <div class="checkout-wrapper">
                               <div class="paragraph-medium">{{__('Taxes included and shipping')}}</div>
                               <div class="paragraph-medium">{{ __('Subtotal') }}</div>
                            </div>
                            <div class="checkout-wrapper">
                               <div class="paragraph-medium">{{__('Quantity')}}</div>
                               <div class="paragraph-medium">{{ nr(Cart::session($owner->id)->getTotalQuantity(), 2, true) }}</div>
                            </div>
                            @if ($shipping_location && $ship = ProductShipping::where('id', $shipping_location)->first())
                            <div class="checkout-wrapper">
                               <div class="paragraph-medium">{{__('Shipping')}}</div>
                               <div class="paragraph-medium">{!! $owner->price($ship->price) !!}</div>
                            </div>
                            @endif
                           <div class="checkout-wrapper">
                              <div class="paragraph-medium">{{ __('Calculated at checkout') }}</div>
                              <div class="subheading-large checkout-total">{!! $owner->price(Cart::session($owner->id)->getTotal()) !!}</div>
                           </div>
                        </div>
                        <div class="checkout-buttons">
                           <button class="button checkout-button" @click="page='checkout'" type="button">{{ __('Checkout') }}</button>
                           <button class="button checkout-button !bg-white !text-black md:!hidden" @click="openCart = false;" type="button">{{ __('Close') }}</button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
    </div>
    @script
    <script>
        Alpine.data('store_cart', () => {
           return {
            page: '-',
            country: @entangle('shipping.country'),
            
            init(){
               let $this = this;
            },
           }
        });
    </script>
    @endscript
</div>