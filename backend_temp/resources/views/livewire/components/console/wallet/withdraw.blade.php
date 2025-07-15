
<?php
    use App\Models\WalletWithdrawal;
    use App\Models\Product;
    use App\Livewire\Actions\ToastUp;
    use function Livewire\Volt\{state, mount, placeholder, uses, rules, updated, usesFileUploads};

    state([
        'note' => '',
        'amount' => 0,
        'platform_fee' => fn() => config('app.wallet.withdraw_percentage')
    ]);

    state([
        'user' => fn() => iam(),
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


   $withdraw = function(){
        $this->validate([
            'amount' => 'required'
        ]);

      //
        $percentage = (int) settings('app.wallet.withdraw_percentage') ?: 0;
        $amount = (int) $this->amount;
        $platform_fee = ($percentage / 100) * $amount;

        if($amount > $this->user->balanceFloat){
            session()->flash('error._error', __('Insufficient funds'));
            return;
        }

        if(WalletWithdrawal::where('user_id', $this->user->id)->where('is_paid', 0)->first()){
            session()->flash('error._error', __('You have a pending withdrawal'));
            return;
        }


        $w = new WalletWithdrawal;
        $w->user_id = $this->user->id;
        $w->amount = $amount;
        $w->note = $this->note;
        $w->transaction = [];
        $w->is_paid = 0;
        $w->save();
        
        // Send Email
        // $email = new \App\Email;
        // // Get email template
        // $template = $email->template('wallet/admin_withdraw', ['user' => $this->user, 'withdraw' => $w]);

        // $emails = settings('notification.emails');
        // $emails = explode(',', $emails);
        // $emails = str_replace(' ', '', $emails);

        // if(!empty($emails)){


        //     $mail = [
        //         'to' => $emails,
        //         'subject' => __('Withdrawal request', []),
        //         'body' => $template
        //     ];
        //     // Send Email
        //     $email->send($mail);
        // }

        // return back()->with('success', __('Withdraw request has been placed.'));

      
        $this->flashToast('success', __('Withdraw request has been placed'));
        $this->dispatch('withdrawalUpdate');
        $this->dispatch('close');
   };
?>


<div class="w-full">
   <div class="flex flex-col" x-data="app_wallet_withdraw">
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross text-sm"></i>
      </a>

      <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Withdraw') }}</header>

      <hr class="yena-divider">

      <form wire:submit="withdraw" class="px-8 pt-2 pb-6">
        <div class="wallet-payout-page">
            <div class="wallet-payout-balance flex flex-col items-center justify-center mb-5">
                <div class="-balance">
                    <div class="currency-payment is-price rounded-xl justify-center">
                        <div class="currency-sign h4">{!! \Currency::symbol(strtoupper(config('app.wallet.currency'))) !!}</div>
                        <div class="currency-field">
                            <div class="currency-value !pr-10" x-text="amount"></div>
                            <input class="currency-input" type="text" @input="proceesAmount" autofocus="" name="amount" value="0" x-model="amount">
                        </div>
                    </div>
                    
                </div>
                <div class="-fee">{!! __('Withdraw up to :amount', ['amount' => $user->price($user->balanceFloat, 2)]) !!}</div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="wallet-items-card flex items-center justify-between bg-gray-100 !border-2 border-solid border-gray-300" @click="instant_withdraw ? enable_instant=true : enable_instant=false" :class="{
                    '!border-gray-300': !enable_instant,
                    '!border-black': enable_instant,
                    '!opacity-20': !instant_withdraw,
                    '!cursor-pointer': instant_withdraw,
                }">
                    <div class="flex flex-col">
                        <div class="--top-heading h-20 w-20 bg-white text-black text-xl rounded-full flex items-center justify-center" :class="{
                            'bg-white text-black': !enable_instant,
                            'bg-black !text-white': enable_instant,
                        }">
                            <i class="fi fi-rr-bolt flex text-2xl !text-[inherit]"></i>
                        </div>
                        <div class="--bal">{{ __('Instant') }}</div>
                        <div class="--sub">{{ __('In a few minutes') }}</div>
                    </div>
                </div>
                <div class="wallet-items-card flex items-center justify-between bg-gray-100 cursor-pointer !border-2 border-solid border-gray-300" @click="enable_instant=false" :class="{
                    '!border-gray-300': enable_instant,
                    '!border-black': !enable_instant,
                }">
                    <div class="flex flex-col">
                        <div class="--top-heading h-20 w-20 bg-white text-black text-xl rounded-full flex items-center justify-center" :class="{
                            'bg-white text-black': enable_instant,
                            'bg-black !text-white': !enable_instant,
                        }">
                            <i class="fi fi-rr-bank flex text-2xl !text-[inherit]"></i>
                        </div>
                        <div class="--bal">{{ __('Normal') }}</div>
                        <div class="--sub">{{ __('Can take up to 1-3 days') }}</div>
                    </div>
                </div>
            </div>

            <div class="payout-fees mb-10 mt-5 pl-8">

                
                <div class="--item">
                    <div class="--dot-wrap mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="11" viewBox="0 0 26 11" fill="none">
                            <g clip-path="url(#clip0_0_4801)">
                              <path opacity="0.6" d="M0.787109 6.47807H18.7871" stroke="#CFDBD5" stroke-linecap="square"/>
                              <circle cx="20.2871" cy="5.97807" r="5" fill="#747A80"/>
                            </g>
                            <defs>
                              <clipPath id="clip0_0_4801">
                                <rect width="25" height="10" fill="white" transform="translate(0.287109 0.978069)"/>
                              </clipPath>
                            </defs>
                          </svg>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="--item-name">{{ __('Fee') }}</div>
                        <div class="--item-fee" x-html="currency + fee"></div>
                    </div>
                </div>
            </div>

            <div class="-p-info-o mt-5">
                {{ __('Transfers made on weekends or holidays take longer. All transfers are subject to review and could be delayed or stopped if we identify an issue.') }}
            </div>

            @if ($success = Session::get('success._success'))
                <div class="mt-5 bg-green-200 font--11 p-1 px-2 rounded-md">
                    <div class="flex items-center">
                        <div>
                            <i class="fi fi-rr-cross-circle flex text-xs"></i>
                        </div>
                        <div class="flex-grow ml-1 text-xs">{{ $success }}</div>
                    </div>
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
                        <div class="flex-grow ml-1 text-xs">{{ $error }}</div>
                    </div>
                </div>
            @endif
            
            <button class="yena-button-stack mt-5 w-full !gap-1">
                {{ __('Withdraw') }} <span x-html="currency + amount_to_settle"></span>
            </button>
        </div>
      </form>
   </div>
   @script
   <script>
       Alpine.data('app_wallet_withdraw', () => {
          return {
            amount: @entangle('amount'),
            platform_fee: @entangle('platform_fee'),
            amount_to_settle: 0,
            fee: 0,
            enable_instant: 0,
            instant_withdraw: true,

            currency_code: '{!! strtoupper(config('app.wallet.currency')) !!}',
            currency: '{!! \Currency::symbol(strtoupper(config('app.wallet.currency'))) !!}',
            isFloat(n){
                return Number(n) === n && n % 1 !== 0;
            },

            proceesAmount(){
                if(!this.amount){
                    this.amount = 0;
                }
                var amount = parseFloat(this.amount);

                var fee = (this.platform_fee / 100) * amount;
                this.fee = fee.toFixed(2);
                this.amount_to_settle = (amount - fee).toFixed(2);

                if (amount.length > 1 && amount.startsWith('0') && !amount.startsWith('0.')) {
                    this.amount = amount.slice(1);
                }

                
                this.amount = String(amount).replace(/[^0-9.]/g, '');

                // console.log(cleaned, String(amount).replace(/[^0-9.]/g, ''));
            },

            init(){
                this.proceesAmount();
            }
          }
       });
   </script>
   @endscript
</div>