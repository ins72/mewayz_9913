<div class="p-6 shadow-none mt-0 rounded-xl shadow-lg bg-white">
  <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center text-xl">
     <span class="whitespace-nowrap">{{ __('Basic') }}</span>
  </div>
  <hr class="my-4">
  <div class="grid grid-cols-2 gap-4">
     <div class="form-input">
        <label>{{ __('App Name') }}</label>
        <input type="text" name="env[APP_NAME]" value="{{ config('app.name') }}">
     </div>
     <div class="form-input">
        <label>{{ __('App Email') }}</label>
        <input type="email" name="env[APP_EMAIL]" value="{{ config('app.APP_EMAIL') }}">
     </div>
     <div class="form-input col-span-2">
        <label>{{ __('Help Center Url') }}</label>
        <input type="text" name="env[HELPCENTER_URL]" value="{{ config('app.HELPCENTER_URL') }}">
     </div>
     <div class="form-input">
      <label>{{ __('Terms Url') }}</label>
      <input type="text" name="settings[others][terms]" value="{{ settings('others.terms') }}">
   </div>
   <div class="form-input">
      <label>{{ __('Privacy Url') }}</label>
      <input type="text" name="settings[others][privacy]" value="{{ settings('others.privacy') }}">
   </div>
  </div>
</div>

<div class="p-6 shadow-none rounded-xl shadow-lg bg-white mt-5">
   <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center text-xl">
      <span class="whitespace-nowrap">{{ __('Users') }}</span>
   </div>
   <hr class="my-4">

   <div class="grid grid-cols-2 gap-4">
      <div class="form-input">
         <label class="initial">{{ __('Enable registration') }}</label>
         <select name="env[enable_registration]">
            @foreach (['1' => 'Enable', '0' => 'Disable'] as $key => $value)
            <option value="{{ $key }}" {{ config('app.enable_registration') == $key ? 'selected' : '' }}>
            {{ __($value) }}
            </option>
            @endforeach
         </select>
      </div>
      <div class="form-input">
         <label class="initial">{{ __('Enable email verification') }}</label>
         <select name="env[email_verification]">
         @foreach (['1' => 'Enable', '0' => 'Disable'] as $key => $value)
         <option value="{{ $key }}" {{ config('app.email_verification') == $key ? 'selected' : '' }}>
         {{ __($value) }}
         </option>
         @endforeach
         </select>
      </div>
   </div>
</div>

<div class="p-6 shadow-none rounded-xl shadow-lg bg-white mt-5">
   <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center text-xl">
      <span class="whitespace-nowrap">{{ __('Wallet') }}</span>
   </div>
   <hr class="my-4">
   <div class="grid grid-cols-2 gap-4">
      <div>
         <div class="font-heading tracking-wider text-zinc-400 flex items-center text-sm">
            <span class="whitespace-nowrap">{{ __('Wallet Default Method') }}</span>
         </div>
         <hr class="my-4">
         <div>
            <div class="form-input">
               <select name="env[WALLET_METHOD]">
                  @foreach ($payments as $key => $item)
                      @if (settings("payment_$key.status"))
                      <option value="{{ $key }}" {{ config('app.wallet.defaultMethod') == $key ? 'selected' : '' }}>
                        {{ ao($item, 'name') }}
                      </option>
                      @endif
                  @endforeach
               </select>
            </div>
         </div>
      </div>
      <div>
         <div class="font-heading tracking-wider text-zinc-400 flex items-center text-sm">
            <span class="whitespace-nowrap">{{ __('Wallet Currency') }}</span>
         </div>
         <hr class="my-4">
         <div>
            <div class="form-input">
               <select name="env[WALLET_CURRENCY]">
                  @foreach (Currency::all() as $key => $value)
                     <option value="{{ $key }}" {{ config('app.wallet.currency') == $key ? 'selected' : '' }}>
                     {!! $key !!}
                     </option>
                  @endforeach
               </select>
            </div>
         </div>
      </div>
      <div>
         <div class="font-heading tracking-wider text-zinc-400 flex items-center text-sm">
            <span class="whitespace-nowrap">{{ __('Percentage per transaction') }}</span>
         </div>
         <hr class="my-4">
         <div>
            <div class="form-input">
               <input type="number" name="env[WALLET_PERCENTAGE]" value="{{ config('app.wallet.percentage') }}">
            </div>
         </div>
      </div>      <div>
         <div class="font-heading tracking-wider text-zinc-400 flex items-center text-sm">
            <span class="whitespace-nowrap">{{ __('Percentage per withdrawal') }}</span>
         </div>
         <hr class="my-4">
         <div>
            <div class="form-input">
               <input type="number" name="env[WALLET_WITHDRAW_PERCENTAGE]" value="{{ config('app.wallet.withdraw_percentage') }}">
            </div>
         </div>
      </div>
   </div>
