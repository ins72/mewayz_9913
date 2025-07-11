<?php
   use function Laravel\Folio\name;
   name('console-settings-account');

   use function Livewire\Volt\{state, mount, rules, usesFileUploads, uses};
   use App\Livewire\Actions\Logout;
   use App\Livewire\Actions\ToastUp;

   uses([ToastUp::class]);

   usesFileUploads();

   state([
      'user' => fn() => Auth::user(),
      'avatar' => null,
   ]);

   rules(fn () => [
      'user.name' => 'required',
      'user.email' => 'required|email|unique:users,email,'.$this->user->id,
   ]);
   
   mount(function(){
   
   });

   $editUser = function(){
      $this->validate();
      // Upload avatar


      $this->user->save();

      $this->flashToast('success', __('Changes saved successfully'));
   };
   
   $logout = function (Logout $logout) {
       $logout();
   
       $this->redirect(route('login'), navigate: true);
   };
?>
<x-layouts.app>
   <x-slot:title>{{ __('Account') }}</x-slot>

   @volt
   <div>

      <div class="mx-auto flex h-full w-full max-w-[1400px]">
         
         @include('_include.settings.settings-menu', ['current' => 'account'])

         <div class="flex flex-1 py-10 ps-[344px]">
            <form class="flex w-full flex-col" wire:submit="editUser">
               <div class="flex w-full flex-row items-center justify-between ">
                  <div class="text-lg font-semibold">{{ __('Account') }}</div>
                  <div class="flex flex-row gap-2">
                     <x-yena.primary-button type="submit">{{ __('Save Changes') }}</x-yena.primary-button>
                  </div>
               </div>
               <div class="mt-8 flex max-w-[600px] flex-col gap-8">
                  <div class="flex w-full flex-1 flex-col">
                     <div class="flex w-[200px] flex-row items-center justify-between">
                        <div>
                           <div class="bg-background-neutral-subtle flex h-16 w-16 items-center justify-center rounded-full">
                              <img class="w-full h-full rounded-full" data-src="{{ Auth::user()->getAvatar() }}" alt="">
                           </div>
                        </div>
                        <div class="flex flex-row items-center"><button class="z-0 group relative inline-flex items-center justify-center box-border appearance-none select-none whitespace-nowrap font-normal subpixel-antialiased overflow-hidden tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 px-unit-4 min-w-unit-20 h-unit-10 text-small gap-unit-2 rounded-medium [&amp;>svg]:max-w-[theme(spacing.unit-8)] data-[pressed=true]:scale-[0.97] transition-transform-colors motion-reduce:transition-none bg-primary text-primary-foreground" type="button">Upload</button></div>
                        <input id="avatar-select-input" type="file" class="hidden">
                     </div>
                  </div>
                  <x-input-x wire:model="user.name" label="{{ __('Full Name') }}"></x-input-x>
               
                  <x-input-x wire:model="user.email" label="{{ __('Email address') }}"></x-input-x>


                  <a class="z-0 group relative inline-flex items-center justify-center box-border appearance-none select-none whitespace-nowrap font-normal subpixel-antialiased overflow-hidden tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 px-unit-4 min-w-unit-20 h-unit-10 text-small gap-unit-2 rounded-medium [&amp;>svg]:max-w-[theme(spacing.unit-8)] data-[pressed=true]:scale-[0.97] transition-transform-colors motion-reduce:transition-none bg-default text-default-foreground w-fit cursor-pointer" wire:click="logout">
                     <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path>
                        <path d="M9 12h12l-3 -3"></path>
                        <path d="M18 15l3 -3"></path>
                     </svg>
                     {{ __('Logout') }}
                  </a>
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
                           <div class="flex-grow ml-1 text-xs">{{ $error }}</div>
                        </div>
                  </div>
               @endif
            </form>
         </div>
      </div>
      
   </div>
   


   @endvolt
</x-layouts.app>
