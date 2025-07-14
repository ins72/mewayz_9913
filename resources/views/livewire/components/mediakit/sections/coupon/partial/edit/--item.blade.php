<div>

    <div x-init="window.addEventListener(`_item_media:${item.uuid}`, (event) => {
        item.image = event.detail.image;
    });"></div>
    <div class="flex items-center justify-center bg-white rounded-2xl border-2 border-gray-300 border-dashed p-5 bg-[#F7F7F7] =shadow relative">
      
      <button class="btn btn-save !w-[24px] !h-[24px] justify-center items-center !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex absolute right-4 top-2" @click="__delete_item(item.uuid)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>

       <div class="MarginRightS FlexCenterCenter handle cursor-grab">
        {!! __i('custom', 'grip-dots-vertical', 'w-3 h-3') !!}
       </div>
       <div class="flex-auto overflow-clip">
          <div class="flex items-start gap-3">
             <div role="button" tabindex="0" class="cursor-pointer bg-gray-200 hover:opacity-70 flex items-center justify-center rounded-8 shrink-0 h-20 w-20" @click="openMedia({
                    event: '_item_media:' + item.uuid,
                    sectionBack:'navigatePage(\'__last_state\')'
                });">
                <template x-if="!item.image">
                    <div class="default-image p-2 !block">
                       {!! __i('--ie', 'image-picture', 'text-gray-400 w-5 h-5') !!}
                    </div>
                 </template>
                 <template x-if="item.image">
                    <img :src="$store.builder.getMedia(item.image)" class="h-full w-[100%] rounded-8 object-cover" alt="">
                 </template>
            </div>
            <div class="flex flex-col gap-2 grow shrink">
               <div >
                  <div>
                       <input placeholder="{{ __('Title') }}" x-model="item.content.title" type="text" class=" text text-gray-600 p-0 overflow-hidden text-ellipsis min-h-[21px]">
                   </div>
               </div>
               <div >
                  <div>
                       <input placeholder="{{ __('Subtitle') }}" x-model="item.content.subtitle" type="text" class=" text text-gray-600 p-0 overflow-hidden text-ellipsis min-h-[21px]">
                   </div>
               </div>
            </div>
          </div>
       </div>
    </div>
 </div>