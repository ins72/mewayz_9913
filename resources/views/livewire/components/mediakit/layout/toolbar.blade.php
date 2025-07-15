
<?php

   use function Livewire\Volt\{state, mount};

   state([
      'site'
   ]);

?>

<div class="editor--toolbar w-[100%] fixed top-0 h-[60px] !pl-[6px]">
   <div class="flex flex-row items-center">
      <div class="flex">

         <button class="yena-button-stack !bg-transparent !shadow-none !border-none">
            <nav class="h-[var(--yena-sizes-8)] min-w-[var(--yena-sizes-8)] text-sm flex items-center justify-center md:justify-start whitespace-nowrap">
               <ol class="flex items-center">
                  <li class="inline-flex items-center">
                     <div class="flex">
                        <a href="{{ route('dashboard-sites-index') }}" class="md:mr-1">
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
   </div>

   <div class="flex-1 pointer-events-none place-self-stretch"></div>

   <div class="flex flex-row items-center">
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
         '!hidden': $store.builder.detectMobile()
      }"></div>
      @endif

   
      @if ($site->canEdit())
      <div class="items-center flex">
         <span class="!min-h-0 text-sm -light -smaller mmr-[16px] hidden md:!block">
            <span x-show="$store.builder.savingState==0">{{ __("Saving...") }}</span>
            <span x-show="$store.builder.savingState==2">{{ __("Saved") }}</span>
         </span>
      </div>
      @endif

      <div class="h-10 w-[1px] bg-[var(--yena-colors-blackAlpha-100)] hidden md:inline-flex mx-[20px]"></div>

      <div class="items-center hidden md:!flex">
            <div>

               <div class="yena-avatar !w-7 !h-7 cursor-pointer" x-tooltip="tippy">
                  <img src="{{ iam()->getAvatar() }}" class="w-[100%] h-full object-cover" alt="">
               </div>
            </div>
      </div>
   </div>
</div>