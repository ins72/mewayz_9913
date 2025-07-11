


<?php
   use App\Models\ProductOrder;

   use function Livewire\Volt\{state, mount, placeholder, usesPagination, rules, with, uses};
   usesPagination();

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
      ->orderBy('total', 'DESC') // Order by the aggregated column instead
      ->get();
   };

   with(fn () => [
      'orders' => $this->getOrders(),
   ]);
   $orderStatus = function($status){
       if ($status == 1) {
           return __('Pending');
       }

       if ($status == 2) {
           return __('Completed');
       }

       if ($status == 3) {
           return __('Canceled');
       }
   };
   $getOrders = function(){
      $paginate = 10;
      $audience = ProductOrder::where('user_id', $this->user->id)
      ->whereHas('payee')->orderBy('id', 'DESC');

      // DO OTHER STUFF
      $audience = $audience->cursorPaginate(
            $paginate,
      );

      return $audience;
   };
?>

<div>

    <div class="w-full" x-data="app_store_customers">
        <div>
           <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-1 right-1 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
              <i class="fi fi-rr-cross-small"></i>
           </a>
     
           <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Store Orders') }}</header>

           <hr class="yena-divider">
     
           <div class="px-6 pb-6 pt-4">
            <div>
               
            </div>


            @if (!$orders->isEmpty())
            <div class="flex-table mt-0">
                <div class="flex-table-header">
                    <span class="is-grow">{{ __('Customer') }}</span>
                    <span>{{ __('Price') }}</span>
                    <span>{{ __('Status') }}</span>
                    <span>{{ __('Date') }}</span>
                    <span class="cell-end">{{ __('Action') }}</span>
                </div>
                @foreach ($orders as $item)
                <div class="flex-table-item rounded-2xl bg-white ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] !mb-3">
                    <div class="flex-table-cell is-media is-grow" data-th="">
                        <div class="flex relative cursor-pointer">
                           <img src="{{ $item->payee->getAvatar() }}" class="w-[38px] h-[38px] [transition:all_.2s_ease-in] object-cover rounded-xl [box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)] p-0.5" alt="">
                        </div>
                        <div>
                            <span class="item-name mb-2">{{ $item->payee->name }}</span>
                            <span class="item-meta">
                                <span>#{{ $item->id }}</span>
                            </span>
                        </div>
                    </div>
                    <div class="flex-table-cell" data-th="{{ __('Price') }}">
                        <span>
                            {!! iam()->price($item->price) !!}
                        </span>
                    </div>
                    <div class="flex-table-cell" data-th="{{ __('Status') }}">
                        <span class="my-0">{{ $this->orderStatus($item->status) }}</span>
                    </div>
                    <div class="flex-table-cell" data-th="{{ __('Date') }}">
                        <span class="my-0">{{ \Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}</span>
                    </div>
                    <div class="flex-table-cell cell-end" data-th="{{ __('Action') }}">
                        <a href="{{--  --}}" class="sandy-expandable-btn ml-auto"><span>{{ __('Manage') }}</span></a>
                    </div>
                </div>
                @endforeach
                
                <div class="mt-10">
                    {!! $orders->links() !!}
                </div>
            </div>
            @else
            <div class="p-10 py-20">
                {{-- @include('include.is-empty') --}}
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