<div>
    @section('title', __('Activate Free'))
    <style>
        #-page{
            padding: 0 !important;
        }
    </style>
    @php
        $price = 0;
    @endphp
    <form  wire:submit.prevent="_activate_free"class="flex flex-col">
        <div class="flex-grow duration-100 max-w-[464px] mx-auto w-full">
           <div>
            <div class="w-full bg-white overflow-hidden rounded-lg [box-shadow:var(--yena-shadows-base)] px-0 py-12 lg:py-20 lg:pt-20">
                 <div class="mx-auto max-w-md px-8 lg:mx-0 border-b border-solid border-gray-300 pb-5">
                    <div class="font-heading mb-2 px-0 font--12 font-extrabold upper-case tracking-wider flex items-center mb-2">
                       <h1 class="text-2xl font-bold leading-normal sm:leading-normal whitespace-nowrap font--caveat">{{ __('Return to Free') }}</h1>
                       <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">{{ __('You are about to activate our free plan.') }}</p>
                 </div>
                 <div class="w-full px-8 mt-5">
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
                    
                    <p class="mt-2 mb-5 font--11 color-gray lg:hidden">{!! __t("By proceeding, I agree to the <a href=\"$terms_link\">Terms of service</a> and <a href=\"$privacy_link\">Privacy policy</a>.") !!}</p>
                    <div class="hidden lg:block">
                       <div class="mt-8 flex justify-between hidden">
                          <div>
                             <div>{{ $this->plan->name }} - {{ $duration == 'month' ? __('Monthly') : __('Yearly') }}</div>
                             <div class="text-sm text-black text-opacity-70">{{ __('Billed') }} {{ $duration == 'month' ? __('Monthly') : __('Yearly') }}</div>
                          </div>
                          <div class="">{!! price_with_cur(\Currency::symbol(settings('payment.currency')), $price) !!}</div>
                       </div>
                       <hr class="mt-8 border-white/20">
                       <div class="mt-8 flex justify-between">
                          <div>
                             <div>{{ __('Total') }}</div>
                          </div>
                          <div class="">{!! price_with_cur(\Currency::symbol(settings('payment.currency')), $price) !!}</div>
                       </div>
                       <div class="mt-10 flex items-center gap-1 text-sm" style="">


                         <div class="font--11 color-gray mb-5">
                             @php
                                 $terms_link = settings('others.terms');
                                 $privacy_link = settings('others.privacy');
                             @endphp
                             {!! __t("By proceeding, I agree to the <a href=\"$terms_link\">Terms of service</a> and <a href=\"$privacy_link\">Privacy policy</a>.") !!}
                               
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
                    <div class="mb-5 bg-red-200 font--11 p-1 px-2 rounded-md">
                            <div class="flex items-center">
                                <div>
                                    <i class="fi fi-rr-cross-circle flex"></i>
                                </div>
                                <div class="flex-grow ml-1">{{ $error }}</div>
                            </div>
                        </div>
                    @endif
                    @if (iam()->activeSubscription() && iam()->activeSubscription()->plan && iam()->activeSubscription()->plan->id == $this->plan->id)
                        
                    <div>
                        <p class="mt-2 text-xs text-gray-600 flex items-center gap-1"><i class="fi fi-rr-triangle-warning"></i> {{ __('You already subscribed to this plan.') }}</p>
                    </div>
                    @else
                    <button class="yena-button-stack --black !rounded-lg !shadow-none !border-none">
                         {{ __('Proceed') }}
                     </button>
                    @endif
                 </div>
                 
              </div>
           </div>
        </div>
    </form>
</div>