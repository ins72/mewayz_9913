
<div class="w-[100%]">
   <div>
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-1 right-1 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross-small"></i>
      </a>

      <header class="flex pt-4 px-6 flex-initial text-3xl font-black" x-text="'{{ __('Share') }}' +' '+ site.name"></header>
      
      <div class="px-6 pb-4">
         <p class="text-[var(--yena-fontSizes-sm)] text-[var(--yena-colors-gray-600)] pt-[var(--yena-space-2)]">
            {!! __t('Let anyone with a link view a <span class="border-b-[0.125em] [border-bottom-style:dashed] border-[var(--yena-colors-gray-400)] cursor-help">
               <span tabindex="0" class="css-0">read-only</span>
            </span> version of this site.') !!}
         </p>

         <div class="flex items-stretch flex-col gap-[var(--yena-space-1)] pt-[var(--yena-space-4)] pb-[var(--yena-space-4)]">
            <div class="flex items-center justify-between flex-row gap-2 w-[100%] pt-[var(--yena-space-2)] pb-[var(--yena-space-2)]">
               <div>
                  <div class="flex items-center">
                     <div class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-[var(--yena-sizes-8)] h-[var(--yena-sizes-8)] rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100">
                        {!! __i('Maps, Navigation', 'Earth, Home, World.3', 'w-5 h-5') !!}
                     </div>
                     <div class="ml-3">
                        <p>{{ __('Public Access') }}</p>
                        <div class="flex items-center">
                           <div class="flex items-center flex-row gap-1">
                              <p class="text-[#8f8b8b] text-xs">{{ __('Anyone with a link can view') }}</p>
                              <div class="text-[#8f8b8b] leading-none">
                                 {!! __i('--ie', 'warning', 'w-2 h-2') !!}
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <div>
                  <label class="sandy-switch">
                     <input class="sandy-switch-input" name="settings[enable_site]" x-model="site.published" value="1" :checked="site.published" type="checkbox">
                     <span class="sandy-switch-in"><span class="sandy-switch-box is-white"></span></span>
                 </label>
               </div>
            </div>

         </div>

         <div class="relative flex w-[100%] isolate  gap-[var(--chakra-space-2)] [grid-template-areas:"paste_generate_import"] grid-cols-[1fr_1fr_1fr]">
            <input type="text" class="w-[100%] h-[3rem] text-[1rem] pl-4 pr-10 rounded-md min-w-0 outline-[transparent_solid_2px] outline-offset-[2px] relative appearance-none [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 border-2 border-solid [border-image:initial] border-transparent bg-[#f7f3f2]" placeholder="{{ __('link goes here...') }}" readonly :value="$store.builder.generateSiteLink(site)">

            <div class="right-0 h-[3rem] text-[1rem] flex items-center justify-center absolute top-0 z-[1]">
               <button type="button" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[2rem] [2.5rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] m-2" @click="$clipboard($store.builder.generateSiteLink(site)); $el.innerText = window.builderObject.copiedText;">{{ __('Copy Link') }}</button>
            </div>
          </div>

         <p class="text-sm text-[var(--yena-colors-gray-500)] mt-1">{{ __('Tip: you can control how this looks in social media and search results in') }} <span class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-baseline outline-[transparent_solid_2px] outline-offset-[2px] leading-[var(--yena-lineHeights-normal)] rounded-md font-semibold [transition-property:var(--yena-transition-property-common)] h-auto min-w-[var(--yena-sizes-8)] text-sm p-0 text-[var(--yena-colors-trueblue-500)] cursor-pointer" @click="page='settings'">{{ __('seo settings') }}</span>.</p>


         <ul class="[list-style:none] flex justify-center w-[100%] px-[var(--s-1)] py-[0] gap-[20px] mt-4">
            <li class="flex flex-col items-center text-[var(--t-s)] text-[var(--c-mix-3)]">
               <a :href="'https://www.facebook.com/sharer/sharer.php?u='+$store.builder.generateSiteLink(site)" target="_blank" class="w-[calc(var(--unit)_*_6)] h-[calc(var(--unit)_*_6)] flex justify-center items-center border-[1px] border-solid border-[var(--c-mix-1)] mb-[8px] rounded-full">
                  <i class="ph ph-facebook-logo text-lg"></i>
               </a>
            </li>
            <li>
               <a :href="'https://twitter.com/intent/tweet?url='+$store.builder.generateSiteLink(site)+'/&text='" class="w-[calc(var(--unit)_*_6)] h-[calc(var(--unit)_*_6)] flex justify-center items-center border-[1px] border-solid border-[var(--c-mix-1)] mb-[8px] rounded-full">
                  <i class="ph ph-x-logo text-lg"></i>
               </a>
            </li>
            <li>
               <a :href="'https://api.whatsapp.com/send?text='+$store.builder.generateSiteLink(site)" class="w-[calc(var(--unit)_*_6)] h-[calc(var(--unit)_*_6)] flex justify-center items-center border-[1px] border-solid border-[var(--c-mix-1)] mb-[8px] rounded-full">
                  <i class="ph ph-whatsapp-logo text-lg"></i>
               </a>
            </li>
            <li>
               <a :href="'https://www.linkedin.com/shareArticle?mini=true&url='+$store.builder.generateSiteLink(site)" class="w-[calc(var(--unit)_*_6)] h-[calc(var(--unit)_*_6)] flex justify-center items-center border-[1px] border-solid border-[var(--c-mix-1)] mb-[8px] rounded-full">
                  <i class="ph ph-linkedin-logo text-lg"></i>
               </a>
            </li>
         </ul>

         <a type="button" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[3rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] m-2" :href="$store.builder.generateSiteLink(site)" target="_blank">{{ __('View site') }}</a>
      </div>
   </div>
</div>