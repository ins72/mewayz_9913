<?php

    use App\Models\Plan;
    use function Laravel\Folio\name;
    use function Livewire\Volt\{state, uses, mount, computed};
    name('dashboard-upgrade-view');

    state(['plan']);
    state([
        'duration' => 'month',
        'payment' => null,
        'user' => fn() => iam(),
    ]);

    $_purchase = function(){
        //dd($this->plan->name);
        $this->validate([
            'payment' => 'required',
            'duration' => 'required'
        ]);

        $duration = $this->duration;
        $user = $this->user;
        $plan = $this->plan;
        $currency = settings('payment.currency');

        $price = $this->plan->price;
        if($duration == 'year') $price = $this->plan->annual_price;
        
        $plan_id = md5("spv_page_plan_recurring_id.{$duration}.{$plan->id}.{$price}");
        $meta = [
            'payment_mode' => [
                'type' => 'recurring',
                'interval' => $this->duration,
                'title' => __(':plan of :price', ['plan' => $plan->name, 'price' => "$price $currency", 'type' => ucfirst($duration)]),
                'name' => $plan->name,
                'id' => $plan_id
            ]
        ];

        $data = [
            'uref'  => md5(microtime()),
            'email' => $this->user->email,
            'price' => $price,
            'callback' => route('dashboard-upgrade-success'),
            'frequency' => $this->duration == 'month' ? 'monthly' : 'annually',
            'currency' => $currency,
            'payment_type' => 'recurring',
            'meta' => $meta,
        ];
        
        $call_function = \App\Yena\SandyCheckout::call_function($duration, $user, $plan);
        $call = \App\Yena\SandyCheckout::cr($this->payment, $data, $call_function);
        
        return $this->js("window.location.replace('$call');");
    };

    $_take_trial = function(){
        if(!$this->plan->_can_take_trial()) session()->flash('trial_error', __('Trial already taken.'));

        $duration = (int) $this->plan->trial_days;
        
        $this->user->cancelCurrentSubscription();
        $sub = $this->user->upgradeCurrentPlanTo($this->plan, $duration, false, false);

        $plan_history = new \App\Models\PlansHistory;
        $plan_history->plan_id = $this->plan->id;
        $plan_history->trial = 1;
        $plan_history->sub_id = $sub->id;
        $plan_history->user_id = $this->user->id;
        $plan_history->save();


        session()->flash('success', __('Plan activated successfully.'));

        return $this->redirect(route('dashboard-index'))->with('success', __('Trial activated successfully.'));
    };

    $_activate_free = function(){
        if($this->user->activeSubscription() && iam()->activeSubscription()->plan && $this->user->activeSubscription()->plan->id == $this->plan->id) return false;
        
        $this->user->cancelCurrentSubscription();
        $this->user->upgradeCurrentPlanTo($this->plan, 2999, false, false);

        $plan_history = new \App\Models\PlansHistory;
        $plan_history->plan_id = $this->plan->id;
        $plan_history->user_id = $this->user->id;
        $plan_history->save();

        session()->flash('success', __('Plan activated successfully.'));
        return $this->redirect(route('dashboard-index'));
    };

?>

