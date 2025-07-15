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
              {{-- <div class="input-box">
                 <label for="text">{{ __('Subtitle') }}</label>
                 <div class="input-group">
                    <x-builder.textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="section.content.subtitle" name="title" placeholder="{{ __('Add text here') }}"></x-builder.textarea>
                 </div>
              </div> --}}

              <template x-if="section.content.booking">
               <div x-data="{service: getBookingService(section.content.booking.booking_id)}">
                  <div class="flex px-[10px] py-[5px] relative [transition:box-shadow_0.25s_ease-out] items-center rounded-[10px] justify-between bg-[#F7F7F7] cursor-pointer hover:bg-[rgb(255,_255,_255)] hover:[box-shadow:rgba(0,_0,_0,_0.2)_0px_8px_20px]">
                     <div class="flex items-center">
                         {{-- <div class="shadow-xl rounded-[10px] w-[30px] h-[30px] ml-[10px] mr-[15px] my-[0] flex items-center justify-center">

                         </div> --}}

                         <span class="text-[12px] font-semibold pl-[0]" x-html="service.name + '+' + service.price_html"></span>
                     </div>

                     <div class="w-[22px] h-[22px] flex items-center rounded-[5px] justify-center bg-[#FFFFFF] [transition:background-color_0.2s_ease-out]" @click="section.content.booking=null">
                         <i class="fi fi-rr-trash text-xs"></i>
                     </div>
                 </div>

                 <div class="mt-1 mb-1 group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed text-center hover:border-solid hover:border-yellow-600 relative" :class="{
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
                           <img :src="$store.builder.getMedia(section.image)" class="h-full w-[100%] object-cover rounded-md" alt="">
                        </div>
                     </template>
                  </div>
                  <div class="grid grid-cols-3 gap-2 mb-1">
                     <div class="flex items-center justify-center flex-col gap-[10px] mb-0 relative w-[100%] h-[90px] border-[1px] border-solid border-[#eef1f5] rounded-[5px] tracking-[-0.2px] cursor-pointer" :class="{
                        'bg-[#eef1f5]': section.content.booking.data.style == 'button'
                     }" @click="section.content.booking.data.style='button'">
                        {!! __i('--ie', 'cursor-button', 'w-7 h-7') !!}
                        <span class="font-normal text-sm">{{ __('Button') }}</span>
                     </div>
                     <div class="flex items-center justify-center flex-col gap-[10px] mb-0 relative w-[100%] h-[90px] border-[1px] border-solid border-[#eef1f5] rounded-[5px] tracking-[-0.2px] cursor-pointer" :class="{
                        'bg-[#eef1f5]': section.content.booking.data.style == 'callout'
                     }" @click="section.content.booking.data.style='callout'">
                        {!! __i('Computers Devices Electronics', 'screen-monitor', 'w-7 h-7') !!}
                        <span class="font-normal text-sm">{{ __('Callout') }}</span>
                     </div>
                     <div class="flex items-center justify-center flex-col gap-[10px] mb-0 relative w-[100%] h-[90px] border-[1px] border-solid border-[#eef1f5] rounded-[5px] tracking-[-0.2px] cursor-pointer" :class="{
                        'bg-[#eef1f5]': section.content.booking.data.style == 'full'
                     }" @click="section.content.booking.data.style='full'">
                        {!! __i('Type, Paragraph, Character', 'media-video', 'w-7 h-7') !!}
                        <span class="font-normal text-sm">{{ __('Full') }}</span>
                     </div>
                  </div>
                  <div class="input-box" :class="{
                     '!hidden': section.content.booking.data.style == 'button'
                  }">
                     <label for="text">{{ __('Title') }}</label>
                     <div class="input-group">
                        <x-builder.textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[65px]" x-model="section.content.booking.data.title" name="title" placeholder="{{ __('Add main heading') }}"></x-builder.textarea>
                     </div>
                  </div>
                  <div class="input-box" :class="{
                     '!hidden': section.content.booking.data.style == 'button'
                  }">
                     <label for="text">{{ __('Subtitle') }}</label>
                     <div class="input-group">
                        <x-builder.textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="section.content.booking.data.subtitle" name="title" placeholder="{{ __('Add text here') }}"></x-builder.textarea>
                     </div>
                  </div>
                  <div class="input-box">
                     <label for="text">{{ __('Button') }}</label>
                     <div class="input-group">
                        <input type="text" class="input-small resizable-textarea blur-body" x-model="section.content.booking.data.button" placeholder="{{ __('Add button text') }}">
                     </div>
                  </div>
               </div>
              </template>
              <template x-if="!section.content.booking">
               <div class="accordion">
                     <div class="mt-1 accordion-item add-new-accordion" @click="__page='add'">
                        <button class="accordion-header" type="button">
                           <p ><span >{{ __('Import Booking') }}</span></p>
                           <span class="plus-icon">
                              {!! __i('interface-essential', 'plus-add.3') !!}
                           </span>
                        </button>
                     </div>
                  </div>
              </template>
           </form>
        </div>
     </div>

</div>