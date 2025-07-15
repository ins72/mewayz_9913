


<?php

   use App\Models\Product;
   use App\Models\ProductCouponCode;
   use App\Livewire\Actions\ToastUp;

   use function Livewire\Volt\{state, mount, placeholder, rules, uses};

   uses([ToastUp::class]);
   rules(fn() => [
      'user.store.name' => 'string',
      'user.store.description' => 'string',
      'user.store.shipping_enable' => 'string',
      'user.store.shipping_type' => 'string',
      'user.store.coupon_enable' => 'string',
      'coupons.*.code' => 'string',
      'coupons.*.discount' => 'string',
      'coupons.*.type' => 'string',
      'coupons.*.product_id' => 'string',
      'coupons.*.end_date' => 'string',
   ]);

   state([
      'add_coupon' => [
        'code' => '',
        'type' => 'fixed_percentage',
        'amount' => '',
        'product' => null,
        'expiry' => '',
      ],
      'coupons' => [],
   ]);

   state([
      'products' => [],
      'user' => fn() => iam(), 
   ]);

   mount(function(){
      if(empty($this->user->store)){
         $this->user->store = [];
      }

      $this->products = Product::where('user_id', iam()->id)->get();
      $this->get();
   });

   $get = function(){
      $this->coupons = ProductCouponCode::where('user_id', iam()->id)->orderBy('id', 'DESC')->get();
   };

   $_remove_coupon = function($id){
      $coupon = ProductCouponCode::where('user_id', iam()->id)->where('id', $id)->delete();

      $this->get();
   };

   $_add_coupon = function(){
      $code = ao($this->add_coupon, 'code');
      if(empty($code)) $code = __('Code') . ' - ' . str()->random(4);
      $new = new ProductCouponCode;
      $new->user_id = $this->user->id;
      $new->code = $code;
      $new->type = ao($this->add_coupon, 'type');
      $new->discount = ao($this->add_coupon, 'amount') ?: 0;
      $new->product_id = ao($this->add_coupon, 'product');
      $new->end_date = !empty(ao($this->add_coupon, 'expiry')) ? ao($this->add_coupon, 'expiry') : null;
      $new->save();

       $this->add_coupon = [
            'code' => '',
            'type' => 'fixed_percentage',
            'amount' => '',
            'product' => null,
            'expiry' => '',
      ];

      $this->get();
   };

   $save = function(){
      $this->user->save();

      foreach ($this->coupons as $item) {
         $item->save();
      }
      
      $this->flashToast('success', __('Saved'));
   };
