
<?php
   use App\Models\Product;
   use App\Livewire\Actions\ToastUp;
   use function Livewire\Volt\{state, mount, placeholder, uses, rules, updated, usesFileUploads};

   usesFileUploads();

   state([
      'image' => null,

      'create' => [
         'name' => '',
      ]
   ]);

   rules(fn () => [
      'create.name' => 'required',
   ]);
   uses([ToastUp::class]);
   mount(fn() => '');

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


   $createProduct = function(){
      $this->validate();
      $create = $this->create;
      $a = new Product;

      if(!empty($this->image)){
         $this->validate([
             'image' => 'image|mimes:jpeg,png,jpg,gif|max:5048'
         ]);


         $filesystem = sandy_filesystem('media/store/image');
         $image = $this->image->storePublicly('media/store/image', $filesystem);
         $image = str_replace("media/store/image/", "", $image);
         $a->featured_img = $image;
      }
      
      $a->user_id = iam()->id;
      $a->stock_settings = [
         'enable' => 0,
         'sku' => 0,
      ];
      $a->extra = [
         'min_quantity' => 0,
         'external_product_link' => '',
      ];
      $a->name = ao($create, 'name');
      $a->save();

      
      $this->flashToast('success', __('Product created'));
      $this->dispatch('close');

      $this->dispatch('productCreated');
   };
?>


<div class="w-full">
   <div class="flex flex-col">
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross text-sm"></i>
      </a>

      <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Product') }}</header>

      <hr class="yena-divider">

      <form wire:submit="createProduct" class="px-8 pt-2 pb-6">

         <div class="settings__upload" data-generic-preview>
            <div class="settings__preview !bg-[#f3f3f3] flex items-center justify-center overflow-hidden">
               @php
                   $_avatar = false;
                   
                   if($image) $_avatar = $image->temporaryUrl();
               @endphp

               @if (!$_avatar)
                  {!! __i('--ie', 'image-picture', 'text-gray-300 w-8 h-8') !!}
               @endif
               @if ($_avatar)
                  <img src="{{ $_avatar }}" alt="">
               @endif
               <div wire:loading.class.remove="!hidden" wire:target="image" class="absolute w-full h-full flex items-center justify-center bg-[#00000063] !hidden">
                  <div class="loader-animation-container flex items-center justify-center"><div class="inner-circles-loader !w-7 !h-7"></div></div>
               </div>
            </div>
            <div class="settings__wrap">
              <div class="text-[2rem] leading-10 font-bold">{{ __('Featured Image') }}</div>
              <div class="settings__content">{{ __('We recommended an image of at least 80x80. Gifs work too.') }}</div>
              <div class="settings__file">
               <input class="settings__input z-50" type="file" wire:model="image">
               <a class="yena-button-stack">{{ __('Choose') }}</a>
              </div>
            </div>
         </div>
         <div class="form-input mt-4">
             <label>{{ __('Product Name') }}</label>
             <input type="text" name="name" wire:model="create.name">
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