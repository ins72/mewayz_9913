
<?php

   use function Livewire\Volt\{state, mount};

   state([
      'site'
   ]);

?>

<div class="editor--toolbar w-full fixed top-0 h-[60px] !pl-[6px]">
   <div class="flex flex-row items-center">
      <div class="flex">

         <button class="yena-button-stack !bg-transparent !shadow-none !border-none">
            <nav class="h-[var(--yena-sizes-8)] min-w-[var(--yena-sizes-8)] text-sm flex items-center justify-center md:justify-start whitespace-nowrap">
               <ol class="flex items-center">
                  <li class="inline-flex items-center">
                     <div class="flex">
                        <a href="{{ route('console-sites-index') }}" class="md:mr-1">
                           {!! __icon('interface-essential', 'home-house-line 2', 'w-4 h-4') !!}
                        </a>
                     </div>

                     <span class="mx-[1px] //sm:mx-2 !hidden md:!block">
                        <div class="text-[color:var(--yena-colors-gray-300)]">
                           {!! __i('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                        </div>
                     </span>
                  </li>
                  <li class="items-center !hidden md:!flex">
                     <div class="flex">
                        <span class="[transition-property:var(--yena-transition-property-common)] cursor-pointer no-underline outline-[transparent_solid_2px] outline-offset-[2px] [pointer-events:inherit] text-[color:var(--yena-colors-gray-500)]" contenteditable="true" x-on:blur="site.name = $event.target.innerText" x-text="site.name">
                        </span>
                     </div>
                  </li>
               </ol>
            </nav>
         </button>
      </div>
      <div class="h-10 w-[1px] bg-[var(--yena-colors-blackAlpha-100)] inline-flex mx-[20px]"></div>

      {{-- <div class="h-10 w-[1px] bg-[var(--yena-colors-blackAlpha-100)] inline-flex mx-[4px]"></div> --}}
   
   @if ($site->canEdit())
      <div class="hidden md:flex -gap-4 frame_left">
         <div class="flex items-center justify-center">
            <a class="button -minimal -smaller -square transform scale-x-[-1]" x-on:click="renderView = renderView == 'max' ? 'normal' : 'max'">
               <i class="text-sm fi fi-rr-down-left-and-up-right-to-center" x-cloak x-show="renderView == 'max'"></i>
               <i class="text-sm fi fi-rr-arrow-up-right-and-arrow-down-left-from-center" x-show="renderView == 'normal'"></i>
           </a>
         </div>
         <div class="h-10 w-[1px] bg-[var(--yena-colors-blackAlpha-100)] inline-flex mx-[20px]"></div>

         <div class="flex items-center justify-center">
            <a class="button -minimal -smaller -square" data-state="closed" x-on:click="renderMobile = renderMobile ? false : true">
               <i class="text-sm fi fi-rr-computer" x-cloak x-show="renderMobile"></i>
               <i class="text-sm fi fi-rr-mobile-notch" x-show="!renderMobile"></i>
           </a>
         </div>
      </div>
   @endif
   </div>

   <div class="flex-1 pointer-events-none place-self-stretch"></div>

   <div class="flex flex-row items-center">


   
      @if ($site->canEdit())
      <div class="hidden md:block">
         <button type="button" class="yena-button-o" @click="navigatePage('design')">
            <span class="--icon">
               {!! __icon('Design Tools', 'Bucket, Paint', 'w-5 h-5') !!}
            </span>
            {{ __('Design') }}
         </button>
      </div>
      <div class="h-10 w-[1px] bg-[var(--yena-colors-blackAlpha-100)] hidden md:inline-flex mx-[20px]"></div>
      @endif
      <div>
         <button type="button" class="yena-button-o" @click="$dispatch('open-modal', 'share-modal');">
            <span class="--icon">
               {!! __icon('--ie', 'share.1', 'w-5 h-5') !!}
            </span>
            {{ __('Share') }}
         </button>
      </div>
      
   
      @if ($site->canEdit())
      <div class="h-10 w-[1px] bg-[var(--yena-colors-blackAlpha-100)] zzhidden inline-flex md:inline-flex mx-[20px]" :class="{
         '!hidden': $store.builder.detectMobile() && site.published==true
      }"></div>
      @endif

   
      @if ($site->canEdit())
      <div class="items-center flex">
         <span class="!min-h-0 text-sm -light -smaller mmr-[16px] hidden md:!block">
            <span x-show="$store.builder.savingState==0">{{ __("Saving...") }}</span>
            <span x-show="$store.builder.savingState==2">{{ __("Saved") }}</span>
         </span>
         <div class="h-10 w-[1px] bg-[var(--yena-colors-blackAlpha-100)] hidden md:!inline-flex mx-[20px]" :class="{
            '!hidden md:!hidden': site.published==true
         }"></div>
         <button class="button -smaller" type="button" @click="site.published=true" :class="{
            '!hidden': site.published==true
         }">{{ __('Publish') }}</button>
         <div class="h-10 w-[1px] bg-[var(--yena-colors-blackAlpha-100)] hidden md:!inline-flex mx-[20px]"></div>
         <button class="button -smaller !bg-[var(--c-mix-1)] !text-black !hidden md:!inline-flex" type="button" @click="$dispatch('open-modal', 'upgrade-modal')">{{ __('Upgrade') }}</button>
      </div>
      @endif

      <div class="h-10 w-[1px] bg-[var(--yena-colors-blackAlpha-100)] hidden md:inline-flex mx-[20px]"></div>

      <div class="items-center hidden md:!flex">
   
         @if ($site->canEdit())
         <div class="mr-[4px]" >
            <button type="button" class="yena-button-o !px-0" @click="navigatePage('analytics')">
               <span class="--icon !mr-0">
                  {!! __icon('Business, Products', 'blackboard-business-chart', 'w-5 h-5') !!}
               </span>
            </button>
         </div>

         <div class="mr-[4px]" x-data="{ tippy: {
            content: () => $refs.template.innerHTML,
            allowHTML: true,
            appendTo: document.body,
            maxWidth: 360,
            interactive: true,
            trigger: 'click',
            animation: 'scale',
         } }">
            <template x-ref="template">
               <div class="yena-menu-list !w-full">
                  <div class="px-4">
                     <p class="yena-text">{{ $site->name }}</p>
         
                     <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">{{ __('Created') }} {{ \Carbon\Carbon::parse($site->created_at)->format('F d\t\h, Y') }}</p>
                     <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">{{ __('by :who', [
                        'who' => $site->createdBy()->name
                     ]) }}</p>
                  </div>
         
                  <hr class="--divider">
         
                  <a @click="$dispatch('open-modal', 'share-modal');" class="yena-menu-list-item">
                     <div class="--icon">
                        {!! __icon('interface-essential', 'share-arrow.2', 'w-5 h-5') !!}
                     </div>
                     <span>{{ __('Share') }}</span>
                  </a>
         
                  <a @click="navigatePage('settings')" class="yena-menu-list-item">
                     <div class="--icon">
                        {!! __icon('interface-essential', 'document-text-edit', 'w-5 h-5') !!}
                     </div>
                     <span>{{ __('Settings') }}</span>
                  </a>
                  <hr class="--divider">
                  <a @click="$dispatch('siteDuplicate')" class="yena-menu-list-item">
                     <div class="--icon">
                        {!! __icon('interface-essential', 'copy-duplicate-object-add-plus', 'w-5 h-5') !!}
                     </div>
                     <span>{{ __('Duplicate') }}</span>
                  </a>
                  <a x-data="{__text:'{{ __('Copy Link') }}'}" @click="clipboard($store.builder.generateSiteLink(site)); __text = window.builderObject.copiedText;" class="yena-menu-list-item">
                     <div class="--icon">
                        {!! __icon('interface-essential', 'share-arrow.1', 'w-5 h-5') !!}
                     </div>
                     <span x-text="__text">{{ __('Copy link') }}</span>
                  </a>
              </div>
            </template>
            <button type="button" class="yena-button-o !px-0" x-tooltip="tippy">
               <span class="--icon !mr-0">
                  {!! __icon('interface-essential', 'dots-menu', 'w-5 h-5') !!}
               </span>
            </button>
         </div>
         @endif
         
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
                  <img src="{{ iam()->getAvatar() }}" class="w-[100%] h-full object-cover" alt="">
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
</div>