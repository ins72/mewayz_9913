


<?php
   use App\Models\ProductOrder;

   use function Livewire\Volt\{state, mount, placeholder, usesPagination, rules, with, uses};
   usesPagination();

   state([
      'user' => fn() => iam(),
   ]);

   mount(function(){
    //   $this->_get();
   });

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

    <div class="w-full" x-data="app_store_orders">
        <div>
           <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-1 right-1 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
              <i class="fi fi-rr-cross-small"></i>
           </a>
     
           <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px] flex items-center gap-3">
            <div class="flex justify-between items-center" :class="{
                '!hidden': !showSingle
            }">
                <div class="bg-white mb-2 w-10 h-10 rounded-lg flex items-center justify-center ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] cursor-pointer" @click="closeSingle">
                    <i class="ph ph-caret-left text-lg"></i>
                </div>
            </div>
            
            <span x-text="showSingle ? '{{ __('Single Order') }}' : '{{ __('Store Orders') }}'"></span>
            </header>

           <hr class="yena-divider">
     
           <div class="px-6 pb-6 pt-4">
                <div x-show="showSingle">
                    <livewire:components.console.store.orders.single :key="uukey('app', 'console.store.orders.single')">
                </div>


                <div x-show="!showSingle">
                    @if (!$orders->isEmpty())
                    <div class="flex-table mt-0 flex flex-col gap-3">
                        <div class="flex-table-header !mb-0">
                            <span class="is-grow">{{ __('Customer') }}</span>
                            <span>{{ __('Price') }}</span>
                            <span>{{ __('Status') }}</span>
                            <span>{{ __('Date') }}</span>
                            <span class="cell-end">{{ __('Action') }}</span>
                        </div>
                        @foreach ($orders as $item)
                        <div class="flex-table-item rounded-2xl bg-white ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] !mb-0">
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
                                <a @click="openSingle('{{ $item->id }}')" class="yena-button-stack ml-auto"><span>{{ __('Manage') }}</span></a>
                            </div>
                        </div>
                        @endforeach

                        @if ($orders->hasMorePages())
                        <div class="mt-5">
                            {!! $orders->links() !!}
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="p-10 py-20">
                        <div class="flex flex-col justify-center items-center px-[20px] py-[60px]">
                           {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
                           <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                              {!! __t('You have no order. <br> Share your product(s) to get orders.') !!}
                           </p>
                        </div>
                    </div>
                    @endif
                </div>
           </div>
        </div>
     </div>


     @script
     <script>
         Alpine.data('app_store_orders', () => {
            return {
                showSingle: false,
                singleId: '14',

                openSingle(id){
                    this.singleId = id;
                    this.showSingle = true;
                },
                closeSingle(){
                    this.singleId = null;
                    this.showSingle = false;
                },
                init(){
                    var $this = this;

                    document.addEventListener('alpine:navigated', (e) => {
                        //  $this.$wire._get();
                    });
                },
            }
         });
     </script>
     @endscript
</div>