


<?php
   use App\Models\ProductOrder;

   use function Livewire\Volt\{state, mount, placeholder, rules};

   state([
      'customers' => [],
      'user' => fn() => iam(),
   ]);

   mount(function(){
      $this->_get();
   });


   $_get = function(){
      $this->customers = ProductOrder::where('user_id', $this->user->id)
      ->whereHas('payee')
      ->select('payee_user_id', \DB::raw('count(*) as total'))
      ->groupBy('payee_user_id')
      ->orderBy('total', 'DESC')
      ->get();
   };
?>

<div>

    <div class="w-full" x-data="app_store_customers">
        <div>
           <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-1 right-1 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
              <i class="fi fi-rr-cross-small"></i>
           </a>
     
           <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Store Customers') }}</header>

           <hr class="yena-divider">
     
           <div class="px-6 pb-6 pt-4">
               @if ($customers->isEmpty())
               <div class="p-10 py-20">
                  <div class="flex flex-col justify-center items-center px-[20px] py-[60px]">
                     {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
                     <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                        {!! __t('You have no customer. <br> Share your product(s) to get a customer.') !!}
                     </p>
                  </div>
              </div>
               @endif
               <div class="flex flex-col gap-4">
                  @foreach($customers as $item)
                  <div class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !flex-col !p-3 !bg-[#ffffffa3] !text-left gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] !w-full">
                     <div class="flex items-center w-full">
                        <div class="mr-[8px]">
                           <div class="bg-transparent rounded-full overflow-hidden w-8 h-8">
                              <img src="{{ $item->payee->getAvatar() }}" class="object-cover h-full w-full" alt=" ">
                           </div>
                        </div>
                        <div class="flex flex-col truncate">
                           <p class="text-xs font-bold text-[var(--yena-colors-gray-800)] truncate">{{ $item->payee->name }}</p>
                           <p class="text-[12px] text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)] truncate">{{ __('Total Orders') }}  - {{ number_format($item->total) }}</p>
                        </div>
                     </div>
                  </div>
                  @endforeach
               </div>
           </div>
        </div>
     </div>


     @script
     <script>
         Alpine.data('app_store_customers', () => {
            return {
              init(){
                 var $this = this;

                  document.addEventListener('alpine:navigated', (e) => {
                     $this.$wire._get();
                  });
              }
            }
         });
     </script>
     @endscript
</div>