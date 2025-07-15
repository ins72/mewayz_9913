<?php

?>

<div>
    <div class="mt-2 content">
        <div class="panel-input mb-1 px-[var(--s-2)]">
           <form>
              <div class="flex flex-col gap-3">

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
               <div x-ref="sortable_wrapper" class="grid grid-cols-1 gap-2">
                  <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid" x-ref="sortable_template">
                     <div>
                        <x-livewire::components.bio.sections.embed.partial.edit.--item />
                     </div>
                  </template>
               </div>
               
               <div class="mt-1 accordion-item add-new-accordion !rounded-full" @click="createItem">
                  <button class="accordion-header !px-4" type="button">
                     <p ><span >{{ __('Add Embed') }}</span></p>
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