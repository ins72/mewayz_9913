<div class="p-6 shadow-none border border-gray-300 mt-0 rounded-none">
  <div class="font-heading mb-2 font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center mb-5">
     <span class="whitespace-nowrap">{{ __('Basic') }}</span>
     <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
  </div>
  <div class="grid grid-cols-2 gap-4">
     <div class="form-input">
        <label>{{ __('App Name') }}</label>
        <input type="text" name="env[APP_NAME]" value="{{ config('app.name') }}">
     </div>
     <div class="form-input">
        <label>{{ __('App Email') }}</label>
        <input type="email" name="env[APP_EMAIL]" value="{{ config('app.APP_EMAIL') }}">
     </div>
  </div>
</div>

<div class="p-6 shadow-none border border-gray-300 mt-0 rounded-none mt-5">
  <div class="font-heading mb-2 font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center mb-5">
     <span class="whitespace-nowrap">{{ __('Users') }}</span>
     <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div class="form-input">
       <label class="initial">{{ __('Enable registration') }}</label>
       <select name="settings[user][enable_registration]">
       @foreach (['1' => 'Enable', '0' => 'Disable'] as $key => $value)
       <option value="{{ $key }}" {{ settings('user.enable_registration') == $key ? 'selected' : '' }}>
       {{ __($value) }}
       </option>
       @endforeach
       </select>
    </div>
    <div class="form-input">
       <label class="initial">{{ __('Enable email verification') }}</label>
       <select name="settings[user][email_verification]">
       @foreach (['1' => 'Enable', '0' => 'Disable'] as $key => $value)
       <option value="{{ $key }}" {{ settings('user.email_verification') == $key ? 'selected' : '' }}>
       {{ __($value) }}
       </option>
       @endforeach
       </select>
    </div>
 </div>
</div>


<div class="p-6 shadow-none border border-gray-300 mt-0 rounded-none mt-5">
  <div class="font-heading mb-2 font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center mb-5">
     <span class="whitespace-nowrap">{{ __('Logos') }}</span>
     <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
  </div>

  <div class="grid grid-cols-2 gap-4">

    <div class="wj-image-selector-w is-avatar relative is-sandy-upload-modal active duration-100 hover:opacity-80 rounded-full">
      <a class="wj-image-selector-trigger flex items-center relative rounded-lg border-2 p-1 m-0">
         <div class="wj-image-container inline-flex items-center justify-center bg-transparent">
  
                     
            <img src="{{ logo() }}" alt="" class="bg-transparent-all object-contain w-full h-full">
         </div>
         <div class="wj-image-selector-text ml-3 flex flex-col">
            <span class="wj-text-holder text-sm text-black">{{ __('App Logo') }}</span>
            <span class="font--8 font-bold uppercase text-gray-600 mt-1">{{ __('2mb Max') }}</span>
         </div>
      </a>
      <input type="file" name="logo">
   </div>
   <div class="wj-image-selector-w is-avatar relative is-sandy-upload-modal active duration-100 hover:opacity-80 rounded-full">
     <a class="wj-image-selector-trigger flex items-center relative rounded-lg border-2 p-1 m-0">
        <div class="wj-image-container inline-flex items-center justify-center bg-transparent">
 
                    
           <img src="{{ favicon() }}" alt="" class="bg-transparent-all object-contain w-full h-full">
        </div>
        <div class="wj-image-selector-text ml-3 flex flex-col">
           <span class="wj-text-holder text-sm text-black">{{ __('App Logo') }}</span>
           <span class="font--8 font-bold uppercase text-gray-600 mt-1">{{ __('2mb Max') }}</span>
        </div>
     </a>
     <input type="file" name="favicon">
  </div>
 </div>
</div>

<div class="p-6 shadow-none border border-gray-300 mt-0 rounded-none mt-5">
  <div class="font-heading mb-2 font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center mb-5">
     <span class="whitespace-nowrap">{{ __('Notifications') }}</span>
     <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
  </div>


  <div class="grid grid-cols-2 gap-4">
    <div class="form-input">
       <label>{{ __('Email\'s') }}</label>
       <textarea name="settings[notification][emails]" cols="30" rows="3">{{ settings('notification.emails') }}</textarea>
    </div>
    <div class="flex flex-col gap-3">
       @foreach (['user' => 'New user', 'plan' => 'New Plan Activation', 'pending_plan' => 'New Manual Plan Request'] as $key => $value)
       <div class="flex items-center gap-2 ">
          <input name="settings[notification][{{$key}}]" type="hidden" value="0">
          <input name="settings[notification][{{$key}}]" type="checkbox" value="1" {{ settings("notification.$key") ? 'checked' : '' }} type="checkbox" class="checked:bg-primary checked:focus:bg-primary-400 hover:checked:bg-primary-400 block flex-none appearance-none rounded-md border-gray-300 bg-contain bg-center bg-no-repeat text-sm shadow-sm focus:outline-none focus:ring-0 focus:ring-transparent disabled:cursor-not-allowed"> 
          <div class="flex-grow">
             <div class="block text-sm font-medium text-gray-800">
                <div class="flex items-center gap-2">
                  {{ __($value) }}
                   <div></div>
                </div>
             </div>
          </div>
       </div>
       @endforeach
    </div>
  </div>
  <div>
    <p class="mt-2 text-xs text-gray-600 flex items-center gap-1"><i class="fi fi-rr-triangle-warning"></i> {{ __('Add valid email addresses separated by a comma.') }}</p>
  </div>
</div>

<!-- General.Payment:START -->
<div class="popup__label">{{ __('Payment') }}</div>
<div class="mort-main-bg rounded-2xl p-5 grid grid-cols-2 gap-4 mb-10">
  <div class="form-input hidden">
     <label class="initial">{{ __('Enable payment') }}</label>
     <select name="settings[payment][enable]" class="bg-w">
     @foreach (['1' => 'Enable', '0' => 'Disable'] as $key => $value)
     <option value="{{ $key }}" {{ settings('payment.enable') == $key ? 'selected' : '' }}>
     {{ __($value) }}
     </option>
     @endforeach
     </select>
     <p class="text-xs mt-4">{{ __('Enable or disable wide payment support on this site. Users cant purchase plans nor can they use payment method\'s or collect payment') }}</p>
  </div>
  <div class="form-input">
     <label class="initial">{{ __('Currency') }}</label>
     <select name="settings[payment][currency]" class="bg-w">
     @foreach (/*Currency::all()*/[] as $key => $value)
     <option value="{{ $key }}" {{ settings('payment.currency') == $key ? 'selected' : '' }}>
     {!! $key !!}
     </option>
     @endforeach
     </select>
     <p class="text-xs mt-4">{{ __('Please select the currency that works with your current payment method & will be used to purchase plans') }}</p>
  </div>
</div>
<!-- General.Payment:END -->