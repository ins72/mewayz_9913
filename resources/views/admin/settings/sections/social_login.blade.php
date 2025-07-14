<div class="p-6 shadow-none mt-0 rounded-xl shadow-lg bg-white">
  <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center text-xl">
     <span class="whitespace-nowrap">{{ __('Facebook') }}</span>
  </div>
  <hr class="my-4">

  <div class="form-input mb-5">
   <label>{{ __('Enable') }}</label>
   <select name="env[FACEBOOK_ENABLE]">
     @foreach (['0' => 'Disable', '1' => 'Enable'] as $key => $value)
     <option value="{{ $key }}" {{ config('app.FACEBOOK_ENABLE') == $key ? 'selected' : '' }}>
       {{ __($value) }}
     </option>
     @endforeach
   </select>
 </div>
  <div class="grid grid-cols-2 gap-4">
   <div class="form-input">
     <label>{{ __('App Id') }}</label>
     <input type="text" name="env[FACEBOOK_CLIENT_ID]" value="{{ config('app.FACEBOOK_CLIENT_ID') }}">
   </div>
   <div class="form-input">
     <label>{{ __('Secret Id') }}</label>
     <input type="text" name="env[FACEBOOK_SECRET]" value="{{ config('app.FACEBOOK_SECRET') }}">
   </div>
   <div class="form-input col-span-2">
     <label>{{ __('Callback') }}</label>
     <input type="text" disabled="" class="opacity-40" value="{{ route('auth.driver.callback', 'facebook') }}">
   </div>
  </div>
</div>

<div class="p-6 shadow-none rounded-xl shadow-lg bg-white mt-4">
  <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center text-xl">
     <span class="whitespace-nowrap">{{ __('Google') }}</span>
  </div>
  <hr class="my-4">
 
   <div class="form-input mb-5">
      <label>{{ __('Enable') }}</label>
      <select name="env[GOOGLE_ENABLE]">
        @foreach (['0' => 'Disable', '1' => 'Enable'] as $key => $value)
        <option value="{{ $key }}" {{ config('app.GOOGLE_ENABLE') == $key ? 'selected' : '' }}>
          {{ __($value) }}
        </option>
        @endforeach
      </select>
    </div>
   <div class="grid grid-cols-2 gap-4">


      <div class="form-input">
         <label>{{ __('Client Id') }}</label>
         <input type="text" name="env[GOOGLE_CLIENT_ID]" value="{{ config('app.GOOGLE_CLIENT_ID') }}">
       </div>
       <div class="form-input">
         <label>{{ __('Secret Id') }}</label>
         <input type="text" name="env[GOOGLE_SECRET]" value="{{ config('app.GOOGLE_SECRET') }}">
       </div>
       <div class="form-input col-span-2">
         <label>{{ __('Callback') }}</label>
         <input type="text" disabled="" class="opacity-40" value="{{ route('auth.driver.callback', 'google') }}">
       </div>
   </div>
 </div>