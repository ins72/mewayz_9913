<?php

?>

<div>
    <div class="mt-2 content">
        <div class="panel-input mb-1 px-[var(--s-2)]">
           <form>
              <div class="input-box">
                 <label for="text">{{ __('Label') }}</label>
                 <div class="input-group">
                    <input type="text" class="input-small blur-body" x-model="section.content.label" name="title" placeholder="{{ __('Add label') }}">
                 </div>
              </div>
              <div class="input-box">
                 <label for="text">{{ __('Title') }}</label>
                 <div class="input-group">
                    <x-builder.textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[65px]" x-model="section.content.title" name="title" placeholder="{{ __('Add main heading') }}"></x-builder.textarea>
                 </div>
              </div>
              <div class="input-box">
                 <label for="text">{{ __('Subtitle') }}</label>
                 <div class="input-group">
                    <x-builder.textarea class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="section.content.subtitle" name="title" placeholder="{{ __('Add text here') }}"/>
                 </div>
              </div>
              <div class="input-box !hidden">
                 <div class="input-label">{{ __('Type') }}</div>
                 <div class="input-group link-group">
                    <button class="text-center btn-link btn-image active" type="button">{{ __('Image') }}</button>
                    <button class="text-center btn-link btn-video" type="button">{{ __('Video') }}</button>
                </div>
              </div>
              <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed text-center hover:border-solid hover:border-yellow-600 relative" :class="{
               'border-gray-200': !section.image,
               'border-transparent': section.image,
              }">
               <template x-if="section.image">
                  <div class="group-hover:flex hidden w-full h-full items-center justify-center absolute right-0 top-0 left-0 bottom-0">
                     <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center icon-shadow" @click="section.image = ''; $dispatch('sectionMediaEvent:' + section.uuid, {
                        image: null,
                        public: null,
                        })">
                         <i class="fi fi-rr-trash"></i>
                     </div>
                 </div>
               </template>
               <template x-if="!section.image">
                  <div class="w-full h-full flex items-center justify-center" @click="page = 'media'; $dispatch('mediaEventDispatcher', {
                     event: 'sectionMediaEvent:' + section.uuid, sectionBack:'navigatePage(\'__last_state\')'
                     })">
                      <div>
                          <span class="loader-line-dot-dot text-black font-2 -mt-2 m-0"></span>
                      </div>
                      <i class="fi fi-ss-plus"></i>
                  </div>
               </template>
               <template x-if="section.image">
                  <div class="h-full w-[100%]">
                      <img :src="section.get_image" class="h-full w-[100%] object-cover rounded-md" alt="">
                  </div>
               </template>
               </div>
              <div class="input-box mt-1">
                 <div class="input-label">{{ __('Action') }}</div>
                 <div class="input-group link-group">
                    <button class="text-center btn-action btn-button" type="button" :class="{'active': section.settings.actiontype == 'button'}" @click="section.settings.actiontype = 'button'">
                        {{ __('Buttons') }}
                    </button>
                    <button class="text-center btn-action btn-form" type="button" :class="{'active': section.settings.actiontype == 'form'}" @click="section.settings.actiontype = 'form'">
                        {{ __('Form') }}
                    </button>
                </div>
              </div>
           </form>
        </div>

        <div class="form-action !block">
           <div class="cursor-pointer input-box" @click="__page=section.settings.actiontype == 'form' ? 'form' : 'button'; clearTimeout(autoSaveTimer);">
              <div class="input-group" >
                 <div class="input-chevron" >
                    <label x-text="section.settings.actiontype == 'form' ? '{{ __('Form') }}' : '{{ __('Button') }}'"></label>
                    <span>
                       {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                    </span>
                 </div>
              </div>
           </div>
        </div>
     </div>

</div>