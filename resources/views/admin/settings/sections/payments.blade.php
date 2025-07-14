<div class="p-6 shadow-none mt-0 rounded-xl shadow-lg bg-white">
   <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center text-xl">
      <span class="whitespace-nowrap">{{ __('Payments') }}</span>
   </div>
   <hr class="my-4">
  <div class="grid grid-cols-2 gap-4">

   <div class="form-input">
      <label>{{ __('Currency') }}</label>
      <select name="settings[payment][currency]">
      @foreach (Currency::all() as $key => $value)
         <option value="{{ $key }}" {{ settings('payment.currency') == $key ? 'selected' : '' }}>
      {!! $key !!}
      </option>
      @endforeach
      </select>
   </div>
  </div>
  <div>
    <p class="mt-2 text-xs text-gray-600 flex items-center gap-1"><i class="fi fi-rr-triangle-warning"></i> {{ __('Please select the currency that works with your current payment method & will be used to purchase plans.') }}</p>
  </div>
  
  <div class="flex flex-col gap-4 mt-5">
     
     <div class="yena-accordion !bg-[var(--yena-colors-gray-50)]">
        
     @foreach ($payments as $key => $item)
     <div class="-accordion-item" x-data="{ expanded: false }" :class="{'-active': expanded}">
         <button class="-accordion-button" @click="expanded = ! expanded" type="button">
             
             <div class="flex flex-row gap-3 w-full">
                 <div class="inline text-sm text-[var(--yena-colors-purple-300)] h-8 w-8 bg-white rounded-full">
                    <img src="{{ gs('assets/image/payments', ao($item, 'thumbnail')) }}" alt="{{ ao($item, 'name') }}" class="h-full w-full rounded-full">
                 </div>
                 <div class="flex flex-col gap-0 text-left">
                     <div class="font-bold">{{ ao($item, 'name') }}</div>
                 </div>
                 <div class="flex-[1] justify-self-stretch self-stretch"></div>
                 {!! __i('Arrows, Diagrams', 'Arrow.2', '-open-icon') !!}
             </div>
         </button>
         <div x-show="expanded" x-collapse>
             <div class="-accordion-panel">
                 <div class="p-6 shadow-none mt-0 rounded-xl shadow-lg bg-white">
                    <div class="font-heading mb-2 font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center mb-5 text-xl">
                       <span class="whitespace-nowrap">
                          <img src="{{ gs('assets/image/payments', ao($item, 'thumbnail')) }}" alt="{{ ao($item, 'name') }}" class="h-8">
                       </span>
                    </div>
                    
                    <div>
                       <div class="flex items-center gap-4 py-4">
                          <div class="mt-1 self-start">
                             <i class="fi fi-rr-eye-crossed text-lg"></i>
                          </div>
                          <div>
                             <div>{{ __('Enable') }} {{ ao($item, 'name') }}</div>
                          </div>
                          <div class="flex-grow"></div>
              
                          @php
                             $checked = settings("payment_$key.status");
                          @endphp
                          <x-input.checkbox name="settings[payment_{{ $key }}][status]" value="1" checked="{{ $checked }}" ></x-input.checkbox> 
              
                       </div>
              
                       <div class="form-input">
                          <label>{{ __('Payment Description') }}</label>
                          <input type="text" name="settings[payment_{{ $key }}][description]" value="{{ settings("payment_$key.description") }}">
                       </div>
                    </div>
                    <div class="font-heading my-6 pr-2 text-zinc-400 flex items-center">
                       <span class="whitespace-nowrap"><i class="fi fi-rr-settings"></i></span>
                       <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
                    </div>
              
                    <div>
                       @includeIf("payment:$key::admin.edit")
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                    
                    </div>
                 </div>
             </div>
         </div>
     </div>
     @endforeach
    </div>
  </div>
</div>