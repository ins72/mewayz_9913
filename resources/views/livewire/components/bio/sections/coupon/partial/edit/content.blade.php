<?php

?>

<div>
    <div class="mt-2 content">
        <div class="panel-input mb-1 px-[var(--s-2)]">
           <form>

                           
               <div x-init="window.addEventListener(`section_media::${section.uuid}`, (event) => {
                  section.content.image = event.detail.image;
                  _save();
               });"></div>
              <div class="flex flex-col gap-3">

               <div class="input-box">
                  <label>{{ __('Title') }}</label>
                  <div class="input-group">
                     <input type="text" class="input-small"  x-model="section.content.title" name="title" placeholder="{{ __('Add main heading') }}">
                  </div>
               </div>

               <div class="relative block h-20 text-center border-2 border-dashed rounded-lg cursor-pointer group bg-white- hover:border-solid hover:border-yellow-600" :class="{
                  'border-gray-200': !section.content.image,
                  'border-transparent': section.content.image,
                 }">
                  <template x-if="section.content.image">
                     <div class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden w-[100%] h-full group-hover:flex">
                        <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full icon-shadow" @click="section.content.image = ''; $dispatch('section_media::' + section.uuid, {
                          image: null,
                          public: null,
                         })">
                            <i class="fi fi-rr-trash"></i>
                        </div>
                    </div>
                  </template>
                  <template x-if="!section.content.image">
                     <div class="flex items-center justify-center w-[100%] h-full" @click="openMedia({
                          event: 'section_media::' + section.uuid,
                          sectionBack:'navigatePage(\'__last_state\')'
                      });">
                         <div>
                             <span class="m-0 -mt-2 text-black loader-line-dot-dot font-2"></span>
                         </div>
                         <i class="fi fi-ss-plus"></i>
                     </div>
                  </template>
                  <template x-if="section.content.image">
                     <div class="h-full w-[100%]">
                         <img :src="$store.builder.getMedia(section.content.image)" class="h-full w-[100%] object-cover rounded-md" alt="">
                     </div>
                  </template>
              </div>

               <div class="input-box">
                  <label>{{ __('Title') }}</label>
                  <div class="input-group">
                     <input type="text" class="input-small"  x-model="section.content.coupon_title" placeholder="{{ __('Add title') }}">
                  </div>
               </div>

               <div class="input-box">
                  <label>{{ __('Description') }}</label>
                  <div class="input-group">
                     <textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="section.content.coupon_desc" placeholder="{{ __('Add text here') }}"></textarea>
                  </div>
               </div>

               

               <div class="input-box">
                  <label>{{ __('Code') }}</label>
                  <div class="input-group">
                     <input type="text" class="input-small"  x-model="section.content.coupon_code" name="title" placeholder="{{ __('Add code') }}">
                  </div>
               </div>


               <div class="advanced-section-settings !mt-0">
                  <form onsubmit="return false">
                     
                     <div class="input-box open-tab-box">
                        <div class="input-group">
                           <div class="switchWrapper">
                              <input id="showCBTN-switch" x-model="section.content.show_coupon_button" type="checkbox" class="switchInput">
                              
                              <label for="showCBTN-switch" class="switchLabel">{{__('Show coupon button')}}</label>
                              <div class="slider"></div>
                           </div>
                        </div>
                     </div>
                  </form>
                  <template x-if="section.content.show_coupon_button">
                     <div>
                        <div class="input-box mt-1">
                           <label>{{ __('Button URL') }}</label>
                           <div class="input-group">
                              <input type="text" class="input-small"  x-model="section.content.link_button_link" placeholder="{{ __('Add Url') }}">
                           </div>
                        </div>
                        <div class="input-box mt-1">
                           <label>{{ __('Button Text') }}</label>
                           <div class="input-group">
                              <input type="text" class="input-small"  x-model="section.content.link_button_text" placeholder="{{ __('Add text') }}">
                           </div>
                        </div>
                     </div>
                  </template>
               </div>
              </div>
           </form>
        </div>
     </div>

</div>