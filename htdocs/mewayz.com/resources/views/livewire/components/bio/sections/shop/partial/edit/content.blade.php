<?php

?>

<div>
    <div class="mt-2 content">
        <div class="panel-input mb-1 px-[var(--s-2)]">
           <form>
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

              <div x-ref="sortable_wrapper" class="flex flex-col gap-3">
               <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid" x-ref="sortable_template">
                  <div x-data="{is_delete:false, product: getProduct(item.content.product_id)}">
                      <div class="contact-list flex items-center justify-center px-5 py-3 !border !border-[#eee]" >
                          <div class="handle mr-2 cursor-move">
                             {!! __i('custom', 'grip-dots-vertical', 'w-3 h-3') !!}
                          </div>
                          <div>
                              <div class="rounded-xl [box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)] p-0.5 w-12 h-12 overflow-hidden">
                                  <img :src="product.featured_image" alt=" " class="block object-cover w-[100%] h-full">
                              </div>
                          </div>
  
                          <div class=" ml-4 w-[100%] flex justify -center truncate flex-col">
                              <h2 class="flex items-center truncate text-xs md:text-sm">
                                  <div class="truncate" x-text="product.name"></div>
                              </h2>
                              <div class="text-sm text-gray-500">
                                  <div class="truncate">
                                      <span class="flex gap-3" x-html="product.price_html"></span>
                                  </div>
                              </div>
                          </div>
                          <div class="flex justify-end gap-1 w-auto ml-auto">
                              {{-- <div class="bg-[#f3f3f3] w-8 h-8 flex items-center justify-center rounded-full handler cursor-pointer">
                                  {!! __i('interface-essential', 'arrows-resize', 'w-4 h-4') !!}
                              </div> --}}
                              <a class="bg-[#f3f3f3] w-8 h-8 flex items-center justify-center rounded-md cursor-pointer" @click="_editSection=item">
                                 {!! __i('interface-essential', 'pen-edit.5', 'w-4 h-4') !!}
                             </a>
                              <div class="bg-[#f3f3f3] w-8 h-8 flex items-center justify-center rounded-md cursor-pointer" @click="$event.stopPropagation(); is_delete=true;">
                                 {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                              </div>
                          </div>
                      </div>
                      <div class="card-button my-2 flex gap-2" x-cloak @click="$event.stopPropagation();" :class="{
                         '!hidden': !is_delete
                        }">
                         <button class="btn btn-medium neutral !h-8 !flex !items-center !justify-center !rounded-full !text-black !bg-white" type="button" @click="is_delete=!is_delete">{{ __('Cancel') }}</button>
       
                         <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-8 !flex !items-center !justify-center !rounded-full w-full" @click="__delete_item(item.uuid); is_delete=false;">{{ __('Yes, Delete') }}</button>
                      </div>
                  </div>
              </template>
              </div>
              <div class="accordion">
               
               <div class="mt-1 accordion-item add-new-accordion" @click="__page='add'">
                  <button class="accordion-header" type="button">
                     <p ><span >{{ __('Import Product') }}</span></p>
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