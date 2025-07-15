<?php
    use App\Models\ProductOrder;
    use function Livewire\Volt\{state, mount, placeholder, usesPagination, rules, with, uses};
    usesPagination();

    state([
        'user' => fn() => iam(),
    ]);
    
    state([
        'owner'
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
        if(!auth()->check()) return [];
        $paginate = 10;
        $audience = ProductOrder::where('payee_user_id', $this->user->id)
        ->whereHas('payee')->orderBy('id', 'DESC');

        // DO OTHER STUFF
        $audience = $audience->cursorPaginate(
                $paginate,
        );

        return $audience;
    };
?>
<div>
    <div x-data="store_orders">
        <div class="modal-outer" @click="$event.stopPropagation()">
            <div>
               <div class="cart-container">
                  <div class="cart-head">
                     <div class="text-[40px] leading-[52px] tracking-[-.54px] font-bold cart-title">{{__('My Orders')}}</div>
                     <button class="close-button" @click="openOrders = false;">
                         <i class="ph ph-x text-xl"></i>
                     </button>
                  </div>
                  <div class="cart-items">
                    @if (auth()->check())
                        <div class="">
                            <div x-show="showSingle">
                                <livewire:products.orders.single :key="uukey('app', 'products.orders.single')">
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
                        @else
                        <div class="">
                            <div class="flex flex-col justify-center items-start px-0 py-[60px]">
                                {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
                                <p class="mt-3 text-base text-left md:!text-2xl font-bold">
                                    {!! __t('Login to see your orders. <br>') !!}
                                </p>
                                <a class="button checkout-button mt-2" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </div>
                        </div>
                    @endif
                  </div>
                  <div class="cart-checkout">
                     <div class="checkout-container">
                        <div class="checkout-buttons">
                           <button class="button checkout-button !bg-white !text-black" @click="openOrders = false;" type="button">{{ __('Close') }}</button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
    </div>
    @script
    <script>
        Alpine.data('store_orders', () => {
           return {
                showSingle: false,
                singleId: null,

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