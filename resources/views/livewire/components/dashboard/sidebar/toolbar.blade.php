
<?php
   use function Livewire\Volt\{state, mount, placeholder, on};


   state([
      'avatar' => fn() => iam()->getAvatar(),
   ]);
   on([
      'updatedAvatar' => function($avatar = null) {
         $this->avatar = $avatar;
      },
   ]);

   mount(fn() => '');

   placeholder('placeholders.console.toolbar-placeholder');
?>

<div class="yena-topbar">
   <header>
      <div class="logo">
         <a href="/" name="">
            <img src="{{ logo() }}" class="h-16 w-16 object-contain" alt=" " width="36" class="block">
         </a>
      </div>
      <nav>
         <ul>
            <li>
               <x-x.href href="{{ route('dashboard-sites-index') }}">
                  <span>
                     {!! __icon('interface-essential', 'browser-internet-web-network-window-app-icon') !!}
                  </span>
                  {{ __('Sites') }} 
               </x-x.href>
            </li>
            <li>
               <x-x.href href="{{ route('dashboard-trash-index') }}">
                  <span>
                     {!! __icon('interface-essential', 'trash-bin-delete') !!}
                  </span>
                  {{ __('Trash') }} 
               </x-x.href>
            </li>
            <!---->
         </ul>
      </nav>
      <div class="profile">
         <div class="profile-picture-holder">
            {{-- <ul class="themes">
               <li class="dark-mode theme-mode">
                  <a href="javascript:void(0)" name="dark-theme">
                     <span>
                        <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path fill-rule="evenodd" clip-rule="evenodd" d="M12.958 2.34399C14.6372 5.52163 14.103 9.4162 11.6303 12.0244C9.1576 14.6326 5.29704 15.3735 2.03442 13.8661C2.83817 19.0086 7.44338 22.6807 12.6357 22.3195C17.828 21.9583 21.8804 17.6838 21.9644 12.4797C22.0483 7.27549 18.136 2.87253 12.958 2.34399Z" stroke="var(--foreground)" stroke-linecap="square"></path>
                        </svg>
                     </span>
                  </a>
               </li>
            </ul>
            <!----> --}}
            <div class="-header-sidebar" x-data="{ sidebarTippy: {
               content: () => $refs.template.innerHTML,
               allowHTML: true,
               appendTo: $root,
               maxWidth: 415,
               interactive: true,
               trigger: 'click',
               animation: 'scale',
            } }">
         
               <template x-ref="template" class="hidden">
                  <div class="yena-menu-list !w-[415px]">
               
                     <a class="yena-menu-list-item border border-transparent hover:border-[color:var(--yena-colors-gray-200)!important] hover:bg-[var(--yena-colors-gray-100)!important] cursor-pointer" @click="$dispatch('open-modal', 'edit-profile-modal');">
                        <span class="yena-avatar !h-8 !w-8">
                           <img src="{{ iam()->getAvatar() }}" class="yena-update-avatar w-full h-full object-cover" alt=" " class="">
                        </span>
                        <div class="flex flex-col ml-2">
                           <p class=" text-sm">{{ iam()->name }}</p>
                           <p class="text-[color:var(--yena-colors-gray-500)] text-xs">{{ iam()->email }}</p>
                        </div>
                        <div class="--icon ml-auto !mr-0">
                           {!! __icon('interface-essential', 'setting4', 'w-5 h-5') !!}
                        </div>
                     </a>
                     {{-- <hr class="--divider">
         
                     <div class="-menu-group">
                        <div class="mx-4 my-2 font-semibold text-sm text-[color:var(--yena-colors-gray-500)]">
                           {{ __('Workspaces') }}
                        </div>
                     </div>
               
                     <div class="yena-menu-list-item border border-transparent hover:border-[color:var(--yena-colors-gray-200)!important] hover:bg-[var(--yena-colors-gray-100)!important]">
                        <span class="yena-avatar !h-[32px] !w-[32px] !bg-[var(--yena-colors-orchid-300)] !text-white !flex items-center justify-center">
                           <p class="text-sm leading-none">J</p>
                        </span>
                        <div class="flex flex-col ml-2">
                           <p class=" text-sm">Jeff Jola's Workspace</p>
                        </div>
                        <div class="--icon ml-auto !mr-0">
                           <a href="" class="yena-btn-clean !text-[color:var(--yena-colors-trueblue-600)] border-2">{{ __('Settings & members') }}</a>
                        </div>
                     </div> --}}
                     <hr class="--divider">
                     <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="$dispatch('logout')">
                        <div class="--icon">
                           {!! __icon('interface-essential', 'login-ogout', 'w-5 h-5') !!}
                        </div>
                        <span>{{ __('Sign out') }}</span>
                     </a>
                  </div>
               </template>
         
               
         
               <div class="yena-avatar !w-7 !h-7 cursor-pointer" x-tooltip="sidebarTippy">
                  <img src="{{ iam()->get_original_user()->getAvatar() }}" class="w-full h-full object-cover" alt="">
               </div>
              </div>
            <!---->
         </div>
      </div>
   </header>
</div>
