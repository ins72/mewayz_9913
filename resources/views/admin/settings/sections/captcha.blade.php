<div class="p-6 shadow-none mt-0 rounded-xl shadow-lg bg-white">
  <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center text-xl">
     <span class="whitespace-nowrap">{{ __('Captcha') }}</span>
  </div>
  <hr class="my-4">

   <div class="grid grid-cols-2 gap-4">
      <div class="form-input">
         <label>{{ __('Enable captcha') }}</label>
         <select name="settings[captcha][enable]">
           @foreach (['0' => 'Disable', '1' => 'Enable'] as $key => $value)
           <option value="{{ $key }}" {{ settings('captcha.enable') == $key ? 'selected' : '' }}>
             {{ __($value) }}
           </option>
           @endforeach
         </select>
       </div>
       <div class="form-input">
         <label>{{ __('Captcha type') }}</label>
         <select name="settings[captcha][type]">
           @foreach (['default' => 'Default', 'google_recaptcha' => 'Google Recaptcha'] as $key => $value)
           <option value="{{ $key }}" {{ settings('captcha.type') == $key ? 'selected' : '' }}>
             {{ __($value) }}
           </option>
           @endforeach
         </select>
       </div>
   </div>

    <div class="font-heading my-6 font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center">
        <span class="whitespace-nowrap">{{ __('Recaptcha') }}</span>
        <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
     </div>
    <div class="grid grid-cols-2 gap-4">
      <div class="form-input">
        <label>{{ __('Recaptcha site key') }}</label>
        <input type="text" name="env[RECAPTCHA_SITE_KEY]" value="{{ config('app.RECAPTCHA_SITE_KEY') }}">
      </div>
      <div class="form-input">
        <label>{{ __('Recaptcha secret key') }}</label>
        <input type="text" name="env[RECAPTCHA_SECRET_KEY]" value="{{ config('app.RECAPTCHA_SECRET_KEY') }}">
      </div>
    </div>
 </div>