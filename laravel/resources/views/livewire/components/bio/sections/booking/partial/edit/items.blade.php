<?php

?>

<div>

   <div x-data="builder__lists_single">
      <div x-show="!selectIcon">
         <div class="website-section">
            <div class="design-navbar">
               <ul >
                   <li class="close-header !flex">
                     <a @click="__page = '-'">
                       <span>
                           {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                       </span>
                     </a>
                  </li>
                  <li class="!pl-0" x-text="item.content.title">{{ __('List') }}</li>
                  <li class="!flex items-center !justify-center">
                    <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" @click="__delete_item(item.uuid)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
                 </li>
               </ul>
            </div>
            <div class="container-small p-[var(--s-2)] pb-[150px]">
              <form method="post">
                 <div class="input-box">
                    <div class="input-label">{{ __('Title') }}</div>
                    <div class="input-group">
                       <input type="text" class="input-small blur-body" x-model="item.content.title" name="title" placeholder="{{ __('Add list title') }}">
                    </div>
                 </div>
                 <div class="input-box mt-1">
                    <div class="input-label">{{ __('Text') }}</div>
                    <div class="input-group">
                       <x-builder.textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[65px]" x-model="item.content.text" name="title" placeholder="{{ __('Add list description') }}"/>
                    </div>
                 </div>
     
                 <div class="input-box mt-1">
                    <div class="input-label">{{ __('Icon type') }}</div>
                    <div class="input-group two-col">
                       <button class="btn-nav active" type="button" :class="{
                          'active': item.content.icon_type == 'image' || !item.content.icon_type,
                       }" @click="item.content.icon_type = 'image'">{{ __('Image') }}</button>
     
                       <button class="btn-nav" type="button" :class="{
                          'active': item.content.icon_type == 'icon',
                       }" @click="item.content.icon_type = 'icon'">{{ __('Icon') }}</button>
                    </div>
                 </div>
     
                 <template x-if="item.content.icon_type == 'icon'">
                    <div class="input-box mt-1">
                       <div class="input-label">{{ __('Icon') }}</div>
                       <div class="input-group media-upload">
                          <button class="btn btn-large !bg-white shadow-lg cursor-pointer !flex !justify-center !items-center !relative !p-0 !text-black" type="button" @click="selectIcon=true">
                           <template x-if="!item.content.icon">
                             <label class="image-picker">
                              <i class="ph ph-image text-xl"></i>
                             </label>
                           </template>
                           <template x-if="item.content.icon">
                             <label class="image-picker">
                              <i :class="item.content.icon" class="ph text-xl"></i>
                             </label>
                           </template>


                             <span class="remove-image flex-grow h-full flex items-center justify-center rounded-tl-[0] rounded-br-[4px] rounded-tr-[4px] rounded-bl-[0] bg-[var(--background)]" @click="$event.stopPropagation(); item.content.icon=null;">
                                 <template x-if="!item.content.icon">
                                    <i class="ph ph-upload-simple"></i>
                                 </template>
                                 <template x-if="item.content.icon">
                                    <i class="ph ph-trash text-red-400"></i>
                                 </template>
                             </span>
                          </button>
                       </div>
                    </div>
                 </template>
     
                 <template x-if="item.content.icon_type == 'image' || !item.content.icon_type">
                    <div class="relative block h-20 mb-1 text-center border-2 border-dashed rounded-lg cursor-pointer group bg-white- hover:border-solid hover:border-yellow-600" :class="{
                       'border-gray-200': !item.content.image,
                       'border-transparent': item.content.image,
                      }">
                       <template x-if="item.content.image">
                          <div class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden w-full h-full group-hover:flex">
                             <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full icon-shadow" @click="item.content.image = ''; $dispatch('listsMediaEvent:' + item.uuid, {
                              image: null,
                              public: null,
                              })">
                                 <i class="fi fi-rr-trash"></i>
                             </div>
                         </div>
                       </template>
                       <template x-if="!item.content.image">
                          <div class="flex items-center justify-center w-full h-full" @click="page = 'media'; $dispatch('mediaEventDispatcher', {
                           event: 'listsMediaEvent:' + item.uuid, sectionBack:'navigatePage(\'__last_state\')'
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
                 </template>
     
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
      </div>
      

      <template x-if="selectIcon">

         <div>
            <div class="media-section">
               <div class="header-navbar">
                  <ul >
                     <li class="close-header !flex">
                        <a @click="selectIcon=false">
                        <span>
                              {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                        </span>
                        </a>
                     </li>
                     <li class="!pl-0">{{ __('Add Icon') }}</li>
                     <li></li>
                  </ul>
               </div>
               <div class="container-small p-[var(--s-2)] pb-[150px] tab-content-box">
                  <div class="tab-content">
                     <div class="active" data-tab-content>
                        <div class="icon-library">
                           <div class="upload-manager">
                              <form disabled="true">
                                 <div class="input-box">
                                    <input type="text" x-model="searchQuery" x-on:input="filterSocials()" placeholder="{{ __('Search icon') }}" class="input-small search-input">
                                    <div class="input-icon zoom-icon">
                                       {!! __i('--ie', 'search.1', 'w-5 h-5') !!}
                                    </div>
                                 </div>
                              </form>
   
                              
                             <div class="files mt-2">
   
                                 <template x-for="(media, index) in icons" :key="index">
                                    <div class="file-card" :data-name="media" @click="item.content.icon=media;">
                                       <label>
                                          <i :class="media" class="ph"></i>
                                       </label>
                                    </div>
                                 </template>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
          </div>
      </template>
   </div>

    @script
    <script>
        Alpine.data('builder__lists_single', () => {
           return {
            selectIcon:false,
            icons: {!! collect(config('yena.phosphoricons'))->toJson() !!},
            searchQuery: '',
            filterSocials(){
                var __ = this;
                var items = this.$root.querySelectorAll('.file-card');
                var searchQuery = this.searchQuery.toLowerCase();
                items.forEach(item => {
                    var _name = item.getAttribute('data-name');
                    
                    if (_name.indexOf(searchQuery) == -1) {
                        item.classList.add('!hidden');
                    }else { item.classList.remove('!hidden') }
                });
            },

            init(){

               var $this = this;
               window.addEventListener("listsMediaEvent:" + this.item.uuid, (event) => {
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