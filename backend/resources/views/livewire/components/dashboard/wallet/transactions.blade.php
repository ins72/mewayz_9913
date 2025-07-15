
<?php
   use App\Yena\SandyAudience;
   use App\Livewire\Actions\ToastUp;
   use App\Models\WalletTransaction;
   use function Livewire\Volt\{state, mount, placeholder, uses, on, updated};
   uses([ToastUp::class]);
   updated([
    'search' => fn() => $this->loadData(),
   ]);

   state([
      'transactions' => [],
   ]);

   state([
      'user' => fn () => iam(),
   ]);

   state([
        'has_more_pages' => false,
        'per_page' => 10,
   ]);

   state([
       'search' => '',
       'name' => '',
       'selected' => [],
   ]);

   mount(function(){
      $this->loadData();
   });

   placeholder('
   <div class="p-5 w-full mt-1">
        <div class="--placeholder-skeleton w-full h-[40px] rounded-md"></div>
      
        <div class="zp-5 rounded-xl zbg-[#f7f3f2] mt-4">
            <div class="--placeholder-skeleton w-full h-[40px] rounded-md"></div>
            <div class="--placeholder-skeleton w-full h-[400px] rounded-sm mt-2"></div>
        </div>
      <div class="--placeholder-skeleton w-full h-[40px] rounded-md mt-3"></div>
   </div>');


   $loadData = function(){
        if(request()->session()->has('session_rand')){
            if((time() - request()->session()->get('session_rand')) > 3600){
                request()->session()->put('session_rand', time());
            }
        }else{
            request()->session()->put('session_rand', time());
        }
        
        $transactions = WalletTransaction::where('user_id', $this->user->id);

        // if (!empty($query = $this->search)) {
        //     $searchBy = filter_var($query, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        //     $audiences = $audiences->where("contact->$searchBy", 'LIKE', '%' . $query . '%');
        // }

        // $transactions = $transactions->orderBy(\DB::raw('RAND('. request()->session()->get('session_rand') .')'));

        $transactions = $transactions->orderBy('id', 'desc')->paginate($this->per_page, ['*'], 'page');
        $this->per_page = $this->per_page + 5;
        $this->has_more_pages = $transactions->hasMorePages();

        $this->transactions = $transactions->items();
   };
?>
<div class="w-full">
   <div x-data="wallet_transactions_modal">
      <div class="flex flex-col">
         <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
            <i class="fi fi-rr-cross text-sm"></i>
         </a>
   
         <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Transactions') }}</header>
   
         <hr class="yena-divider">
   
         <div class="">
            <div class=" px-8 pt-5">
                {{-- <div class="form-input">
                    <label>{{ __('Folder Name') }}</label>
                    <input type="text" name="name" wire:model="name">
                </div> --}}

                <div>
                    {{-- <div class="form-input !bg-transparent">
                        <label>{{ __('Search') }}</label>
                        <input type="text" name="name" wire:model.live.debounce.850ms="search">
                    </div> --}}
                    @if ($has_more_pages)
                        <div wire:loading wire:target="loadData" class="w-full">
                            <div class="flex justify-center w-full">
                                <div class="lds-ring !flex justify-center items-center"><div></div><div></div><div></div><div></div></div>
                            </div>
                        </div>
                    @endif
   
                    <div class="overflow-y-auto h-full max-h-[calc(100vh_-_360px)]" x-ref="windowContactWrapper" wire:target="loadData">
                        
                    <div class="wallet-transactions mt-0 p-0 bg-white rounded-[24px] flex flex-col gap-2">
                        @foreach ($transactions as $item)
                            @php
                                $component = "livewire::components.console.wallet.include.transaction-include-$item->type";
                            @endphp
                            <a class="wallet-transactions-item bg-[#f7f3f2] rounded-[16px]"  @click="$dispatch('open-modal', 'transaction-modal'); $dispatch('registerTransaction', {id: '{{ $item->id }}'})">
                                <x-dynamic-component :component="$component" :$item/>
                            </a>
                        @endforeach
                    </div>
                    </div>
                </div>
            </div>
         </form>
      </div>
   </div>

   
   @script
   <script>
       Alpine.data('wallet_transactions_modal', () => {
          return {
            has_more_pages: @entangle('has_more_pages'),
            init(){
                let $this = this;
                let _throttleTimer = null;
                let _throttleDelay = 100;

                let handler = function(e) {
                    console.log($this.$refs.windowContactWrapper.scrollTop + $this.$refs.windowContactWrapper.clientHeight, $this.$refs.windowContactWrapper.scrollHeight - 100);

                    clearTimeout(_throttleTimer);
                    _throttleTimer = setTimeout(function() {
                        if ($this.$refs.windowContactWrapper.scrollTop + $this.$refs.windowContactWrapper.clientHeight > $this.$refs.windowContactWrapper.scrollHeight - 100) {
                            if($this.has_more_pages){
                                $this.$wire.loadData();
                            }
                        }
                    }, _throttleDelay);
                };

                $this.$refs.windowContactWrapper.removeEventListener('scroll', handler);
                $this.$refs.windowContactWrapper.addEventListener('scroll', handler);
            }
          }
       });
   </script>
   @endscript
</div>