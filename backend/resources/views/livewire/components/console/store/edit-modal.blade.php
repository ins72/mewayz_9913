
<?php
   use App\Livewire\Actions\ToastUp;
   use App\Models\Product;
   use App\Models\ProductOption;
   use App\Traits\AudienceTraits;
   use function Livewire\Volt\{state, mount, placeholder, uses, on, rules, updated, usesFileUploads};

   usesFileUploads();
   
   on([
      'registerProduct' => function($id){
         $this->product_id = $id;
         $this->get();
      },
   ]);
   updated([
      'featured_image' => function(){
         if(!empty($this->featured_image)){
            $this->validate([
               'featured_image' => 'image|mimes:jpeg,png,jpg,gif|max:5048'
            ]);
            $filesystem = sandy_filesystem('media/store/image');
            storageDelete('media/store/image', $this->product->featured_img);


            $avatar = $this->featured_image->storePublicly('media/store/image', $filesystem);
            $avatar = str_replace("media/store/image/", "", $avatar);
            $this->product->featured_img = $avatar;
            $this->product->save();
            $this->get();

            $this->dispatch('productUpdated');
         }
      },
      'image' => function(){
         $this->skipRender();
         $this->validate([
               'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5048'
         ]);

         $filesystem = sandy_filesystem('media/store/image');
         $images = $this->product->media ?: [];
         
         foreach ($this->image as $photo) {
            $image = $photo->storePublicly('media/store/image', $filesystem);
            $image = str_replace("media/store/image/", "", $image);
            
            $images[] = $image;
         }

         $this->product->media = $images;
         $this->product->save();
         $this->get();

         $this->dispatch('productUpdated');
      },
   ]);

   uses([ToastUp::class]);

   state([
      'featured_image' => null,
      'image' => null,
      // 'countries' => fn() => \App\Yena\Country::list(),
   ]);

   state([
      'product' => fn() => new Product,
      'product_id' => null,
      'productArray' => [],
   ]);

   state([
      'variation_color_array' => [],
      'variation_color' => [
        'name' => '',
        'price' => '',
        'value' => ''
      ],
      'variation_size_array' => [],
      'variation_size' => [
        'name' => '',
        'price' => '',
        'value' => ''
      ],
      'variation_image_array' => [],
      'variation_image' => [
        'name' => '',
        'price' => '',
        'value' => ''
      ],
   ]);

   rules(fn () => [
        'product.name' => 'required',
        'product.description' => '',
        'product.productType' => 'string',
        'product.price_type' => 'string',
        'product.price' => 'string',
        'product.comparePrice' => 'string',
        'product.stock' => 'string',
      //   'product.stock_settings.enable' => '',
        'product.sku' => '',
        'product.min_quantity' => '',
        'product.external_product_link' => '',

        'variation_size_array.*.name' => 'string',
        'variation_size_array.*.price' => 'string',
        'variation_size_array.*.variation_value' => 'string',
        
        'variation_color_array.*.name' => 'string',
        'variation_color_array.*.price' => 'string',
        'variation_color_array.*.variation_value' => 'string',
        
        'variation_image_array.*.name' => 'string',
        'variation_image_array.*.price' => 'string',
        'variation_image_array.*.variation_value' => 'string',
   ]);
   mount(function(){
      // $this->get();
   });

   placeholder('
   <div class="p-5 w-full mt-1">
      <div class="flex mb-2 gap-4">
         <div>
            <div class="--placeholder-skeleton w-[200px] h-[200px] rounded-3xl"></div>
         </div>
         <div class="flex flex-col gap-2 w-full">
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-[150px] h-[40px] rounded-full mt-5"></div>
         </div>
      </div>
      
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');


   $get = function($id = null){
      $this->product = Product::where('user_id', iam()->id);

      if($this->product_id){
         $this->product = $this->product->where('id', $this->product_id);
      }

      $this->product = $this->product->orderBy('id', 'desc')->first();
      $this->productArray = $this->product->toArray();
      $this->__variations();
   };

   $__variations = function(){
      foreach (['size', 'image', 'color'] as $key => $type) {
          $dynamic_array = "variation_{$type}_array";
          $this->{$dynamic_array} = ProductOption::where('product_id', $this->product->id)->where('type', $type)->get();
      }
   };

   $add_variation = function($type){
      $filesystem = sandy_filesystem('media/store/variant');

      $dynamic_name = "variation_$type";
      $array = $this->{$dynamic_name};

      $value = ao($array, 'value');

      if($type == 'image' && !empty($array['value'])){
         $image = $array['value']->storePublicly('media/store/variant', $filesystem);
         $value = str_replace('media/store/variant/', "", $image);
      }

      if(!$price = ao($array, 'price')) $price = 0;

      $vari = new ProductOption;
      $vari->product_id = $this->product->id;
      $vari->user_id = iam()->id;
      $vari->type = $type;
      $vari->name = ao($array, 'name');
      $vari->price = (float) str_replace(',', '.', $price);
      $vari->variation_value = $value;
      $vari->save();


      $this->{$dynamic_name} = [
         'name' => '',
         'price' => '',
         'value' => ''
      ];
      $this->__variations();

      if($this->product->variant()->count() == 1){
         $this->dispatch('productUpdated');
      }
   };

   $remove_variation = function($id){
      if(!$variation = ProductOption::where('product_id', $this->product->id)->where('id', $id)->first()) return false;

      $type = $variation->type;

      if($type == 'image'){
         storageDelete('media/store/variant', $variation->variation_value);
      }
      
      $variation->delete();

      $this->__variations();
      
      if($this->product->variant()->count() == 0){
         $this->dispatch('productUpdated');
      }
   };

   $deleteMedia = function($index){
      $media = $this->product->media;
      if(!is_array($media)) return;

      if(!in_array($index, $media)) return;

      
      storageDelete('media/store/image', $index);
      // unset($media[$index]);

      foreach ($media as $key => $value) {
         if($value == $index) unset($media[$key]);
      }

      $media = array_values($media);

      $this->product->media = $media;
      $this->product->save();
      $this->get();

      $this->dispatch('productUpdated');
   };
   $save = function($audience){
      
        
      foreach (['size', 'image', 'color'] as $key => $type) {
          $dynamic_array = $this->{"variation_{$type}_array"};
          
          foreach ($dynamic_array as $item) {
               $item->price = (float) str_replace(',', '.', $item->price);
               $item->save();
          }
      }
      $this->product->price = (float) str_replace(',', '.', $this->product->price);
      $this->product->save();
      
      $this->flashToast('success', __('Product saved'));
      $this->dispatch('productUpdated');
   };
?>

<div class="w-full">
   <div x-data="store_editing">
      <div class="flex flex-col">
         <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
            <i class="fi fi-rr-cross text-sm"></i>
         </a>
   
         <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Edit Product') }}</header>
   
         <hr class="yena-divider">
   
         <form @submit.prevent="save" class="px-8 pt-2 pb-6">
   
            <div class="settings__upload" data-generic-preview>
               <div class="settings__preview !bg-[#f3f3f3] flex items-center justify-center !h-[8rem] !w-[8rem] !rounded-full overflow-hidden">
                  <input class="settings__input z-50" type="file" wire:model="featured_image">

                  @php
                      $_avatar = false;
                      if($product->featured_img){
                        $_avatar = $product->getFeaturedImage();
                      }
                      
                      if($featured_image) $_avatar = $featured_image->temporaryUrl();
                  @endphp
   
                  {!! __i('--ie', 'image-picture', 'w-8 h-8 z-[99] bg-[#fff] rounded-[550px] p-[7px] text-[#000]') !!}
                  @if ($_avatar)
                     <img src="{{ $_avatar }}" alt="">
                  @endif
               </div>
               <div class="settings__wrap">


                 <div class="text-[1.5rem] leading-10 font-bold">{{ $product->name }}</div>
                 <div class="settings__content flex items-center gap-2">{{ __('Created on :date', ['date' => \Carbon\Carbon::parse($product->created_at)->toFormattedDateString()]) }}</div>
                 <div class="flex gap-2">
                     <a @click="_page='-'" class="yena-button-stack --primary !text-xs !h-8">
                        {{ __('Manage') }}
                     </a>
                     <a @click="_page='gallery'" class="yena-button-stack --primary !text-xs !h-8">
                        {{ __('Gallery') }}
                     </a>
                     <a @click="_page='variation'" class="yena-button-stack --primary !text-xs !h-8">

                        {{ __('Variation') }}
                     </a>
                 </div>
               </div>
            </div>
            <div class="flex flex-col mt-4">
               <div x-show="_page=='-'" x-cloak>
                  <div class="flex flex-col gap-3 mb-2">
                     <div class="form-input">
                        <input type="text" name="name" wire:model="product.name" placeholder="{{ __('Name') }}">
                     </div>
                     <div
                        x-ref="quillEditor"
                        x-init="
                           quill = new window.Quill($refs.quillEditor, {theme: 'snow'});
                           quill.on('text-change', function () {
                              description = quill.root.innerHTML;
                           });
   
                           quill.root.innerHTML = description;
                        "
                     >
                     
                     </div>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                     <div class="form-input">
                        <input type="number" placeholder="{{ __('Price') }}" name="price" wire:model="product.price">
                     </div>
                     
                     <div class="form-input">
                        <input type="number" placeholder="{{ __('Sale Price') }}" wire:model="product.comparePrice" name="compare_price">
                     </div>
                  </div>

                  <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-2">
                     {{-- <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]"> --}}
                     <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Stock') }}</span>
                     <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                     <div class="form-input">
                        <input type="number" placeholder="{{ __('Stock') }}" wire:model="product.stock" name="product_stock">
                     </div>
                     <div class="form-input">
                        <input type="number" placeholder="{{ __('Sku') }}" wire:model="product.sku" name="stock[sku]">
                     </div>
                  </div>

                  
                  <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-2">
                     {{-- <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]"> --}}
                     <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Other') }}</span>
                     <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                     <div class="form-input">
                        <input type="number" placeholder="{{ __('Minimum Quantity') }}" name="extra[min_quantity]" wire:model="product.min_quantity">
                     </div>
                     <div class="form-input">
                        <input type="text" placeholder="{{ __('External Product Link') }}" name="extra[external_product_link]" wire:model="product.external_product_link">
                     </div>
               </div>
               </div>
               <div x-show="_page=='gallery'" x-cloak>
                  <div class="media-section ![height:initial] !relative ![top:initial] !w-full ![z-index:initial] !border-0" wire:ignore>
                     <div class="container-small tab-content-box !p-0 !m-0">
                        <div class="tab-content">
                              <div class="active" data-tab-content>
                                 <div class="device-library ![overflow:initial] !relative ![height:initial] !p-0">
                                    <div class="upload-manager"
                                    x-on:livewire-upload-start="uploading = true"
                                    x-on:livewire-upload-finish="uploading = false"
                                    x-on:livewire-upload-cancel="uploading = false"
                                    x-on:livewire-upload-error="uploading = false"
                                    x-on:livewire-upload-progress="progress = $event.detail.progress">
                                       <div class="upload-card relative">
                                          
                                          <input type="file" wire:model="image" multiple name="image" class="absolute right-0 top-0 w-full h-full opacity-0">

                                          <div class="upload-box mb-1">
                                             {!! __icon('interface-essential', 'image-picture-upload-arrow', 'w-5 h-5') !!}
                                          </div>

                                          <p x-cloak x-show="uploading" x-text="'{{ __('Uploading') }}' + ' · ' + progress + '%'"></p>

                                          <p x-cloak x-show="!uploading">{{ __('Add image  · 5MB max') }} </p>
                                       </div>
                                       <button class="btn !hidden" x-cloak :class="{'!hidden': !uploading}">{{ __('Cancel') }}</button>

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
                                          <div class="mb-1 bg-red-200 text-[11px] p-1 px-2 rounded-md">
                                             <div class="flex items-center">
                                                <div>
                                                   <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                                </div>
                                                <div class="flex-grow ml-1 text-xs">{{ $error }}</div>
                                             </div>
                                          </div>
                                       @endif
                                    
                                       <div class="files" :class="{
                                          '!hidden': !product.media || product.media && product.media.length == 0
                                       }">

                                       <template x-for="(media, index) in product.media" :key="index">
                                          <div class="file-card">
                                             
                                             <label x-data="{m: getMedia(media)}" x-intersect="m">
                                                <span class="checkmark !bg-white shadow shadow-xl" @click="$wire.deleteMedia(media)">
                                                      {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                                                </span>
                                                <span class="media-cover"></span>
                                                <img :src="m" loading="lazy" alt="">
                                                <div class="image-options"></div>
                                             </label>
                                          </div>
                                       </template>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div x-show="_page=='variation'" x-cloak>
               
                  <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-2">
                     {{-- <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]"> --}}
                     <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Color') }}</span>
                     <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                  </div>

                  <div class="flex">
                        <div class="grid grid-cols-3 gap-2 mr-2">
                           <div class="form-input w-full">
                              <input type="text" class="border-dashed" wire:model="variation_color.name" placeholder="{{ __('Add Name') }}">
                           </div>
                           <div class="form-input w-full">
                              <input type="text" class="border-dashed" wire:model="variation_color.price" placeholder="{{ __('Add Price') }}">
                           </div>
                           <div class="form-input is-pickr" wire:ignore style="--colorpickrbg: url({{ gs('assets/image/svg/color-picker-ico.svg') }})">
                              <input pickr-input placeholder="{{ __('Add Color') }}" class="border-dashed" type="text" wire:model="variation_color.value" >
                              <div class="color-mix"></div>
                           </div>
                        </div>
            
                        <div class="flex items-center">
                           <a class="ml-auto my-auto bg-[#f7f3f2] w-8 h-8 flex items-center justify-center rounded-md cursor-pointer" wire:click="add_variation('color')">
                           <i class="fi fi-rr-plus text-gray-400 text-xs font-bold"></i>
                           </a>
                        </div>
                  </div>
            
            
                  <div class="mt-3 flex flex-col gap-4">
                        @foreach ($variation_color_array as $key => $v)
                        <div class="flex">
                           <div class="grid grid-cols-3 gap-2 mr-2">
                              <div class="form-input w-full">
                                    <input type="text" class="" wire:model="variation_color_array.{{ $key }}.name" placeholder="{{ __('Add Name') }}">
                              </div>
                              <div class="form-input w-full">
                                    <input type="text" class="" wire:model="variation_color_array.{{ $key }}.price" placeholder="{{ __('Add Price') }}">
                              </div>
                              <div class="form-input w-full has-fancy-r">
                                    <input type="text" class="" wire:model="variation_color_array.{{ $key }}.variation_value" placeholder="{{ __('Add Color') }}">
            
                                    <div class="fancy-r">
                                       <div class="-fancy-rw rounded-full border-2 border-solid border-black" style="background: {{ $v->variation_value }}"></div>
                                    </div>
                              </div>
                           </div>
               
                           <div class="flex items-center">
                              <a class="ml-auto my-auto bg-g-gray w-8 h-8 flex items-center justify-center rounded-full cursor-pointer" wire:click="remove_variation({{ $v->id }})">
                              
                                    {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                              </a>
                           </div>
                        </div>
                        @endforeach
                  </div>
                  <!-- -->
                  <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-2">
                     {{-- <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]"> --}}
                     <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Size') }}</span>
                     <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                  </div>
                  <div class="flex">
                        <div class="grid grid-cols-3 gap-2 mr-2">
                           <div class="form-input w-full">
                              <input type="text" class="border-dashed" wire:model="variation_size.name" placeholder="{{ __('Add Name') }}">
                           </div>
                           <div class="form-input w-full">
                              <input type="text" class="border-dashed" wire:model="variation_size.price" placeholder="{{ __('Add Price') }}">
                           </div>
                           <div class="form-input w-full">
                              <input type="number" class="border-dashed" wire:model="variation_size.value" placeholder="{{ __('Add Size') }}">
                           </div>
                        </div>
            
                        <div class="flex items-center">
                           <a class="ml-auto my-auto bg-[#f7f3f2] w-8 h-8 flex items-center justify-center rounded-md cursor-pointer" wire:click="add_variation('size')">
                           <i class="fi fi-rr-plus text-gray-400 text-xs font-bold"></i>
                           </a>
                        </div>
                  </div>
            
            
                  <div class="mt-3 flex flex-col gap-4">
                        @foreach ($variation_size_array as $key => $v)
                        <div class="flex">
                           <div class="grid grid-cols-3 gap-2 mr-2">
                              <div class="form-input w-full">
                                    <input type="text" class="" wire:model="variation_size_array.{{ $key }}.name" placeholder="{{ __('Add Name') }}">
                              </div>
                              <div class="form-input w-full">
                                    <input type="text" class="" wire:model="variation_size_array.{{ $key }}.price" placeholder="{{ __('Add Price') }}">
                              </div>
                              <div class="form-input w-full">
                                    <input type="number" class="" wire:model="variation_size_array.{{ $key }}.variation_value" placeholder="{{ __('Add Size') }}">
                              </div>
                           </div>
               
                           <div class="flex items-center">
                              <a class="ml-auto my-auto bg-g-gray w-8 h-8 flex items-center justify-center rounded-full cursor-pointer" wire:click="remove_variation({{ $v->id }})">
                              
                                    {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                              </a>
                           </div>
                        </div>
                        @endforeach
                  </div>
         
                  <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-2">
                     {{-- <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]"> --}}
                     <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Image') }}</span>
                     <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                  </div>
                  <div class="flex">
                        <div class="grid grid-cols-3 gap-2 mr-2">
                           <div class="form-input w-full">
                              <input type="text" class="border-dashed" wire:model="variation_image.name" placeholder="{{ __('Add Name') }}">
                           </div>
                           <div class="form-input w-full">
                              <input type="text" class="border-dashed" wire:model="variation_image.price" placeholder="{{ __('Add Price') }}">
                           </div>
      
                           <div class="fake-input relative border-dashed has-fancy-r">
                              <input type="file" class="absolute right-0 top-0 opacity-0 w-full h-full" wire:model="variation_image.value" placeholder="{{ __('Add Image') }}">
      
                              <p>{{ __('Add Image') }}</p>
      
                              <div class="fancy-r">
                              
                                    {!! __i('interface-essential', 'image-picture', 'w-6 h-6') !!}
                              </div>
                              <div wire:loading wire:target="variation_image.value"
                                    class="text-xs font-bold m-0 absolute -bottom-4 left-0">
                                    {{ __('Processing image...') }}</div>
                           </div>
                        </div>
            
                        <div class="flex items-center">
                           <a wire:loading.class="disabled" wire:target="variation_image.value" class="ml-auto my-auto bg-[#f7f3f2] w-8 h-8 flex items-center justify-center rounded-md cursor-pointer" wire:click="add_variation('image')">
                           <i class="fi fi-rr-plus text-gray-400 text-xs font-bold"></i>
                           </a>
                        </div>
                  </div>
                  
                  <div class="mt-3 flex flex-col gap-2">
                        @foreach ($variation_image_array as $key => $v)
                        <div class="flex">
                           <div class="grid grid-cols-3 gap-2 mr-2">
                              <div class="form-input w-full">
                                    <input type="text" class="" wire:model="variation_image_array.{{ $key }}.name" placeholder="{{ __('Add Name') }}">
                              </div>
                              <div class="form-input w-full">
                                    <input type="text" class="" wire:model="variation_image_array.{{ $key }}.price" placeholder="{{ __('Add Price') }}">
                              </div>
                              <div class="fake-input relative has-fancy-r disabled">
            
                                    <p class="truncate pr-5">{{ $v->variation_value }}</p>
               
                                    <div class="fancy-r">
                                       <div class="-fancy-rw rounded-full bg-gray-300 bg-cover" style="background: url({{ gs('media/store/variant', $v->variation_value) }})"></div>
                                    </div>
                              </div>
                           </div>
               
                           <div class="flex items-center">
                              <a class="ml-auto my-auto bg-g-gray w-8 h-8 flex items-center justify-center rounded-full cursor-pointer" wire:click="remove_variation({{ $v->id }})">
                              
                                    {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                              </a>
                           </div>
                        </div>
                        @endforeach
                  </div>
               </div>
            </div>
   
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
            <button class="yena-button-stack mt-5 w-full">{{ __('Save') }}</button>
         </form>
      </div>
   </div>

   
   @script
   <script>
       Alpine.data('store_editing', () => {
          return {
            _page: '-',
            colors: [],
            uploading: false,
            progress: 0,
            titleText: '{{ __('Media') }}',
            product: @entangle('productArray').live,
            gs: '{{ gs('media/store/image') }}',
            description: @entangle('product.description'),

            getMedia(media){

               return this.gs +'/'+ media;
            },

            save(){
               let $this = this;
               

               $this.$wire.save($this.audience);
            },
            
            _color(color){
               return color.replace(/#/g, '');
            },
            init(){
               var $this = this;
            }
          }
       });
   </script>
   @endscript
</div>