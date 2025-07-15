<?php

?>

<div>
    <div class="mt-2 content">
        <div class="panel-input mb-1 px-[var(--s-2)]">
           <form>
              <div class="flex flex-col gap-2">

               <div class="input-box">
                  <label>{{ __('Title') }}</label>
                  <div class="input-group">
                      <input type="text" class="input-small"  x-model="section.content.title" name="title" placeholder="{{ __('Add main heading') }}">
                  </div>
               </div>

               <div class="input-box">
                   <label>{{ __('Subtitle') }}</label>
                   <div class="input-group">
                      <textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="section.content.subtitle" name="title" placeholder="{{ __('Add text here') }}"></textarea>
                   </div>
                </div>
              </div>
              <div class="accordion mt-1">
               <div x-ref="sortable_wrapper" class="flex flex-col gap-2">
                  <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid" x-ref="sortable_template">
                     <button class="panel-button media-button" @click="__page = 'section::slider::' + item.uuid" type="button">
                        <div class="handle">
                           {!! __i('custom', 'grip-dots-vertical', 'w-3 h-3') !!}
                        </div>
                        
                        <span class="image-holder !mr-0">
                           <template x-if="!item.image">
                              <div class="default-image">
                                 {!! __i('--ie', 'image-picture', 'text-gray-300', 'w-5 h-5') !!}
                              </div>
                           </template>
                           <template x-if="item.image">
                              <img :src="$store.builder.getMedia(item.image)" class="!object-contain">
                           </template>
                        </span>
                        <p class="sub-panel-width ml-2 mr-auto">
                           <span x-text="item.content.title"></span>
                        </p>
                        {!! __i('Arrows, Diagrams', 'Arrow.5', 'panel-button__icon') !!}
                     </button>
                  </template>
               </div>
               
               <div class="mt-1 accordion-item add-new-accordion" @click="createItem">
                  <button class="accordion-header" type="button">
                     <p ><span >{{ __('Add Slider') }}</span></p>
                     <span class="plus-icon">
                        {!! __i('interface-essential', 'plus-add.3') !!}
                     </span>
                  </button>
               </div>
            </div>
           </form>
        </div>
     </div>

</div>