?>
<div>

    <div class="w-full" x-data="app_store_settings">
        <div>
           <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-1 right-1 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
              <i class="fi fi-rr-cross-small"></i>
           </a>
     
           <header class="flex py-4 px-6 flex-initial text-3xl font-black">{{ __('Store settings') }}</header>
     
           <hr class="yena-divider">
           
     
           <form wire:submit="save" class=" pt-4">
            <div class="px-6 pb-6">
               <div class="form-input mb-4">
                   <input type="text" name="store[shop_name]" wire:model="user.store.name" placeholder="{{ __('Store Name') }}">
               </div>
               <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                  <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Shipping') }}</span>
                  <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
               </div>
   
   
               <div class="input-box !mt-0 !border border-solid border-[var(--c-mix-1)]">
                  <div class="input-group !border-0">
                     <div class="switchWrapper">
                        <input class="switchInput !border-0" id="enable_shipping" type="checkbox" wire:model="user.store.shipping_enable">
   
                        <label for="enable_shipping" class="switchLabel">{{ __('Enable Shipping') }}</label>
                        <div class="slider"></div>
                     </div>
                  </div>
               </div>
               
               <div class="grid grid-cols-1 md:!grid-cols-2 gap-3">
                  <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-start !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] {{ !ao($user->store, 'shipping_type') ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('user.store.shipping_type', '0')">
                        <div>
                           <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                              {!! __i('Maps, Navigation', 'pin-location-hand-select', 'w-5 h-5 text-black') !!}
                           </div>
                        </div>
   
                        <div class="flex flex-col">
                           <p class="text-xl font-bold text-[var(--yena-colors-gray-800)]">{{ __('No strict') }}</p>
                           <p class="text-xs text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)]">{{ __('Customers can use any shipping location') }}</p>
                        </div>
                     </button>
                     <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-start !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] {{ ao($user->store, 'shipping_type') ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('user.store.shipping_type', '1')">
                           <div>
                              <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                                 {!! __i('Maps, Navigation', 'Earth, Pin, Location, Direction', 'w-5 h-5 text-black') !!}
                              </div>
                           </div>
      
                           <div class="flex flex-col">
                              <p class="text-xl font-bold text-[var(--yena-colors-gray-800)]">{{ __('Strict') }}</p>
                              <p class="text-xs text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)] whitespace-pre-line">{{ __('Customers can only use your shipping locations') }}</p>
                           </div>
                        </button>
                 </div>

                 <button class="yena-button-stack !w-full mt-4" @click="$dispatch('open-modal', 'store-shipping-modal')" type="button">{{ __('View Locations') }}</button>


               <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                  <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Coupon') }}</span>
                  <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
               </div>
   
               <div class="input-box !mt-0 !border border-solid border-[var(--c-mix-1)]">
                  <div class="input-group !border-0">
                     <div class="switchWrapper">
                        <input class="switchInput !border-0" id="enable_coupn" type="checkbox" wire:model="user.store.coupon_enable">
   
                        <label for="enable_coupn" class="switchLabel">{{ __('Enable Coupon') }}</label>
                        <div class="slider"></div>
                     </div>
                  </div>
               </div>

               <div class="grid grid-cols-1 md:!grid-cols-3 gap-4 mb-5" wire:ignore>
                  <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-center !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)]" type="button" @click="add_coupon.type = 'fixed_percentage'" :class="{
                     '!border-[var(--yena-colors-purple-400)]': add_coupon.type=='fixed_percentage'
                  }">
                        <div>
                           <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                              {!! __i('shopping-ecommerce', 'Sale, Discount, Promotion.2', 'w-5 h-5 text-black') !!}
                           </div>
                        </div>
   
                        <div class="flex flex-col">
                           <p class="text-xl font-bold text-[var(--yena-colors-gray-800)]">{{ __('Percentage') }}</p>
                        </div>
                  </button>
                  <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-center !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)]" type="button" @click="add_coupon.type = 'fixed_cart'" :class="{
                     '!border-[var(--yena-colors-purple-400)]': add_coupon.type=='fixed_cart'
                  }">
                        <div>
                           <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                              {!! __i('shopping-ecommerce', 'shopping-bag-percent', 'w-5 h-5 text-black') !!}
                           </div>
                        </div>
   
                        <div class="flex flex-col">
                           <p class="text-xl font-bold text-[var(--yena-colors-gray-800)]">{{ __('Fixed Cart') }}</p>
                        </div>
                  </button>
                  <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-center !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)]" type="button" @click="add_coupon.type = 'fixed_product'" :class="{
                     '!border-[var(--yena-colors-purple-400)]': add_coupon.type=='fixed_product'
                  }">
                        <div>
                           <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                              {!! __i('shopping-ecommerce', 'Sale, Discount, Promotion.2', 'w-5 h-5 text-black') !!}
                           </div>
                        </div>
   
                        <div class="flex flex-col">
                           <p class="text-xl font-bold text-[var(--yena-colors-gray-800)]">{{ __('Fixed Product') }}</p>
                        </div>
                  </button>
               </div>

                  <div class="flex">
                        <div :class="add_coupon.type == 'fixed_product' ? 'grid-cols-4' : 'grid-cols-3'" class="grid gap-2 mr-2 w-full">
                           <div class="form-input w-full">
                              <input type="text" class="border-dashed" wire:model="add_coupon.code" placeholder="{{ __('Code') }}">
                           </div>
                           <div class="form-input w-full has-fancy-r">
                              <input type="text" class="border-dashed" wire:model="add_coupon.amount" placeholder="{{ __('Amount') }}">
                              <template x-if="add_coupon.type == 'fixed_percentage'">
                                 <div class="fancy-r">
                                       <div class="-fancy-rw rounded-full flex items-center justify-center">
                                          {!! __i('shopping-ecommerce', 'Sale, Discount, Promotion.9', 'h-6 w-6') !!}
                                       </div>
                                 </div>
                              </template>
                              <template x-if="add_coupon.type == 'fixed_cart'">
                                 <div class="fancy-r">
                                       <div class="-fancy-rw rounded-full flex items-center justify-center">
                                          {!! __i('money', 'Coins', 'h-6 w-6') !!}
                                       </div>
                                 </div>
                              </template>
                           </div>
                           <template x-if="add_coupon.type == 'fixed_product'">
                              <div class="form-input w-full">
                                 <select name="product" class="border-dashed" wire:model="add_coupon.product">
                                       <option value="">{{ __('Select Product') }}</option>
                                       @foreach ($products as $item)
                                          <option value="{{ $item->id }}">{{ $item->name }}</option>
                                       @endforeach
                                 </select>
                              </div>
                           </template>
                           <label class="form-input w-full has-fancy-r">
                              <input type="date" class="border-dashed" wire:model="add_coupon.expiry" x-ref="add_coupon_expiry" placeholder="{{ __('Expiry Date') }}">
                           </label>
                     </div>
                     <div class="flex items-center">
                           <a class="ml-auto my-auto bg-[#f7f3f2] w-8 h-8 flex items-center justify-center rounded-md cursor-pointer" wire:click="_add_coupon">
                              <i class="fi fi-rr-plus text-gray-400 text-xs font-bold"></i>
                           </a>
                     </div>
                  </div>
      
                  <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4 {{ $coupons->isEmpty() ? '!hidden' : '' }}">
                     <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                     <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('All Codes') }}</span>
                     <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                  </div>

                  <div class="mt-3 flex flex-col gap-4">
                        @foreach ($coupons as $key => $v)
                        <div class="flex">
                           <div class="grid {{ $v->type == 'fixed_product' ? 'grid-cols-4' : 'grid-cols-3' }} gap-2 mr-2 w-full">
                              <div class="form-input w-full">
                                    <input type="text" class="" wire:model="coupons.{{ $key }}.code" placeholder="{{ __('Code') }}">
                              </div>
                              <div class="form-input w-full has-fancy-r">
                                    <input type="text" class="" wire:model="coupons.{{ $key }}.discount" placeholder="{{ __('Amount') }}">
                              
                                    @if ($v->type == 'fixed_percentage')
                                    <div class="fancy-r">
                                       <div class="-fancy-rw rounded-full flex items-center justify-center">
                                          {!! __i('shopping-ecommerce', 'Sale, Discount, Promotion.9', 'h-6 w-6') !!}
                                       </div>
                                    </div>
                                    @endif
                                    
                                    @if ($v->type == 'fixed_cart')
                                    <div class="fancy-r">
                                       <div class="-fancy-rw rounded-full flex items-center justify-center">
                                          {!! __i('money', 'Coins', 'h-6 w-6') !!}
                                       </div>
                                    </div>
                                    @endif
                              </div>
                              @if ($v->type == 'fixed_product')
                              <div class="form-input w-full">
                                    <select name="product" wire:model="coupons.{{ $key }}.product_id">
                                       <option value="">{{ __('Select Product') }}</option>
                                       @foreach ($products as $item)
                                          <option value="{{ $item->id }}">{{ $item->name }}</option>
                                       @endforeach
                                    </select>
                              </div>
                              @endif
                              @php
                                    $now = \Carbon\Carbon::now();
                                    $end_date = \Carbon\Carbon::parse($v->end_date)
                              @endphp
                              <div class="form-input w-full has-fancy-r">
                                    <input type="date" class="" wire:model="coupons.{{ $key }}.end_date" placeholder="{{ __('Expiry Date') }}">
                              
                                    {{-- <div class="fancy-r">
                                       <div class="-fancy-rw rounded-full flex items-center justify-center {{ $now > $end_date ? 'disabled-brown' : 'bright-green' }}">
                                          {!! __i('Smileys', $now > $end_date ? 'Smileys.1' : 'Smileys.5', 'h-5 w-5 text-white') !!}
                                       </div>
                                    </div> --}}
                              </div>
                           </div>
               
                           <div class="flex items-center">
                              <a class="ml-auto my-auto bg-[#f7f3f2] w-8 h-8 flex items-center justify-center rounded-md cursor-pointer" wire:click="_remove_coupon('{{ $v->id }}')">
                              
                                    {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                              </a>
                           </div>
                        </div>
                        @endforeach
                  </div>

                 <button class="yena-button-stack !w-full mt-4" type="submit">{{ __('Save') }}</button>
              </div>
           </form>
        </div>
     </div>


     @script
     <script>
         Alpine.data('app_store_settings', () => {
            return {
               add_coupon: @entangle('add_coupon'),
               init(){
                  var $this = this;
               }
            }
         });
     </script>
     @endscript
</div>