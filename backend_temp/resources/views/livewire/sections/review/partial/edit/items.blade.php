<?php

?>

<div>

   <div class="website-section" x-data="builder__reviews_single">
       <div class="design-navbar">
          <ul >
              <li class="close-header !flex">
                <a @click="__page = '-'">
                  <span>
                      {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                  </span>
                </a>
             </li>
             <li class="!pl-0" x-text="item.content.title">{{ __('Review') }}</li>
             <li class="!flex items-center !justify-center">
               <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" @click="__delete_item(item.uuid)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
            </li>
          </ul>
       </div>
       <div class="container-small p-[var(--s-2)] pb-[150px]">
         <form method="post">
            <div class="input-box mt-1">
               <div class="input-label">{{ __('Review') }}</div>
               <div class="input-group">
                  <x-builder.textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[65px]" x-model="item.content.text" name="title" placeholder="{{ __('Add review description') }}"/>
               </div>
            </div>
            <div class="input-box">
               <div class="input-label">{{ __('Name') }}</div>
               <div class="input-group">
                  <input type="text" class="input-small blur-body" x-model="item.content.title" name="title" placeholder="{{ __('Add name') }}">
               </div>
            </div>
            <div class="input-box">
               <div class="input-label">{{ __('Bio') }}</div>
               <div class="input-group">
                  <input type="text" class="input-small blur-body" x-model="item.content.bio" name="bio" placeholder="{{ __('Add text here') }}">
               </div>
            </div>
            <template x-if="section.settings.type == 'stars'">
               <div class="input-box">
                  <div class="input-label">{{ __('Rating') }}</div>
                  <div class="input-group">
                     <input type="range" class="input-small range-slider" min="0" max="5" step="1" x-model="item.content.rating">
                     <p class="range-value" x-text="item.content.rating+'/'+'5'">4/5</p>
                  </div>
               </div>
            </template>
            
            <div class="relative block h-20 mb-1 text-center border-2 border-dashed rounded-lg cursor-pointer group bg-white- hover:border-solid hover:border-yellow-600" :class="{
               'border-gray-200': !item.content.image,
               'border-transparent': item.content.image,
              }">
               <template x-if="item.content.image">
                  <div class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden w-full h-full group-hover:flex">
                     <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full icon-shadow" @click="item.content.image = ''; $dispatch('reviewsMediaEvent:' + item.uuid, {
                      image: null,
                      public: null,
                      })">
                         <i class="fi fi-rr-trash"></i>
                     </div>
                 </div>
               </template>
               <template x-if="!item.content.image">
                  <div class="flex items-center justify-center w-full h-full" @click="page = 'media'; $dispatch('mediaEventDispatcher', {
                   event: 'reviewsMediaEvent:' + item.uuid, sectionBack:'navigatePage(\'__last_state\')'
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

            <div class="mt-1 input-box">
               <div class="input-label">{{ __('Link') }}</div>
               <div class="input-group button-input-group">

                  <x-builder.input>
                     <div class="relative link-options__main">
                        <input class="input-small main__link" type="text" x-model="item.content.button_link" placeholder="{{ __('Search site or paste link') }}" x-on:input="filter()" >
                     </div>
                  </x-builder.input>
               </div>
            </div>
         </form>
       </div>
    </div>

    @script
    <script>
        Alpine.data('builder__reviews_single', () => {
           return {


            init(){

               var $this = this;
               window.addEventListener("reviewsMediaEvent:" + this.item.uuid, (event) => {
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