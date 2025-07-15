
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
    mount(function(){
        if(!$this->user->wallet_settings){
            $this->user->wallet_settings = [
                'silence' => 'golden'    
            ];
        }
    });

    rules(fn() => [
        'user.wallet_settings.paypal_email' => '',
        'user.wallet_settings.crypto_channel' => '',
        'user.wallet_settings.crypto_address' => '',
        'user.wallet_settings.bank_name' => '',
        'user.wallet_settings.bank_account_number' => '',
        'user.wallet_settings.bank_more_info' => '',
    ]);

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


   $save = function(){
        $this->user->save();

        $this->flashToast('success', __('Saved succesfully'));
        $this->dispatch('close');
   };
?>


<div class="w-full">
   <div class="flex flex-col" x-data="app_wallet_settings">
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross text-sm"></i>
      </a>

      <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Settings') }}</header>

      <hr class="yena-divider">

      <form wire:submit="save" class="px-8 pt-4 pb-6">
        <div>
            <div class="grid grid-cols-2 gap-4">
                <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-center !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)]" type="button" @click="withdrawal_type = 'paypal'" :class="{
                   '!border-[var(--yena-colors-purple-400)]': withdrawal_type=='paypal'
                }">
                      <div>
                         <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                            <i class="ph ph-paypal-logo text-xl"></i>
                         </div>
                      </div>
 
                      <div class="flex flex-col">
                         <p class="text-xl font-bold text-[var(--yena-colors-gray-800)]">{{ __('Paypal') }}</p>
                      </div>
                </button>
                <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-center !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)]" type="button" @click="withdrawal_type = 'crypto'" :class="{
                   '!border-[var(--yena-colors-purple-400)]': withdrawal_type=='crypto'
                }">
                      <div>
                         <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                            <i class="ph ph-currency-btc text-xl"></i>
                         </div>
                      </div>
 
                      <div class="flex flex-col">
                         <p class="text-xl font-bold text-[var(--yena-colors-gray-800)]">{{ __('Crypto') }}</p>
                      </div>
                </button>
                <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-center !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] col-span-2" type="button" @click="withdrawal_type = 'bank'" :class="{
                   '!border-[var(--yena-colors-purple-400)]': withdrawal_type=='bank'
                }">
                      <div>
                         <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                            <i class="ph ph-piggy-bank text-xl"></i>
                         </div>
                      </div>
 
                      <div class="flex flex-col">
                         <p class="text-xl font-bold text-[var(--yena-colors-gray-800)]">{{ __('Bank Transfer') }}</p>
                      </div>
                </button>
            </div>



            <div class="mt-4">
                <div x-cloak x-show="withdrawal_type=='paypal'">
                    <div class="form-input">
                        <label>{{ __('Paypal Email') }}</label>
                        <input type="text" wire:model="user.wallet_settings.paypal_email" placeholder="{{ __('example@gmail.com') }}">
                    </div>
                </div>
                <div x-cloak x-show="withdrawal_type=='crypto'">
                    <div>
                        <div class="form-input">
                            <label>{{ __('Crypto Channel') }}</label>
                            <input type="text" wire:model="user.wallet_settings.crypto_channel" placeholder="{{ __('USDT') }}">
                        </div>
                        <div class="form-input mt-3">
                            <label>{{ __('Crypto Address') }}</label>
                            <input type="text" wire:model="user.wallet_settings.crypto_address" placeholder="{{ __('xxxxxxxxxxxxxxxxxx') }}">
                        </div>
                    </div>
                </div>
                <div x-cloak x-show="withdrawal_type=='bank'">
                    <div class="form-input">
                        <label>{{ __('Bank name') }}</label>
                        <input type="text" wire:model="user.wallet_settings.bank_name" placeholder="{{ __('Chase') }}">
                    </div>
                    <div class="form-input mt-3">
                        <label>{{ __('Bank account number') }}</label>
                        <input type="text" wire:model="user.wallet_settings.bank_account_number" placeholder="{{ __('xxxxxxxxxxx') }}">
                    </div>
                    <div class="form-input mt-3">
                      <label>{{ __('Payment Details') }}</label>
                      <textarea wire:model="user.wallet_settings.bank_more_info" cols="30" rows="4"></textarea>
                    </div>
            
                    <div class="sandy-expandable-block mt-3 w-full !mb-0">
                        <h4 class="sandy-expandable-header">
                        <div class="text-left">
                            <h4 class="sandy-expandable-title">{{ __('More details') }}</h4>
                            <p class="sandy-expandable-description">{{ __('Please add a well descriptive information about your withdrawal bank. Iban, country, swift code, etc.') }}</p>
                        </div>
                        </h4>
                    </div>
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
                    <div class="flex-grow ml-1 text-xs">{{ $error }}</div>
                </div>
            </div>
        @endif
        
        <button class="yena-button-stack mt-3 w-full !gap-1">
            {{ __('Save') }}
        </button>
      </form>
   </div>
   @script
   <script>
       Alpine.data('app_wallet_settings', () => {
          return {
            withdrawal_type: 'paypal',
          }
       });
   </script>
   @endscript
</div>