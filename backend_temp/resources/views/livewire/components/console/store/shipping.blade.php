


<?php
   use App\Models\ProductShipping;

   use function Livewire\Volt\{state, mount, placeholder, rules};

   rules(fn() => [
      'shipping.*.name' => '',
      'shipping.*.price' => '',
      'shipping.*.country_iso' => '',
   ]);
   
   state([
      'shipping' => [],
      'countries' => fn () => \Country::list(),
   ]);
   
   state([
      'name' => '',
      'price' => '',
      'country' => '',
   ]);

   mount(function(){
      $this->_get();
   });


   $_get = function(){
      $this->shipping = ProductShipping::where('user_id', iam()->id)->orderBy('id', 'DESC')->get();
   };

   $createShipping = function(){
      if(empty($this->country)) $this->country = 'US';
      if(empty($this->name)) $this->name = 'United States';
      if(empty($this->price)) $this->price = 0;

      $new = new ProductShipping;
      $new->user_id = iam()->id;
      $new->name = $this->name;
      $new->price = $this->price;
      $new->country = \Country::country_code($this->country);
      $new->country_iso = $this->country;
      $new->save();

      $this->name = '';
      $this->price = '';
      $this->country = '';

      $this->_get();
   };

   $deleteShipping = function($id){
      if(!$shipping = ProductShipping::where('id', $id)->where('user_id', iam()->id)->first()) return false;

      $shipping->delete();

      $this->_get();
   };

   $save = function(){
      foreach ($this->shipping as $item) {
         $item->country = \Country::country_code($item->country_iso); 
         $item->save();
      }

      $this->_get();
   };
?>

<div>

    <div class="w-full" x-data="app_store_shipping">
        <div>
           <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-1 right-1 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
              <i class="fi fi-rr-cross-small"></i>
           </a>
     
           <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Store shipping') }}</header>

           <hr class="yena-divider">
     
           <div class="px-6 pb-6 pt-4">
            <div>
               <div class="flex gap-2">
                  <div class="grid grid-cols-3 gap-2">
                     <div>
                        <div class="yena-form-group">
                           <div class="--wrap !pl-0">
                              <div class="--wrap-list">
                                 
                                 <div class="-wrap-input-group">
                                    <input type="text" wire:model="name" class="!pl-4 !border-dashed !zzborder-black !border-2" placeholder="{{ __('Add name') }}">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div>
                        <div class="yena-form-group">
                           <div class="--wrap !pl-0">
                              <div class="--wrap-list">
                                 
                                 <div class="-wrap-input-group">
                                    <input type="text" wire:model="price" class="!pl-4 !border-dashed !border-2" placeholder="{{ __('Add price') }}">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div>
                        <div class="yena-form-group">
                           <div class="w-full">
                              <div class="--right-element flex-col !h-full">
                                 <div class="flex flex-1"></div>
                                 <div class="flex h-[var(--yena-sizes-10)] pt-[var(--yena-space-4)] pb-[var(--yena-space-4)] items-center">
                                    {!! __i('Maps, Navigation', 'map-pin-location-circle', 'w-6 h-6') !!}
                                 </div>
                              </div>
         
                              <div class="--wrap !pl-0">
                                 <div class="--wrap-list">
                                    
                                    <div class="-wrap-input-group">
                                       <select name="country" wire:model="country" id="" class="border-dashed border-2 h-[2.5rem] rounded-md [border-color:inherit] text-[1rem]">
                                           <option value="">{{ __('Add Country') }}</option>
               
                                           @foreach ($countries as $key => $value)
                                               <option value="{{ $key }}">{{ $value }}</option>
                                           @endforeach
                                       </select>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                     
                  <div class="flex items-center">
                     <a class="ml-auto my-auto bg-[#f7f3f2] w-8 h-8 flex items-center justify-center rounded-md cursor-pointer" wire:click="createShipping">
                        <i class="fi fi-rr-plus text-gray-400 text-xs font-bold"></i>
                     </a>
                  </div>
               </div>
            </div>

            
            <hr class="yena-divider my-4">
            @if ($shipping->isEmpty())
               <div class="flex flex-col justify-center items-center px-[20px] py-[60px]">
                  {!! __i('Delivery', 'Fast Delivery', 'w-14 h-14') !!}
                  <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                     {!! __t('You have no shipping locations. <br> Create one to get started.') !!}
                  </p>
               </div>
            @endif

            <form wire:submit="save" class="{{ $shipping->isEmpty() ? 'hidden' : '' }}">
               
               <div class="flex flex-col gap-2">
                  @foreach ($shipping as $index => $item)
                  <div>
                     <div class="flex gap-2">
                        <div class="grid grid-cols-3 gap-2">
                           <div>
                              <div class="yena-form-group">
                                 <div class="--wrap !pl-0">
                                    <div class="--wrap-list">
                                       
                                       <div class="-wrap-input-group">
                                          <input type="text" wire:model="shipping.{{ $index }}.name" class="!pl-4 !border-dashed !border-2" placeholder="{{ __('Add name') }}">
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div>
                              <div class="yena-form-group">
                                 <div class="--wrap !pl-0">
                                    <div class="--wrap-list">
                                       
                                       <div class="-wrap-input-group">
                                          <input type="text" wire:model="shipping.{{ $index }}.price" class="!pl-4 !border-dashed !border-2" placeholder="{{ __('Add price') }}">
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div>
                              <div class="yena-form-group">
                                 <div class="w-full">
                                    <div class="--right-element flex-col !h-full">
                                       <div class="flex flex-1"></div>
                                       <div class="flex h-[var(--yena-sizes-10)] pt-[var(--yena-space-4)] pb-[var(--yena-space-4)] items-center">
                                          <img src="{{ gs('assets/image/countries', strtolower($item->country_iso) . ".svg") }}" alt="" class="w-5 h-5 object-cover rounded-full">
                                       </div>
                                    </div>
               
                                    <div class="--wrap !pl-0">
                                       <div class="--wrap-list">
                                          
                                          <div class="-wrap-input-group">
                                             <select name="country" wire:model="shipping.{{ $index }}.country_iso" class="border-dashed border-2 h-[2.5rem] rounded-md [border-color:inherit] text-[1rem]">
                                                 <option value="">{{ __('Add Country') }}</option>
                     
                                                 @foreach ($countries as $key => $value)
                                                     <option value="{{ $key }}">{{ $value }}</option>
                                                 @endforeach
                                             </select>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                           
                        <div class="flex items-center">
                           <a class="ml-auto my-auto bg-red-500 text-white w-8 h-8 flex items-center justify-center rounded-md cursor-pointer" wire:click="deleteShipping('{{ $item->id }}')">
                              {!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}
                           </a>
                        </div>
                     </div>
                  </div>
                  @endforeach
               </div>
               <button class="yena-button-stack mt-5 w-full">{{ __('Save') }}</button>
            </form>


           </div>
        </div>
     </div>


     @script
     <script>
         Alpine.data('app_store_shipping', () => {
            return {
              init(){
                 var $this = this;

                  document.addEventListener('alpine:navigated', (e) => {
                     // $this.$wire._get();
                  });
              }
            }
         });
     </script>
     @endscript
</div>