</div>
<div class="p-6 shadow-none rounded-xl shadow-lg bg-white mt-5">
   <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center text-xl">
      <span class="whitespace-nowrap">{{ __('Api') }}</span>
   </div>
   <hr class="my-4">

   <div class="grid grid-cols-2 gap-4">
      <div>
         <div class="font-heading tracking-wider text-zinc-400 flex items-center text-sm">
            <span class="whitespace-nowrap">{{ __('OpenAi') }}</span>
         </div>
         <hr class="my-4">
         <div>
            <div class="form-input">
               <input type="text" name="env[OPENAI_KEY]" value="{{ config('app.openai_key') }}">
            </div>
         </div>
      </div>
      <div>
         <div class="font-heading tracking-wider text-zinc-400 flex items-center text-sm">
            <span class="whitespace-nowrap">{{ __('Unsplash') }}</span>
         </div>
         <hr class="my-4">
         <div>
            <div class="form-input">
               <input type="text" name="env[UNSPLASH_ACCESS_KEY]" value="{{ config('unsplash.access_key') }}">
            </div>
         </div>
      </div>
      <div>
         <div class="font-heading tracking-wider text-zinc-400 flex items-center text-sm">
            <span class="whitespace-nowrap">{{ __('Pexels') }}</span>
         </div>
         <hr class="my-4">
         <div>
            <div class="form-input">
               <input type="text" name="env[PEXELS_API_KEY]" value="{{ config('pexels.api_key') }}">
            </div>
         </div>
      </div>
   </div>
</div>

