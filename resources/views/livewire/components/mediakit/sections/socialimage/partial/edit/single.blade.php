<?php

?>

<div>

   <div class="website-section" x-data="builder__logos_single">
       <div class="design-navbar">
          <ul >
              <li class="close-header !flex">
                <a @click="__page = '-'">
                  <span>
                      {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                  </span>
                </a>
             </li>
             <li class="!pl-0">{{ __('Logo') }}</li>
             <li class="!flex items-center !justify-center">
               <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" @click="__delete_item(item.uuid)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
            </li>
          </ul>
       </div>
       <div class="container-small p-[var(--s-2)] pb-[150px]">
         <form method="post">
            
            <div class="relative block h-20 mb-1 text-center border-2 border-dashed rounded-lg cursor-pointer group bg-white- hover:border-solid hover:border-yellow-600" :class="{
               'border-gray-200': !item.content.image,
               'border-transparent': item.content.image,
              }">
               <template x-if="item.content.image">
                  <div class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden w-[100%] h-full group-hover:flex">
                     <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full icon-shadow" @click="item.content.image = ''; $dispatch('logosMediaEvent:' + item.uuid, {
                      image: null,
                      public: null,
                      })">
                         <i class="fi fi-rr-trash"></i>
                     </div>
                 </div>
               </template>
               <template x-if="!item.content.image">
                  <div class="flex items-center justify-center w-[100%] h-full" @click="page = 'media'; $dispatch('mediaEventDispatcher', {
                   event: 'logosMediaEvent:' + item.uuid, sectionBack:'navigatePage(\'__last_state\')'
                   })">
                      <div>
                          <span class="m-0 -mt-2 text-black loader-line-dot-dot font-2"></span>
                      </div>
                      <i class="fi fi-ss-plus"></i>
                  </div>
               </template>
               <template x-if="item.content.image">
                  <div class="h-full w-[100%]">
                      <img :src="$store.builder.getMedia(item.content.image)" class="h-full w-[100%] object-cover rounded-md" alt="">
                  </div>
               </template>
            </div>

            <template x-if="item.content.image">
               <div class="input-box" x-data="{__showing: 'desktop'}">
                  <div class="input-label">{{__('Size')}}</div>
                  <div class="input-group">
                     <button type="button" class="btn !w-[40px] !h-[40px] !min-w-[40px] !aspect-square !flex !items-center !justify-center !bg-[var(--c-mix-1)] !rounded-tr-none !rounded-br-none" @click="__showing = __showing == 'mobile' ? 'desktop' : 'mobile'">
                        <i class="text-sm fi fi-rr-computer text-[var(--foreground)]" x-cloak x-show="__showing == 'desktop'"></i>
                        <i class="text-sm fi fi-rr-mobile-notch text-[var(--foreground)]" x-show="__showing == 'mobile'"></i>
                     </button>
                     <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'desktop'}" min="0.2" max="2" step="0.1" x-model="item.content.desktop_size">
                     <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'mobile'}" min="0.2" max="2" step="0.1" x-model="item.content.mobile_size">
                     <p class="image-size-value" x-text="__showing == 'desktop' ? (item.content.desktop_size ? item.content.desktop_size : '') : (item.content.mobile_size ? item.content.mobile_size : '')"></p>
                  </div>
               </div>
            </template>
            <div class="mt-1 input-box">
               <div class="input-label">{{ __('Link') }}</div>
               <div class="input-group button-input-group">

                  <x-builder.input>
                     <div class="relative link-options__main">
                        <input class="input-small main__link" type="text" x-model="item.content.text" placeholder="{{ __('Search site or paste link') }}" x-on:input="filter()" >
                     </div>
                  </x-builder.input>
               </div>
            </div>
         </form>
       </div>
    </div>

    @script
    <script>
        Alpine.data('builder__logos_single', () => {
           return {


            init(){

               var $this = this;
               window.addEventListener("logosMediaEvent:" + this.item.uuid, (event) => {
                  $this.item.content.image = event.detail.image;
                  $this.dispatchSections();
                  $this._save();
               });
            }
           }
         });
    </script>
    @endscript
</div>