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
              <div class="input-box">
                  <label for="text-size">{{ __('Pricing') }}</label>
                  <div class="input-group align-type">
                     <button class="btn-nav !w-[50%] active" type="button" @click="section.settings.type = 'single'" :class="{'active': section.settings.type =='single'}">
                        <span>{{ __('Single') }}</span>
                     </button>
                     <button class="btn-nav !w-[50%]" type="button" @click="section.settings.type = 'plans'" :class="{'active': section.settings.type == 'plans'}">
                        <span>{{ __('Plans') }}</span>
                     </button>
                  </div>
               </div>
               <div class="input-box">
               <label for="text-size">{{ __('Currency') }}</label>
               <div class="input-group currency-group">
                  <select class="focus:!shadow-none focus:!border-0 focus:!outline-none [box-shadow:none!important]" x-model="section.settings.currency">
                     <template x-for="(currency, index) in currencies" :key="index">
                        <option :value="index" x-html="currency +' '+ index" :selected="section.settings.currency==index"></option>
                     </template>
                  </select>
               </div>
            </div>

              <div class="accordion">
               <div x-ref="sortable_wrapper" class="flex flex-col gap-2">
                  <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid" x-ref="sortable_template">
                     
                     <button class="panel-button media-button !justify-start" @click="__page = 'section::pricing::' + item.uuid" type="button">
                        <div class="handle">
                           {!! __i('custom', 'grip-dots-vertical', 'w-3 h-3') !!}
                        </div>
                        <p class="sub-panel-width ml-2">
                           <span x-text="item.content.title"></span>
                        </p>


                        {!! __i('Arrows, Diagrams', 'Arrow.5', 'panel-button__icon !ml-auto') !!}
                     </button>
                  </template>
               </div>
               
               <template x-if="(section.items.length + 1) <= 3">
                  <div class="mt-1 accordion-item add-new-accordion" @click="createItem">
                     <button class="accordion-header" type="button">
                        <p ><span >{{ __('Add Pricing') }}</span></p>
                        <span class="plus-icon">
                           {!! __i('interface-essential', 'plus-add.3') !!}
                        </span>
                     </button>
                  </div>
               </template>
            </div>
           </form>
        </div>
     </div>

</div>