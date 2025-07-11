<div class="p-6 shadow-none mt-0 rounded-xl shadow-lg bg-white">
  <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center text-xl">
     <span class="whitespace-nowrap">{{ __('Smtp') }}</span>
  </div>
  <hr class="my-4">
  <div class="form-input">
    <label>{{ __('Mailer') }}</label>
    <select name="env[MAIL_MAILER]">
      <option value="smtp" {{ config('app.MAIL_MAILER') == 'smtp' ? 'checked' : '' }}>{{ __('smtp') }}</option>
    </select>
  </div>

  <div class="font-heading my-6 pr-2 text-zinc-400 flex items-center">
      <span class="whitespace-nowrap"><i class="fi fi-rr-settings"></i></span>
      <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
   </div>

  <div class="grid grid-cols-2 gap-4">
   <div class="form-input">
     <label>{{ __('Host') }}</label>
     <input type="text" name="env[MAIL_HOST]" value="{{ config('app.MAIL_HOST') }}">
   </div>
   <div class="form-input">
     <label>{{ __('From email') }}</label>
     <input type="text" name="env[MAIL_FROM_ADDRESS]" value="{{ config('app.MAIL_FROM_ADDRESS') }}">
   </div>
   <div class="form-input">
     <label>{{ __('From name') }}</label>
     <input type="text" name="env[MAIL_FROM_NAME]" value="{{ config('app.MAIL_FROM_NAME') ?? '${APP_NAME}' }}">
   </div>
   <div class="form-input">
     <label>{{ __('Port') }}</label>
     <input type="text" name="env[MAIL_PORT]" value="{{ config('app.MAIL_PORT') }}">
   </div>
   <div class="form-input">
     <label>{{ __('Username') }}</label>
     <input type="text" name="env[MAIL_USERNAME]" value="{{ config('app.MAIL_USERNAME') }}">
   </div>
   <div class="form-input">
     <label>{{ __('Password') }}</label>
     <input type="text" name="env[MAIL_PASSWORD]" value="{{ config('app.MAIL_PASSWORD') }}">
   </div>
 </div>
</div>