<?php

?>

<div>

   <div class="website-section" x-data="builder__gallery_single">
       <div class="design-navbar">
          <ul >
              <li class="close-header !flex">
                <a @click="__page = '-'">
                  <span>
                      {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                  </span>
                </a>
             </li>
             <li class="!pl-0">{{ __('Media') }}</li>
             <li class="!flex items-center !justify-center">
               <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" @click="__delete_item(item.uuid)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
            </li>
          </ul>
       </div>
       <div class="container-small p-[var(--s-2)] pb-[150px]">
         <form method="post">
            
            <div class="relative block h-20 mb-1 text-center border-2 border-dashed rounded-lg cursor-pointer group bg-white- hover:border-solid hover:border-yellow-600" :class="{
               'border-gray-200': !item.content.image,
               'border-transparent': item.content.image,
              }">
               <template x-if="item.content.image">
                  <div class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden w-full h-full group-hover:flex">
                     <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full icon-shadow" @click="item.content.image = ''; $dispatch('galleryMediaEvent:' + item.uuid, {
                      image: null,
                      public: null,
                      })">
                         <i class="fi fi-rr-trash"></i>
                     </div>
                 </div>
               </template>
               <template x-if="!item.content.image">
                  <div class="flex items-center justify-center w-full h-full" @click="page = 'media'; $dispatch('mediaEventDispatcher', {
                   event: 'galleryMediaEvent:' + item.uuid, sectionBack:'navigatePage(\'__last_state\')'
                   })">
                      <div>
                          <span class="m-0 -mt-2 text-black loader-line-dot-dot font-2"></span>
                      </div>
                      <i class="fi fi-ss-plus"></i>
                  </div>
               </template>
               <template x-if="item.content.image">
                  <div class="h-full w-[100%]">
                      <img :src="$store.builder.getMedia(item.content.image)" class="h-full w-[100%] object-cover rounded-md" alt="">
                  </div>
               </template>
            </div>
         </form>
       </div>
    </div>

    @script
    <script>
        Alpine.data('builder__gallery_single', () => {
           return {


            init(){

               var $this = this;
               window.addEventListener("galleryMediaEvent:" + this.item.uuid, (event) => {
                  $this.item.content.image = event.detail.image;
                  // $this.dispatchSections();
                  // $this._save();
               });
            }
           }
         });
    </script>
    @endscript
</div>