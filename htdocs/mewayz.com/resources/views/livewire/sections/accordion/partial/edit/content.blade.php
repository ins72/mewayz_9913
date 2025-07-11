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
                    <x-builder.textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="section.content.subtitle" name="title" placeholder="{{ __('Add text here') }}"></x-builder.textarea>
                 </div>
              </div>
              <div class="accordion">
               <div x-ref="sortable_wrapper">

               <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid" x-ref="sortable_template">
                  <div class="accordion-item accordion sortable-item" x-bind:data-id="item.uuid" @click="__page = 'section::accordion::' + item.uuid">
                     <div class="accordion-header menu">
                        <div class="cursor-move handle">
                           {!! __i('custom', 'grip-dots-vertical', '!w-[10px] !h-[10px]') !!}
                        </div>
                        <p class="sub-panel-width">
                           <span x-text="item.content.title"></span>
                        </p>
                        <span>
                           {!! __i('Arrows, Diagrams', 'Arrow.5', '!w-5 !h-5') !!}
                        </span>
                     </div>
                     <div class="accordion-body menu call-to-action"></div>
                  </div>
               </template>
             </div>
               
               <div class="mt-1 accordion-item add-new-accordion" @click="createItem">
                  <button class="accordion-header" type="button">
                     <p ><span >{{ __('Add Accordion') }}</span></p>
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