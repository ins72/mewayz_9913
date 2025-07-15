<?php

?>

<div>
    <div class="mt-2 content">
        <div class="panel-input mb-1 px-[var(--s-2)]">
           <form>
            
            <div class="input-box">
               <label for="text-size">{{ __('Logo') }}</label>
               <div class="input-group two-col">
                  <button class="btn-nav" type="button" :class="{
                     'active': site.header.logo_type == 'text',
                  }" @click="site.header.logo_type = 'text'"
                  >{{ __('Text') }}</button>

                  <button class="btn-nav" type="button" :class="{
                     'active': site.header.logo_type == 'image',
                  }" @click="site.header.logo_type = 'image'"
                  >{{ __('Image') }}</button>
               </div>
            </div>
            
            <div class="input-box" :class="{'!hidden': site.header.logo_type !== 'text'}">
               <label for="text-size"></label>
               <div class="input-group">
                  <input type="text" class="input-small blur-body" x-model="site.header.logo_text" name="title" placeholder="{{ __('Your Logo') }}">
               </div>
            </div>
            
            <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed text-center hover:border-solid hover:border-yellow-600 relative mb-1" :class="{
               'border-gray-200': !site.header.logo,
               'border-transparent': site.header.logo,
               '!hidden': site.header.logo_type !== 'image'
              }">
               <template x-if="site.header.logo">
                  <div class="group-hover:flex hidden w-full h-full items-center justify-center absolute right-0 top-0 left-0 bottom-0">
                     <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center icon-shadow" @click="site.header.logo = ''; $dispatch('sectionMediaEvent:header', {
                        image: null,
                        public: null,
                        })">
                         <i class="fi fi-rr-trash"></i>
                     </div>
                 </div>
               </template>
               <template x-if="!site.header.logo">
                  <div class="w-full h-full flex items-center justify-center" @click="page = 'media'; $dispatch('mediaEventDispatcher', {
                     event: 'sectionMediaEvent:header', sectionBack:'navigatePage(\'__last_state\')'
                     })">
                      <div>
                          <span class="loader-line-dot-dot text-black font-2 -mt-2 m-0"></span>
                      </div>
                      <i class="fi fi-ss-plus"></i>
                  </div>
               </template>
               <template x-if="site.header.logo">
                  <div class="h-full w-[100%]">
                      <img :src="$store.builder.getMedia(site.header.logo)" class="h-full w-[100%] object-cover rounded-md" alt="">
                  </div>
               </template>
            </div>
            
            <div class="input-box">
               <label for="text">{{ __('Link') }}</label>

               <div class="input-group">
                  <x-builder.input>
                     <input type="text" class="input-small blur-body" x-model="site.header.link" name="title" placeholder="{{ __('Search site or paste link') }}" x-on:input="filter()">
                  </x-builder.input>
               </div>
            </div>
           </form>
        </div>

        <div class="form-action !block">
           <div class="cursor-pointer input-box mb-0" @click="__page='links'; clearTimeout(autoSaveTimer);">
              <div class="input-group !rounded-b-none" >
                 <div class="input-chevron" >
                    <label>{{ __('Links') }}</label>
                    <span>
                       {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                    </span>
                 </div>
              </div>
           </div>
           <div class="cursor-pointer input-box mb-0" @click="__page='button'; clearTimeout(autoSaveTimer);">
              <div class="input-group !rounded-t-none !border-t-0 !rounded-b-none">
                 <div class="input-chevron" >
                    <label>{{ __('Buttons') }}</label>
                    <span>
                       {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                    </span>
                 </div>
              </div>
           </div>
           <div class="cursor-pointer input-box" @click="__page='announcement'; clearTimeout(autoSaveTimer);">
              <div class="input-group !rounded-t-none !border-t-0">
                 <div class="input-chevron" >
                    <label>{{ __('Announcement') }}</label>
                    <span>
                       {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                    </span>
                 </div>
              </div>
           </div>
        </div>
     </div>

</div>