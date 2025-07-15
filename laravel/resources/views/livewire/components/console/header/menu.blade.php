<div class="md:hidden mobile-header-toolbar">
    <div class="bg-white h-14 shadow-- fixed z-20 w-full top-0 left-0" x-data>
        <div class="flex flex-row items-center justify-between h-full px-4">
            <a @click="$store.app.toggleSidebar()">
                {!! __i('--ie', 'menu-burger.4', 'w-6 h-6') !!}
            </a>
           <div class="mt-0 header-center"></div>
         
           <div x-data="{ tippy: {
              content: () => $refs.template.innerHTML,
              allowHTML: true,
              appendTo: document.body,
              maxWidth: 250,
              interactive: true,
              trigger: 'click',
              animation: 'scale',
              placement: 'bottom-start'
           } }">

              <div class="yena-avatar !w-7 !h-7 cursor-pointer" x-tooltip="tippy">
                 <img src="{{ iam()->getAvatar() }}" class="w-full h-full object-cover" alt="">
              </div>

              <template x-ref="template" class="hidden">
                 <div class="yena-menu-list !min-w-[initial] !w-[250px] !max-w-full p-[var(--yena-space-2)]">
                    <p class="my-[var(--yena-space-2)] mr-[var(--yena-space-4)] ml-[var(--yena-space-2)] font-semibold text-sm normal-case text-[var(--yena-colors-gray-500)] tracking-[0px]">{{ iam()->email }}</p>
                    
                    <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="$dispatch('logout')">
                       <div class="--icon">
                          {!! __icon('interface-essential', 'login-ogout', 'w-5 h-5') !!}
                       </div>
                       <span>{{ __('Sign out') }}</span>
                    </a>
                 </div>
              </template>
           </div>
        </div>
     </div>
     <div class="h-14 flex-none"></div>
</div>