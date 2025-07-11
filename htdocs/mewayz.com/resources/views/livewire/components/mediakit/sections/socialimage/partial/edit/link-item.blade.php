<div>
    <div x-data="builder__socialimage_item__single">
      <div x-data="{ tippy: {
            content: () => $refs.template.innerHTML,
            allowHTML: true,
            appendTo: $root,
            maxWidth: 360,
            interactive: true,
            trigger: 'click',
            animation: 'scale',
         } }">
         <div>
            <div class="flex flex-col justify-center bg-white rounded-2xl border-2 border-gray-300 border-dashed p-5 bg-[#F7F7F7] =shadow relative" x-data="{is_delete:false}">
               <div class="card-button p-3 mb-1 flex gap-2 bg-[var(--yena-colors-gray-100)] w-[100%] rounded-lg !hidden" x-cloak @click="$event.stopPropagation();" :class="{
                  '!hidden': !is_delete
                  }">
                  <button class="btn btn-medium neutral !h-8 !flex !items-center !justify-center !rounded-full !text-black !bg-white" type="button" @click="is_delete=!is_delete">{{ __('Cancel') }}</button>
         
                  <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-8 !flex !items-center !justify-center !rounded-full w-[100%]" type="button" @click="__delete_item(item.uuid)">{{ __('Yes, Delete') }}</button>
               </div>
               
               <button class="btn btn-save !w-[24px] !h-[24px] justify-center items-center !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex !absolute right-2 top-2" @click="is_delete=!is_delete" type="button">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
         
                <div class="flex items-center w-[100%]">
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
                            <div class="">
                               <div>
                                    <input placeholder="{{ __('Title') }}" x-model="item.content.title" type="text" class=" text text-gray-600 p-0 overflow-hidden text-ellipsis !min-h-[21px]">
                                </div>
                            </div>
                            <div class="">
                               <div>
                                    <input placeholder="{{ __('Username') }}" x-model="item.content.username" type="text" class=" text text-gray-600 p-0 overflow-hidden text-ellipsis !min-h-[21px]">
                                </div>
                            </div>
                            <div class="">
                                <div class="colors-container">
                                 <div class="input-box !pb-0">
                                    <div class="input-group !border-0">
                                       
                                       <div class="custom-color !block !border-0">
                                          
                                             <div class="input-box !pb-0">
                                                <div class="input-group">
                                                   <input type="color" class="input-small input-color" x-model="item.content.color" :style="{
                                                      'background-color': item.content.color,
                                                      'color': $store.builder.getContrastColor(item.content.color)
                                                      }" maxlength="6" style="">
                                                </div>
                                             </div>
                                          
                                       </div>
                                    </div>
                                 </div>
                              </div>
                            </div>
                         </div>
                       </div>
                    </div>
                </div>

                <div class="">
                   <div>
                     <div class="input-box !mt-1 !mb-2">
                
                       <div class="input-group media-upload">
                          <button class="btn btn-large !bg-white !border !border-solid !border-[var(--c-mix-1)] cursor-pointer !flex !justify-center !items-center !relative !p-0 !text-black" type="button" x-tooltip="tippy">
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
      
                     <x-builder.input>
                        <div class="link-options__main relative">
                           <input placeholder="{{ __('URL or email (required)') }}" x-model="item.content.link" type="text" class="w-[100$] !border-0 !border-b !border-[var(--c-mix-1)] border-solid text text-gray-600 p-0 overflow-hidden text-ellipsis !min-h-[21px]">
                        </div>
                     </x-builder.input>
                    </div>
                </div>
             </div>
         </div>
          <template x-ref="template">
            <div class="yena-menu-list !w-[350px] !max-h-[420px]">
            
               <div class="--navbar">
                   <ul >
                       <li class="close-header !flex">
                           <a @click="console.log(tippy, $root)">
                               <span>
                                 <i class="ph ph-x"></i>
                               </span>
                           </a>
                       </li>
                       <li class="!pl-0">{{ __('Icon') }}</li>
                       <li class="!flex items-center !justify-center">
                           {{-- <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button> --}}
                       </li>
                   </ul>
               </div>
               <form disabled="true" class="my-1">
                  <div class="input-box">
                     <input type="text" x-model="searchQuery" x-on:input="filterSocials()" placeholder="{{ __('Search icon') }}" class="input-small search-input">
                     {{-- <div class="input-icon zoom-icon px-3">
                        {!! __i('--ie', 'search.1', 'w-5 h-5') !!}
                     </div> --}}
                  </div>
               </form>
               <div class="grid grid-cols-3 md:!grid-cols-4">
   
                  <template x-for="(media, index) in icons" :key="index">
                     <div class="p-[12px] flex flex-col justify-center items-center cursor-pointer rounded-lg hover:!bg-[#eee] file-card" :data-name="media" @click="item.content.icon=media;">
                        <label class="w-[100%] h-[calc(100%_-_20px)] relative select-none flex items-center justify-center cursor-pointer">
                           <i :class="media" class="ph"></i>
                        </label>
                     </div>
                  </template>
               </div>
            </div>
          </template>
      </div>
    </div>
    @script
    <script>
        Alpine.data('builder__socialimage_item__single', () => {
           return {


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
               window.addEventListener(`_item_media:${this.item.uuid}`, (event) => {
                  $this.item.image = event.detail.image;
                  $this._save();
               });
            }
           }
         });
    </script>
    @endscript
 </div>