<div>
    @if ($this->plan->is_free)
        @include('livewire.components.upgrade.view.free')
    @endif

    @if (!$this->plan->is_free)
    <div>
        @section('title', __('Purchase'))
        <style>
            #-page{
                padding: 0 !important;
            }
        </style>
        @php
            $price = $this->plan->price;
    
            if($duration == 'year') $price = $this->plan->annual_price;
        @endphp
        <div class="flex flex-col">
            <div class="flex-grow duration-100 max-w-[464px] mx-auto w-full">
               <div>
                  <div class="w-full bg-white overflow-hidden rounded-lg [box-shadow:var(--yena-shadows-base)] px-0 pt-12 lg:pt-20 lg:pt-20">
                     <div class="mx-auto max-w-md px-8 lg:mx-0 border-b border-solid border-gray-300 pb-5">
                        <div class="font-heading mb-2 px-0 font--12 font-extrabold upper-case tracking-wider flex items-center mb-2">
                           <h1 class="text-2xl font-bold leading-normal sm:leading-normal whitespace-nowrap font--caveat">{{ __('Payment Details') }}</h1>
                           {{-- <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div> --}}
                        </div>
                        {{-- <p class="mt-2 text-sm text-gray-600">{{ __('Complete your purchase by providing your payment information.') }}</p> --}}
        

                        @php
                            $sub = iam()->activeSubscription();
                        @endphp
                        @if ($sub && $sub->plan)
                        <p class="mt-4 pr-8 pt-2 text-sm text-gray-500 border-t border-solid border-gray-300">{!! __t('Purchasing this plan will cancel your current <b>:plan</b> plan.', ['plan' => $sub->plan->name, 'expiry' => $sub->remainingDays()]) !!}</p>
                        @endif
    
    
                        @if ($this->plan->_can_take_trial())
                            <p class="mt-4 pr-8 pt-2 text-sm text-gray-500 border-t border-solid border-gray-300">{!! __t('Try out our :duration days <b>:plan</b> for Free!', ['plan' => $this->plan->name, 'duration' => $this->plan->trial_days]) !!}</p>
                            <form  wire:submit.prevent="_take_trial" class="mt-3">
    
                                @if ($error = Session::get('trial_error'))
                                <div class="mb-5 bg-red-200 font--11 p-1 px-2 rounded-md">
                                        <div class="flex items-center">
                                            <div>
                                                <i class="fi fi-rr-cross-circle flex"></i>
                                            </div>
                                            <div class="flex-grow ml-1">{{ $error }}</div>
                                        </div>
                                    </div>
                                @endif
                            
                                <button class="sandy-button block">
                                    <div class="--sandy-button-container">
                                        <span class="text-xs">{{ __('Take our free trial') }}</span>
                                    </div>
                                </button>
                            </form>
                        @endif
                     </div>
                     
                    <section class="plan-duration center pb-5 border-b border-solid border-gray-300 px-0 my-5 px-8">
                        <div class="grid grid-cols-2 gap-4 px-5">
                            <label class="sandy-big-checkbox">
                                <input type="radio" name="duration" wire:model.live="duration" class="sandy-input-inner" value="month">
                                <div class="checkbox-inner md:text-base md:!px-4 shadow-md bg-[var(--yena-colors-gradient-light)] border border-solid border-[var(--yena-colors-gray-200)] h-full {{ $duration == 'month' ? '!text-[var(--yena-colors-trueblue-500)] ![box-shadow:none] !bg-[var(--yena-colors-trueblue-50)]' : '' }}">
                                    <div class="checkbox-wrap">
                                        <div class="content">
                                            <h1 class="text-lg font-bold font--caveat">{!! \Currency::symbol(settings('payment.currency')) . formatNum($this->plan->price) !!}</h1>
                                            <p>{{ __("Pay once Month") }}</p>
                                        </div>
                                        <div class="icon !hidden">
                                            <div class="active-dot rounded-sm w-5 h-5">
                                                <i class="fi fi-rr-check text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="sandy-big-checkbox">
                            <input type="radio" name="duration" wire:model.live="duration" class="sandy-input-inner" value="year">
                            <div class="checkbox-inner md:text-base md:!px-4 shadow-md bg-[var(--yena-colors-gradient-light)] border border-solid border-[var(--yena-colors-gray-200)] h-full {{ $duration == 'year' ? '!text-[var(--yena-colors-trueblue-500)] ![box-shadow:none] !bg-[var(--yena-colors-trueblue-50)]' : '' }}">
                                <div class="checkbox-wrap">
                                    <div class="content">
                                        <h1 class="text-lg font-bold font--caveat">{!! \Currency::symbol(settings('payment.currency')) . formatNum($this->plan->annual_price) !!}</h1>
                                        <p>{{ __("Pay once Yearly") }}</p>
                                    </div>
                                    <div class="icon !hidden">
                                        <div class="active-dot rounded-sm w-5 h-5">
                                            <i class="fi fi-rr-check text-xs"></i>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </label>
    
                        </div>
                    </section>
                    
                    @php
                        $payments = new \App\Yena\Payments;
                        $allpayments = $payments->getInstalledMethods();
                    @endphp
                     <div class="mx-auto mt-1 w-full px-8">
                        <div class="checkout-container opacity-100">
                           <div class="h-auto px-0 md:relative md:top-0 md:bottom-0 flex flex-col gap-6">
                                @foreach ($allpayments as $key => $item)
                                    @if (settings("payment_$key.status"))
                                    <div>
                                        <div class="flex items-center justify-between p-2 px-4 rounded-lg md:text-base md:!px-4 shadow-md bg-[var(--yena-colors-gradient-light)] border border-solid border-[var(--yena-colors-gray-200)] {{ $payment == $key ? '!text-[var(--yena-colors-trueblue-500)] ![box-shadow:none] !bg-[var(--yena-colors-trueblue-50)]' : '' }}" wire:click="$set('payment', '{{ $key }}')">
                                            <div class="flex items-center">
                                                <img alt="{{ ao($item, 'name') }}" class="h-4" src="{{ gs('assets/image/payments', !empty(ao($item, 'thumbnail_full')) ? ao($item, 'thumbnail_full') : ao($item, 'thumbnail')) }}">
                                                
                                                <small class="block pt-0 text-xs leading-4 dark:text-gray-500 text-gray-500">{{ settings("payment_$key.description") }}</small>
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
                     <div class="text-black flex w-full justify-center py-12 pr-20 bg-[var(--yena-colors-gray-50)] rounded-t-2xl mt-5">
                        <div class="mx-auto w-full max-w-sm">
                           <div class="w-full px-8">
                              <div class="whitespace-nowrap sm:text-lg">{{ __('Subscribe to') }} <b>{{ $this->plan->name }}</b> - {{ $duration == 'month' ? __('Monthly') : __('Yearly') }}</div>
                              <div class="mt-2 flex items-end">
                                 <div class="text-2xl font-bold sm:text-4xl">{!! price_with_cur(\Currency::symbol(settings('payment.currency')), $price) !!}</div>
                                 <div class="mb-1 ml-1 flex flex-none">
                                    <div>/ {{ $duration == 'month' ? __('month') : __('year') }}</div>
                                 </div>
                              </div>
                              @php
                                  $terms_link = settings('others.terms');
                                  $privacy_link = settings('others.privacy');
                              @endphp
                              
                              <p class="mt-2 mb-5 text-[11px] color-gray">{!! __t("By proceeding, I agree to the <a href=\"$terms_link\">Terms of service</a> and <a href=\"$privacy_link\">Privacy policy</a>.") !!}</p>
                              <form form wire:submit.prevent="_purchase">
                               @php
                                   $error = false;
                           
                                   if(!$errors->isEmpty()){
                                       $error = $errors->first();
                                   }
                               @endphp
                               @if ($error)
                               <div class="mb-5 bg-red-200 text-[11px] p-1 px-2 rounded-md">
                                       <div class="flex items-center">
                                           <div>
                                               <i class="fi fi-rr-cross-circle flex"></i>
                                           </div>
                                           <div class="flex-grow ml-1">{{ $error }}</div>
                                       </div>
                                   </div>
                               @endif
                               <button class="yena-button-stack --black !rounded-lg !shadow-none !border-none">
                                    <div class="--icon">
                                        <i class="ph ph-credit-card"></i>
                                    </div>
                    
                                    {{ __('Proceed') }}
                                </button>
                               </form>
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
