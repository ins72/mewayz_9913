
<?php
   use App\Models\YenaTemplate;
   use App\Models\Site;
   use App\Livewire\Actions\ToastUp;
   use App\Yena\Site\Generate;
   use function Livewire\Volt\{state, mount, placeholder, uses, rules, updated};

   state([
      'name' => '',
      'payment' => ''
   ]);

   rules(fn () => [
      'name' => 'required|min:2',
      'payment' => 'required',
   ]);
   uses([ToastUp::class]);
   mount(fn() => '');

   $buyTemplate = function($id){
      $this->validate();
      if(!$template = YenaTemplate::find($id)) abort(404);
      $user = iam();

      $_c = Site::where('user_id', iam()->id)->count();
      if(__o_feature('consume.sites', iam()) != -1 && $_c >= __o_feature('consume.sites', iam())){
         $this->js('window.runToast("error", "'. __('You have reached your site creation limit. Please upgrade your plan.') .'");');
         return;
      }

      $data = [
         'uref'  => md5(microtime()),
         'email' => iam()->email,
         'price' => $template->price,
         'callback' => route('dashboard-templates-success'),
         'payment_type' => 'onetime',
      ];

      $serialize = \App\Yena\SandyCheckout::buyTemplate($user, $template, $this->name, $user->get_original_user()->id);
      $call = \App\Yena\SandyCheckout::cr($this->payment, $data, $serialize);
      return $this->js("window.location.replace('$call');");
   };

   placeholder('
   <div class="p-5 w-full">
      <div class="--placeholder-skeleton w-full h-[60px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[60px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[60px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');
?>


<div class="w-full">
   <div class="flex flex-col">
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross text-sm"></i>
      </a>

      <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Buy Template') }}</header>

      <hr class="yena-divider">

      <form wire:submit="buyTemplate(selectedTemplate)" class="px-8 pt-4 pb-6">
         <div class="flex flex-col gap-6 border-b border-solid border-gray-300 pb-5 mb-5">
            <x-input-x wire:model="name" label="{{ __('Your business name') }}"></x-input-x>
         </div>

         @php
            $payments = new \App\Yena\Payments;
            $allpayments = $payments->getInstalledMethods();
         @endphp
         <div class="mx-auto mt-1 w-full p-8 bg-[var(--yena-colors-gray-100)] rounded-lg">
            <div class="checkout-container opacity-100">
               <div class="h-auto px-0 md:relative md:top-0 md:bottom-0 flex flex-col gap-6">
                  @foreach ($allpayments as $key => $item)
                        @if (settings("payment_$key.status"))
                        <div>
                           <div class="flex items-center justify-between p-2 px-4 rounded-lg md:text-base md:!px-4 shadow-md bg-white border border-solid border-[var(--yena-colors-gray-200)] {{ $payment == $key ? '!text-[var(--yena-colors-trueblue-500)] ![box-shadow:none] !bg-[var(--yena-colors-trueblue-50)]' : '' }}" wire:click="$set('payment', '{{ $key }}')">
                              <div class="flex items-center">
                                    <img alt="{{ ao($item, 'name') }}" class="h-4" src="{{ gs('assets/image/payments', !empty(ao($item, 'thumbnail_full')) ? ao($item, 'thumbnail_full') : ao($item, 'thumbnail')) }}">
                                    
                                    <small class="block pt-2 text-xs leading-4 dark:text-gray-500 text-gray-500">{{ settings("payment_$key.description") }}</small>
                              </div>

                              <a class="bg-black text-white relative inline-block rounded-lg px-3 py-1 flex items-center cursor-pointer {{ $payment == $key ? 'disabled' : '' }}">
                                    <span class="text-theme cursor-pointer text-xs font-bold">{{ $payment == $key ? __('Selected') : __('Select') }}</span>
                              </a>
                           </div>
                        </div>
                        @endif
                  @endforeach
               </div>
            </div>
         </div>
         @php
            $error = false;

            if(!$errors->isEmpty()){
                  $error = $errors->first();
            }
         @endphp
         @if ($error)
            <div class="mt-4 bg-red-200 text-[11px] p-1 px-2 rounded-md">
                  <div class="flex items-center">
                     <div>
                        <i class="fi fi-rr-cross-circle flex text-xs"></i>
                     </div>
                     <div class="flex-grow ml-1 text-xs">{{ $error }}</div>
                  </div>
            </div>
         @endif

         <button class="yena-button-stack mt-5 w-full">{{ __('Proceed') }}</button>
      </form>
   </div>
</div>