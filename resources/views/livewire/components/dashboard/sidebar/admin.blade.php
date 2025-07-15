
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

   placeholder('placeholders.console.sidebar-placeholder');
?>

<div class="yena-sidebar-inner" x-data="sidebarMenu">
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
                  <img src="{{ $avatar }}" class="yena-update-avatar w-full h-full object-cover" alt=" " class="">
               </span>
               <div class="flex flex-col ml-2">
                  <p class=" text-sm">{{ iam()->name }}</p>
                  <p class="text-[color:var(--yena-colors-gray-500)] text-xs">{{ iam()->email }}</p>
               </div>
               <div class="--icon ml-auto !mr-0">
                  {!! __icon('interface-essential', 'setting4', 'w-5 h-5') !!}
               </div>
            </a>
            <hr class="--divider">

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
            </div>
            <hr class="--divider">
            <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="$dispatch('logout')">
               <div class="--icon">
                  {!! __icon('interface-essential', 'login-ogout', 'w-5 h-5') !!}
               </div>
               <span>{{ __('Sign out') }}</span>
            </a>
         </div>
      </template>

      <template class="lazy-load-template" id="edit-profile-modal">
        <x-modal name="edit-profile-modal" :show="false" removeoverflow="true" maxWidth="xl" >
         <livewire:components.console.sidebar.profile-modal lazy>
         </x-modal>
      </template>


      

        <a class="sidebar-workspace" x-tooltip="{...sidebarTippy}">
           <div class="--padded">
              <div class="flex-grow flex-shrink basis-auto pointer-events-none min-w-0">
                 <div class="w-full">
                    <div class="flex items-center mr-4">
                       <div class="--avatar">
                          <div class="--icon">J</div>
                       </div>

                       <div class="--text">
                          <p>Jeffrey's workspace</p>
                       </div>
                    </div>
                 </div>
              </div>

              <div class="flex self-center shrink ml-2">
                 {!! __i('Arrows, Diagrams', 'Arrow.2', 'h-6') !!}
              </div>
           </div>
         </a>
     </div>
     <div class="sidebar-search">
        <div class="--icon">
           {!! __i('interface-essential', 'search.1') !!}
        </div>
        <input type="text" placeholder="{{ __('Jump to') }}">
        <div class="--right-element">
           <p>{{ __('Ctrl+k') }}</p>
        </div>
     </div>
     <div class="flex flex-col items-center mt-4 mb-2">
        <x-x.href class="sidebar-item" href="{{ route('dashboard-sites-index') }}">
           <div class="--inner">
              {!! __icon('interface-essential', 'browser-internet-web-network-window-app-icon') !!}
              <p>{{ __('All Sites') }}</p>

              <span class="dot-button ml-auto !hidden">
                  <i class="fi fi-rr-plus text-[10px]"></i>
              </span>
           </div>
        </x-x.href>

        <livewire:components.console.sidebar.folders lazy>
        <div class="w-full pt-1 pb-3 border-[var(--yena-colors-gray-200)]">
           <hr class="w-full opacity-[0.6] border-b border-solid">
        </div>
        <x-x.href class="sidebar-item">
           <div class="--inner">
              {!! __icon('interface-essential', 'thunder-lightning-notifications') !!}
              <p>{{ __('Templates') }}</p>
           </div>
        </x-x.href>
        <x-x.href class="sidebar-item">
           <div class="--inner">
              {!! __icon('Content Edit', 'open-book') !!}
              <p>{{ __('Inspiration') }}</p>
           </div>
        </x-x.href>
        <x-x.href class="sidebar-item">
           <div class="--inner">
              {!! __icon('Design Tools', 'Bucket, Paint') !!}
              <p>{{ __('Themes') }}</p>
           </div>
        </x-x.href>
        <x-x.href class="sidebar-item">
           <div class="--inner">
              {!! __icon('Construction, Tools', 'project-book-house') !!}
              <p>{{ __('Custom fonts') }}</p>
           </div>
        </x-x.href>
        <x-x.href class="sidebar-item" href="{{ route('dashboard-trash-index') }}">
           <div class="--inner">
              {!! __icon('interface-essential', 'trash-bin-delete') !!}
              <p>{{ __('Trash') }}</p>
           </div>
        </x-x.href>
        <div class="w-full pt-1 pb-3 border-[var(--yena-colors-gray-200)]">
           <hr class="w-full opacity-[0.6] border-b border-solid">
        </div>
        <x-x.href class="sidebar-item">
           <div class="--inner">
              {!! __icon('Building, Construction', 'store') !!}
              <p>{{ __('Store') }}</p>
           </div>
        </x-x.href>
        <x-x.href class="sidebar-item">
           <div class="--inner">
              {!! __icon('Files', 'file-blank-tree-connect') !!}
              <p>{{ __('Crm') }}</p>
           </div>
        </x-x.href>
     </div>

     <div class="w-full pt-1 pb-3 border-[var(--yena-colors-gray-200)]">
        <hr class="w-full opacity-[0.6] border-b border-solid">
     </div>

     <x-x.href class="sidebar-item !hidden" href="{{ route('dashboard-settings-index') }}">
        <div class="--inner">
           {!! __icon('Users', 'single-user-add-plus_1') !!}
           <p>{{ __('Settings') }}</p>
        </div>
     </x-x.href>

     <x-x.href class="sidebar-item" href="{{ route('dashboard-settings-account') }}">
        <div class="--inner">
           {!! __icon('Users', 'single-user-add-plus_1') !!}
           <p>Invite to workspace</p>
        </div>
     </x-x.href>
     <x-x.href class="sidebar-item">

        <div class="--inner">
           {!! __icon('money', 'Coins') !!}
           <p>245 credit</p>
        </div>
     </x-x.href>
     <x-x.href class="sidebar-item">

        <div class="--inner">
           {!! __icon('Messages Chat', 'Chat, Messages, Bubble.12') !!}
           <p>Contact us</p>
        </div>
     </x-x.href>

     <div class="flex-1"></div>

     <div class="upgrade-plan">
        <div class="--header">
           <span class="--plan-card">{{ __('Pro') }}</span>
           <span class="--plan-text">{{ __('Upgrade to Yena Pro') }}</span>
        </div>

        <div class="--text">{{ __('Unlock unlimited AI and remove our branding') }}</div>

        <div class="--button">
           <x-x.href href="">{{ __('View Plans') }}</x-x.href>
        </div>
     </div>
  
</div>
