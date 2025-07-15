
<?php
    use App\Models\User;
    use App\Models\Product;
    use App\Models\WalletTransaction;
    use App\Livewire\Actions\ToastUp;
    use function Livewire\Volt\{state, mount, placeholder, uses, on, updated};

   on([
      'registerTransaction' => function($id){
         $this->transaction_id = $id;
         $this->_get();
      },
   ]);
   state([
      'image' => null,

      'create' => [
         'name' => '',
      ]
   ]);

   state([
      'user' => fn () => iam(),
   ]);
    state([
        'transaction' => fn () => new WalletTransaction
    ]);

    state([
        'payer' => null,
        'buyer' => null,
    ]);

    state([
        'type_class' => function(){
            return $this->transaction->type == 'minus' ? 'text-red-500' : 'text-green-500';
        },
        'type_text' => function(){
            return $this->transaction->type == 'minus' ? '-' : '+';
        }
    ]);
    $_get = function(){
        $this->transaction = WalletTransaction::where('user_id', $this->user->id);

        if($this->transaction_id){
            $this->transaction = $this->transaction->where('id', $this->transaction_id);
        }

        $this->transaction = $this->transaction->orderBy('id', 'desc')->first();

        $this->payer = User::find(ao($this->transaction->transaction, 'payee'));
        $this->buyer = User::find(ao($this->transaction->transaction, 'bio'));
    };

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
   
?>


<div class="w-full">
   <div class="flex flex-col">
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross text-sm"></i>
      </a>

      <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Transaction') }}</header>

      <hr class="yena-divider">

      <div class="px-8 pt-2 pb-6" x-data="app_wallet_transaction">
        <div class="!p-0 payment-process-wrapper shadow-none">
            <div class="currency-payment is-price mb-7">
            <div class="currency-sign h4 {{ $type_class }}">{!! \Currency::symbol($transaction->currency) !!}</div>
            <div class="currency-field w-full">
                <div class="currency-value !opacity-100 {{ $type_class }}">{{ $type_text . nr($transaction->amount, false, 2) }}</div>
            </div>
            </div>
            <div class="payment">
            <div class="color-primary flex flex-col mb-5">
                <span class="font-bold text-lg">{{ __('Page') }}</span>
            </div>
            @if ($buyer)
            <div class="payment-options p-5">
                <div class="payment-option">
                    <div class="w-10 h-10 rounded-full overflow-hidden mr-3">
                        <img src="{{ $buyer->getAvatar() }}" class="w-[100%] h-full object-cover" alt="">
                    </div>
                    <div class="payment-details">
                        <div class="payment-category">{{ $buyer->name }}</div>
                        <div class="payment-content">{{ $buyer->email }}</div>
                    </div>
                </div>
            </div>
            @endif
            <div class="color-primary flex flex-col mb-5 mt-4">
                <span class="font-bold text-lg">{{ __('Payer') }}</span>
            </div>
            
            @if ($payer)
            <div class="payment-options p-5">
                <div class="payment-option">
                    <div class="w-10 h-10 rounded-full overflow-hidden mr-3">
                        <img src="{{ $payer->getAvatar() }}" class="w-[100%] h-full object-cover" alt="">
                    </div>
                    <div class="payment-details">
                        <div class="payment-category">{{ $payer->name }}</div>
                        <div class="payment-content">{{ $payer->email }}</div>
                    </div>
                </div>
            </div>
            @endif
            <div class="payment-table">
                <div class="payment-flex hidden">
                    <div class="payment-cell">{{ __('Amount to settled.') }}</div>
                    <div class="payment-cell">{!! price_with_cur($transaction->currency, settings('payment.currency')) !!}</div>
                </div>
                <div class="payment-flex">
                    <div class="payment-cell">{{ __('Paid Amount') }}</div>
                    <div class="payment-cell">{!! price_with_cur($transaction->currency, $transaction->amount) !!}</div>
                </div>
                <div class="payment-flex">
                    <div class="payment-cell">{{ __('Tracking ID') }}</div>
                    <div class="payment-cell">#{{ $transaction->spv_id }}</div>
                </div>
                <div class="payment-flex">
                    <div class="payment-cell">{{ __('Date') }}</div>
                    <div class="payment-cell">{{ \Carbon\Carbon::parse($transaction->created_at)->toFormattedDateString() }}</div>
                </div>
            </div>
            
            <div class="sandy-expandable-block mt-10 w-full">
                <h4 class="sandy-expandable-header">
                <div class="text-left">
                    <h4 class="sandy-expandable-title">{{ ao($transaction->transaction, 'item.name') }}</h4>
                    <p class="sandy-expandable-description">{{ ao($transaction->transaction, 'item.description') }}</p>
                </div>
                </h4>
            </div>

            @if (ao($transaction->transaction, 'location'))
            <div class="color-primary flex flex-col mb-5 mt-5">
                <span class="font-bold text-lg">{{ __('Paying Location') }}</span>
                <span class="text-xs text-gray-400">{{ __('Transaction fee might vary depending on paying location.') }}</span>
            </div>
            
            <div class="grid grid-cols-2 gap-4 rounded-2xl">
                <div class="">
                    <div class="card customize mb-0 p-3">
                        <div class="card-header">
                        <div class="flex items-center mb-3">
                            <div class="h-avatar sm is-video bg-white mr-2 hidden md:flex">
                                <i class="la la-phone c-black"></i>
                            </div>
                            <p class="text-sm m-0">{{ __('Device') }}</p>
                        </div>
                        </div>
                        <div>
                        <div class="flex justify-between items-center bg-white p-5 mb-0 rounded-2xl">
                            <span class="item-name text-xs">{{ ao($transaction->transaction, 'location.agent.os') }}</span>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="card customize mb-0 p-3">
                        <div class="card-header">
                        <div class="flex items-center mb-3">
                            <div class="h-avatar sm is-video bg-white mr-2 hidden md:flex">
                                <i class="sni sni-browser c-black"></i>
                            </div>
                            <p class="text-sm m-0">{{ __('Browsers') }}</p>
                        </div>
                        </div>
                        <div>
                        <div class="flex justify-between items-center bg-white p-5 mb-0 rounded-2xl">
                            <span class="item-name text-xs">{{ ao($transaction->transaction, 'location.agent.browser') }}</span>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="card customize mb-0">
                        <div class="card-header">
                        <div class="flex items-center mb-3">
                            <div class="h-avatar sm is-video bg-white mr-2 hidden md:flex">
                                <i class="sni sni-flag c-black"></i>
                            </div>
                            <p class="text-sm m-0">{{ __('Country') }}</p>
                        </div>
                        </div>
                        <div>
                        <div class="flex-table is-insight">
                            <div class="flex-table-item no-shadow bg-white rounded-2xl">
                                <div class="flex-table-cell is-media is-grow mb-0" data-th="">
                                    <div class="w-10 h-10 rounded-full overflow-hidden mr-3">
                                        <img src="{{ Country::icon(ao($transaction->transaction, 'location.country.iso')) }}" class="w-[100%] h-full object-cover" alt="">
                                    </div>
                                    <div>
                                    <span class="item-name text-xs">{{ ao($transaction->transaction, 'location.country.city') }}, {{ ao($transaction->transaction, 'location.country.iso') }}</span>
                                    <span class="item-meta text-xs">{{ ao($transaction->transaction, 'location.country.name') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            </div>
        </div>
      </div>
   </div>
   @script
   <script>
       Alpine.data('app_wallet_transaction', () => {
          return {
            
          }
       });
   </script>
   @endscript
</div>