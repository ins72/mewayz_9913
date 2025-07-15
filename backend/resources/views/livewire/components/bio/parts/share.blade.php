
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

         <div class="relative flex w-[100%] isolate  gap-[var(--chakra-space-2)] [grid-template-areas:"paste_generate_import"] grid-cols-[1fr_1fr_1fr]">
            <input type="text" class="w-[100%] h-[3rem] text-[1rem] pl-4 pr-10 rounded-md min-w-0 outline-[transparent_solid_2px] outline-offset-[2px] relative appearance-none [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 border-2 border-solid [border-image:initial] border-transparent bg-[#f7f3f2]" placeholder="{{ __('link goes here...') }}" readonly :value="$store.bio.generateSiteLink(site)">

            <div class="right-0 h-[3rem] text-[1rem] flex items-center justify-center absolute top-0 z-[1]">
               <button type="button" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[2rem] [2.5rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] m-2" @click="$clipboard($store.bio.generateSiteLink(site)); $el.innerText = window.builderObject.copiedText;">{{ __('Copy Link') }}</button>
            </div>
          </div>


         <ul class="[list-style:none] flex justify-start w-[100%] gap-[20px] mt-2">
            <li class="flex flex-col items-center text-[var(--t-s)] text-[var(--c-mix-3)]">
               <a :href="'https://www.facebook.com/sharer/sharer.php?u='+$store.bio.generateSiteLink(site)" target="_blank" class="w-[calc(var(--unit)_*_6)] h-[calc(var(--unit)_*_6)] flex justify-center items-center border-[1px] border-solid border-[var(--c-mix-1)] mb-[8px] rounded-full">
                  <i class="ph ph-facebook-logo text-lg"></i>
               </a>
            </li>
            <li>
               <a :href="'https://twitter.com/intent/tweet?url='+$store.bio.generateSiteLink(site)+'/&text='" class="w-[calc(var(--unit)_*_6)] h-[calc(var(--unit)_*_6)] flex justify-center items-center border-[1px] border-solid border-[var(--c-mix-1)] mb-[8px] rounded-full">
                  <i class="ph ph-x-logo text-lg"></i>
               </a>
            </li>
            <li>
               <a :href="'https://api.whatsapp.com/send?text='+$store.bio.generateSiteLink(site)" class="w-[calc(var(--unit)_*_6)] h-[calc(var(--unit)_*_6)] flex justify-center items-center border-[1px] border-solid border-[var(--c-mix-1)] mb-[8px] rounded-full">
                  <i class="ph ph-whatsapp-logo text-lg"></i>
               </a>
            </li>
            <li>
               <a :href="'https://www.linkedin.com/shareArticle?mini=true&url='+$store.bio.generateSiteLink(site)" class="w-[calc(var(--unit)_*_6)] h-[calc(var(--unit)_*_6)] flex justify-center items-center border-[1px] border-solid border-[var(--c-mix-1)] mb-[8px] rounded-full">
                  <i class="ph ph-linkedin-logo text-lg"></i>
               </a>
            </li>
         </ul>

         <a type="button" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[3rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] m-2" :href="$store.bio.generateSiteLink(site)" target="_blank">{{ __('View site') }}</a>
      </div>
   </div>
</div>