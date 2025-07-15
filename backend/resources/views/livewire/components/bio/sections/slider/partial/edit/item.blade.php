<div >
    <div x-data="builder__sectionSliderItem">
        
          <div>
            <div class="website-section !block">
                <div class="design-navbar !h-full">
                    <ul >
                        <li class="close-header !flex">
                          <a @click="__page='-'">
                            <span>
                                {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                            </span>
                          </a>
                       </li>
                       <li class="!pl-0">{{ __('Edit Slider') }}</li>
                       <li class="!flex items-center !justify-center">
                           <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" @click="is_delete=!is_delete">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
                       </li>
                    </ul>
                    <div class="card-button p-3 mb-0 flex gap-2 bg-[var(--yena-colors-gray-100)] w-[100%] rounded-none" x-cloak @click="$event.stopPropagation();" :class="{
                        '!hidden': !is_delete
                        }">
                        <button class="btn btn-medium neutral !h-8 !flex !items-center !justify-center !rounded-full !text-black !bg-white" type="button" @click="is_delete=!is_delete">{{ __('Cancel') }}</button>
               
                        <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-8 !flex !items-center !justify-center !rounded-full w-[100%]" type="button" @click="__delete_item(item.uuid)">{{ __('Yes, Delete') }}</button>
                    </div>
                </div>
                
                <div class="container-small sticky">
                    <div class="tab-content-box">
                        <div class="tab-content">
                            <div x-cloak x-show="__tab == 'content'" data-tab-content>
                                <div class="mt-2 content">
                                    <div class="panel-input mb-1 px-[var(--s-2)]">
                                       <form>
                                          <div class="flex flex-col gap-3">
                                         <div class="input-box !mb-0">
                                             <label>{{ __('Link') }}</label>
                                             <div class="input-group">

                                                <x-builder.input>
                                                   <div class="link-options__main relative">
                                                      <input placeholder="{{ __('URL or email (required)') }}" x-model="item.content.link" type="text" class="input-small">
                                                   </div>
                                                </x-builder.input>
                                             </div>
                                          </div>
                                            
                                            <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed text-center hover:border-solid hover:border-yellow-600 relative" :class="{
                                                'border-gray-200': !item.image,
                                                'border-transparent': item.image,
                                            }">
                                                <template x-if="item.image">
                                                <div class="group-hover:flex hidden w-full h-full items-center justify-center absolute right-0 top-0 left-0 bottom-0">
                                                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center icon-shadow" @click="item.image = ''; $dispatch('slider_item_media:' + item.uuid, {
                                                        image: null,
                                                        public: null,
                                                        })">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </div>
                                                </div>
                                                </template>
                                                <template x-if="!item.image">
                                                <div class="w-full h-full flex items-center justify-center" @click="page = 'media'; $dispatch('mediaEventDispatcher', {
                                                    event: 'slider_item_media:' + item.uuid, sectionBack:'navigatePage(\'__last_state\')'
                                                    })">
                                                    <div>
                                                        <span class="loader-line-dot-dot text-black font-2 -mt-2 m-0"></span>
                                                    </div>
                                                    <i class="fi fi-ss-plus"></i>
                                                </div>
                                                </template>
                                                <template x-if="item.image">
                                                <div class="h-full w-[100%]">
                                                    <img :src="$store.builder.getMedia(item.image)" class="h-full w-[100%] object-cover rounded-md" alt="">
                                                </div>
                                                </template>
                                                </div>
                                          </div>
                                       </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
          </div>
        
    </div>


  @script
  <script>
      Alpine.data('builder__sectionSliderItem', () => {
         return {
            __tab: 'content',
            is_delete:false,
            init(){
                let $this = this;
                window.addEventListener(`slider_item_media:${$this.item.uuid}`, (event) => {
                    $this.item.image = event.detail.image;
                });
            }
         }
      });
  </script>
  @endscript
</div>