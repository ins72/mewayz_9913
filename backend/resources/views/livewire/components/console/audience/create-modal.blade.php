
<?php
   use App\Yena\SandyAudience;
   use App\Livewire\Actions\ToastUp;
   use App\Models\Audience;
   use function Livewire\Volt\{state, mount, placeholder, uses, rules, updated, usesFileUploads};

   usesFileUploads();

   state([
      'avatar' => null,
      'countries' => fn() => \App\Yena\Country::list(),

      'create' => [
         'name' => '',
         'email' => '',
         'lastCountry' => '',
         'phone_number' => '',

         'settings' => [
            'job_title' => '',
            'location' => '',
         ]
      ]
   ]);

   rules(fn () => [
      'create.name' => 'required',
      'create.email' => 'required|email',
   ]);
   uses([ToastUp::class]);
   mount(fn() => '');

   placeholder('
   <div class="p-5 w-full mt-1">
      <div class="flex mb-2 gap-4">
         <div>
            <div class="--placeholder-skeleton w-[200px] h-[200px] rounded-3xl"></div>
         </div>
         <div class="flex flex-col gap-2 w-full">
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-[150px] h-[40px] rounded-full mt-5"></div>
         </div>
      </div>
      
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');


   $createAudience = function(){
      $this->validate();
      $create = $this->create;
      $a = new Audience;

      if(!empty($this->avatar)){
         $this->validate([
             'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:5048'
         ]);


         $filesystem = sandy_filesystem('media/audience/avatar');
         $avatar = $this->avatar->storePublicly('media/audience/avatar', $filesystem);
         $avatar = str_replace("media/audience/avatar/", "", $avatar);
         $a->avatar = $avatar;
      }

      $extra = [
          'created_by' => 1
      ];

      // $a->user = iam()->id;
      $a->owner_id = iam()->id;
      $a->contact = $this->create;
      $a->extra = $extra;
      $a->save();

      SandyAudience::create_activity(iam()->id, $a->id, __('Created'), __('Audience created successfully.'));

      
      $this->flashToast('success', __('Audience created'));
      $this->dispatch('close');

      $this->dispatch('audienceRefresh');
   };
?>


<div class="w-full">
   <div class="flex flex-col" x-data="audience_create">
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross text-sm"></i>
      </a>

      <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Audience') }}</header>

      <hr class="yena-divider">

      <form wire:submit="createAudience" class="px-8 pt-2 pb-6">

         <div class="settings__upload" data-generic-preview>
            <div class="settings__preview !bg-[#f3f3f3] flex items-center justify-center overflow-hidden">
               @php
                   $_avatar = false;
                   
                   if($avatar) $_avatar = $avatar->temporaryUrl();
               @endphp

               @if (!$_avatar)
                  {!! __i('--ie', 'image-picture', 'text-gray-300 w-8 h-8') !!}
               @endif
               @if ($_avatar)
                  <img src="{{ $_avatar }}" alt="">
               @endif
               <div wire:loading.class.remove="!hidden" wire:target="avatar" class="absolute w-full h-full flex items-center justify-center bg-[#00000063] !hidden">
                  <div class="loader-animation-container flex items-center justify-center"><div class="inner-circles-loader !w-7 !h-7"></div></div>
               </div>
            </div>
            <div class="settings__wrap">
              <div class="text-[2rem] leading-10 font-bold">{{ __('Profile photo') }}</div>
              <div class="settings__content">{{ __('We recommended an image of at least 80x80. Gifs work too.') }}</div>
              <div class="settings__file">
               <input class="settings__input z-50" type="file" wire:model="avatar">
               <a class="yena-button-stack">{{ __('Choose') }}</a>
              </div>
            </div>
         </div>
         <div class="form-input mt-4">
             <label>{{ __('First & Last Name') }}</label>
             <input type="text" name="name" wire:model="create.name">
         </div>
         <div class="form-input mt-2">
             <label>{{ __('Email') }}</label>
             <input type="email" name="email" wire:model="create.email">
         </div>
         <div class="form-input is-link always-active active mt-2">
             <label>{{ __('Phone Number') }}</label>
             <div class="is-link-inner">
                 <div class="side-info pl-1 relative">
                     <img :src="country()" class="w-12 h-10 pl-2 flag-img-tag" alt="">
                     <select name="lastCountry" wire:model="create.lastCountry" class="p-0 pl-0 w-14 absolute opacity-0 inset-0 cursor-pointer">
                        <template x-for="(item, index) in countries" :key="index">
                           <option :value="index" x-text="item"></option>
                        </template>
                     </select>
                 </div>
                 <input type="text" wire:model="create.phone_number" name="phone_number"
                     placeholder="{{ __('Enter a phone number') }}"
                     class="is-alt-input bg-white">
             </div>
         </div>

         <div class="form-input mt-2">
             <label>{{ __('Job Title') }}</label>
             <input type="text" wire:model="create.settings.job_title" name="settings[job_title]">
         </div>

         <div class="form-input mt-2">
             <label>{{ __('Location') }}</label>
             <input type="text" wire:model="create.settings.location" name="settings[location]" placeholder="{{ __('Enter a location (Ex: Alabama, USA)') }}">
         </div>

         @php
            $error = false;

            if(!$errors->isEmpty()){
                  $error = $errors->first();
            }

            if(Session::get('error._error')){
                  $error = Session::get('error._error');
            }
         @endphp
         @if ($error)
            <div class="mt-5 bg-red-200 font--11 p-1 px-2 rounded-md">
                  <div class="flex items-center">
                     <div>
                        <i class="fi fi-rr-cross-circle flex text-xs"></i>
                     </div>
                     <div class="flex-grow ml-1 text-xs">{{ str_replace('create.', '', $error) }}</div>
                  </div>
            </div>
         @endif
         <button class="yena-button-stack mt-5 w-full">{{ __('Save') }}</button>
      </form>
   </div>
   @script
   <script>
       Alpine.data('audience_create', () => {
          return {
            countries: @entangle('countries'),
            create: @entangle('create').live,

            country(){
               let country = 'AF';

               if(this.create.lastCountry){
                  country = this.create.lastCountry;
               }

               let flag = country.toLowerCase();
               var src = "{{ gs('assets/image/countries/') }}/" + flag + '.svg';
               return src;
            },
            init(){
               
            }
          }
       });
   </script>
   @endscript
</div>