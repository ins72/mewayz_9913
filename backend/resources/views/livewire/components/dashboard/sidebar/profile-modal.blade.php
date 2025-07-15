
<?php
   use App\Livewire\Actions\ToastUp;
   use App\Models\Folder;
   use App\Models\FolderMember;
   use function Livewire\Volt\{state, mount, placeholder, uses, rules, updated, usesFileUploads};

   usesFileUploads();
   updated([
   'avatar' => function(){
      $this->dispatch('updatedAvatar', $this->avatar->temporaryUrl());
      $this->js('
         let avatars = document.querySelectorAll(".yena-update-avatar");

         avatars.forEach(function(e){
            //e.setAttribute("src", "'.$this->avatar->temporaryUrl().'");
            //console.log(e)
         });
      ');
   }]);

   state([
      'user' => fn() => iam()->get_original_user(),
      'avatar' => null,
   ]);

   rules(fn () => [
      'user.name' => 'required',
      'user.email' => 'required|email|unique:users,email,'.$this->user->id,
   ]);
   uses([ToastUp::class]);
   mount(fn() => '');

   $editUser = function(){
      $this->validate();
      // Upload avatar


      if(!empty($this->avatar)){
            $this->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:5048'
            ]);
            storageDelete('media/avatar', $this->user->avatar);


            $filesystem = sandy_filesystem('media/avatar');
            $avatar = $this->avatar->storePublicly('media/avatar', $filesystem);
            $avatar = str_replace("media/avatar/", "", $avatar);
            $this->user->avatar = $avatar;
      }

      $this->user->save();

      $this->flashToast('success', __('Changes saved successfully'));
   };

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
?>


<div class="w-full">
   <div class="flex flex-col">
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross text-sm"></i>
      </a>

      <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Profile') }}</header>

      <hr class="yena-divider">

      <form wire:submit="editUser" class="px-8 pt-2 pb-6">

         <div class="settings__upload" data-generic-preview>
            <div class="settings__preview">
               @php
                   $_avatar = iam()->get_original_user()->getAvatar();
                   
                   if($avatar) $_avatar = $avatar->temporaryUrl();
               @endphp
               <img src="{{ $_avatar }}" alt="">
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
      
         <div class="flex flex-col gap-6 mt-5">
            <x-input-x wire:model="user.name" label="{{ __('Full Name') }}"></x-input-x>
            <x-input-x wire:model="user.email" label="{{ __('Email address') }}"></x-input-x>
         </div>

         <button class="yena-button-stack mt-5 w-full">{{ __('Save') }}</button>
      </form>
   </div>
</div>