<div class="p-6 shadow-none rounded-xl shadow-lg bg-white mt-5">
   <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center text-xl">
      <span class="whitespace-nowrap">{{ __('Images') }}</span>
   </div>
   <hr class="my-4">

  <div class="grid grid-cols-2 gap-4">
   <div>
      <div class="font-heading tracking-wider text-zinc-400 flex items-center text-sm">
         <span class="whitespace-nowrap">{{ __('App Logo') }}</span>
      </div>
      <hr class="my-4">
      <div x-data="__image('{{ logo() }}')">
         <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed text-center hover:border-solid hover:border-yellow-600 relative border-gray-200">
               <input type="file" @change="selectFile" class="absolute h-full w-full right-0 bottom-0 z-50 opacity-0" name="logo">
               <div class="w-full h-full flex items-center justify-center relative z-40">
                   <div class="w-6 h-6 flex items-center justify-center rounded-lg bg-white">
                     <i class="fi fi-ss-plus text-xs"></i>
                   </div>
               </div>
               
               <template x-if="imageUrl">
                  <div class="absolute h-full w-full right-0 bottom-0 z-20">
                     <img :src="imageUrl" class="h-full w-[100%] object-contain rounded-md" alt="">
                  </div>
               </template>
            </div>
      </div>
   </div>
   <div>
      <div class="font-heading tracking-wider text-zinc-400 flex items-center text-sm">
         <span class="whitespace-nowrap">{{ __('Logo Icon') }}</span>
      </div>
      <hr class="my-4">
      <div x-data="__image('{{ logo_icon() }}')">
         <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed text-center hover:border-solid hover:border-yellow-600 relative border-gray-200">
               <input type="file" @change="selectFile" class="absolute h-full w-full right-0 bottom-0 z-50 opacity-0" name="logo_icon">
               <div class="w-full h-full flex items-center justify-center relative z-40">
                   <div class="w-6 h-6 flex items-center justify-center rounded-lg bg-white">
                     <i class="fi fi-ss-plus text-xs"></i>
                   </div>
               </div>
               
               <template x-if="imageUrl">
                  <div class="absolute h-full w-full right-0 bottom-0 z-20">
                     <img :src="imageUrl" class="h-full w-[100%] object-contain rounded-md" alt="">
                  </div>
               </template>
            </div>
      </div>
   </div>
   <div>
      <div class="font-heading tracking-wider text-zinc-400 flex items-center text-sm">
         <span class="whitespace-nowrap">{{ __('App Favicon') }}</span>
      </div>
      <hr class="my-4">
      <div x-data="__image('{{ favicon() }}')">
         <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed text-center hover:border-solid hover:border-yellow-600 relative border-gray-200">
               <input type="file" @change="selectFile" class="absolute h-full w-full right-0 bottom-0 z-50 opacity-0" name="favicon">
               <div class="w-full h-full flex items-center justify-center relative z-40">
                   <div class="w-6 h-6 flex items-center justify-center rounded-lg bg-white">
                     <i class="fi fi-ss-plus text-xs"></i>
                   </div>
               </div>
               
               <template x-if="imageUrl">
                  <div class="absolute h-full w-full right-0 bottom-0 z-20">
                     <img :src="imageUrl" class="h-full w-[100%] object-contain rounded-md" alt="">
                  </div>
               </template>
            </div>
      </div>
   </div>
   <div>
      <div class="font-heading tracking-wider text-zinc-400 flex items-center text-sm">
         <span class="whitespace-nowrap">{{ __('Login Image') }}</span>
      </div>
      <hr class="my-4">
      <div x-data="__image('{{ login_image() }}')">
         <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed text-center hover:border-solid hover:border-yellow-600 relative border-gray-200">
               <input type="file" @change="selectFile" class="absolute h-full w-full right-0 bottom-0 z-50 opacity-0" name="login_logo">
               <div class="w-full h-full flex items-center justify-center relative z-40">
                   <div class="w-6 h-6 flex items-center justify-center rounded-lg bg-white">
                     <i class="fi fi-ss-plus text-xs"></i>
                   </div>
               </div>
               
               <template x-if="imageUrl">
                  <div class="absolute h-full w-full right-0 bottom-0 z-20">
                     <img :src="imageUrl" class="h-full w-[100%] object-contain rounded-md" alt="">
                  </div>
               </template>
            </div>
      </div>
   </div>
 </div>

 <div class=" mt-4">
    <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center text-xl">
       <span class="whitespace-nowrap">{{ __('Branding') }}</span>
    </div>
    <hr class="my-4">
 </div>
 
 <div class="grid grid-cols-2 gap-4">
   <div>
      <div class="font-heading tracking-wider text-zinc-400 flex items-center text-sm">
         <span class="whitespace-nowrap">{{ __('Light Logo') }}</span>
      </div>
      <hr class="my-4">
      <div x-data="__image('{{ logo_branding('light') }}')">
         <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed text-center hover:border-solid hover:border-yellow-600 relative border-gray-200">
               <input type="file" @change="selectFile" class="absolute h-full w-full right-0 bottom-0 z-50 opacity-0" name="branding_light">
               <div class="w-full h-full flex items-center justify-center relative z-40">
                   <div class="w-6 h-6 flex items-center justify-center rounded-lg bg-white">
                     <i class="fi fi-ss-plus text-xs"></i>
                   </div>
               </div>
               
               <template x-if="imageUrl">
                  <div class="absolute h-full w-full right-0 bottom-0 z-20">
                     <img :src="imageUrl" class="h-full w-[100%] object-contain rounded-md" alt="">
                  </div>
               </template>
            </div>
      </div>
   </div>
   <div>
      <div class="font-heading tracking-wider text-zinc-400 flex items-center text-sm">
         <span class="whitespace-nowrap">{{ __('Dark Logo') }}</span>
      </div>
      <hr class="my-4">
      <div x-data="__image('{{ logo_branding('dark') }}')">
         <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed text-center hover:border-solid hover:border-yellow-600 relative border-gray-200">
               <input type="file" @change="selectFile" class="absolute h-full w-full right-0 bottom-0 z-50 opacity-0" name="branding_dark">
               <div class="w-full h-full flex items-center justify-center relative z-40">
                   <div class="w-6 h-6 flex items-center justify-center rounded-lg bg-white">
                     <i class="fi fi-ss-plus text-xs"></i>
                   </div>
               </div>
               
               <template x-if="imageUrl">
                  <div class="absolute h-full w-full right-0 bottom-0 z-20">
                     <img :src="imageUrl" class="h-full w-[100%] object-contain rounded-md" alt="">
                  </div>
               </template>
            </div>
      </div>
   </div>
 </div>
</div>

<div class="p-6 shadow-none rounded-xl shadow-lg bg-white mt-5">
   <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center text-xl">
      <span class="whitespace-nowrap">{{ __('Notifications') }}</span>
   </div>
   <hr class="my-4">


  <div class="grid grid-cols-2 gap-4">
    <div class="form-input">
       <label>{{ __('Email\'s') }}</label>
       <textarea name="settings[notification][emails]" cols="30" rows="3">{{ settings('notification.emails') }}</textarea>
    </div>
  </div>
  <div>
    <p class="mt-2 text-xs text-gray-600 flex items-center gap-1"><i class="fi fi-rr-triangle-warning"></i> {{ __('Add valid email addresses separated by a comma.') }}</p>
  </div>
</div>