<div >
    <div x-data="builder__sectionLinksItem">
        
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
                       <li class="!pl-0">{{ __('Edit Link') }}</li>
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
                    <div>
                        <div class="tab-link">
                            <ul class="tabs">
                            <li class="tab !w-[100%]" @click="__tab = 'content'" :class="{'active': __tab == 'content'}">{{ __('Content') }}</li>
                            <li class="tab !w-[100%]" @click="__tab = 'style'" :class="{'active': __tab == 'style', '!hidden': section.settings.style !== '-'}">{{ __('Style') }}</li>
                            <li class="tab !w-[100%]" @click="__tab = 'animate'" :class="{'active': __tab == 'animate'}">{{ __('Animate') }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content-box">
                        <div class="tab-content">
                            <div x-cloak x-show="__tab == 'content'" data-tab-content>
                                <div class="mt-2 content">
                                    <div class="panel-input mb-1 px-[var(--s-2)]">
                                       <form>
                                          <div class="flex flex-col gap-3">
                            
                                           <div class="input-box !mb-0">
                                              <label>{{ __('Title') }}</label>
                                              <div class="input-group">
                                                  <input type="text" class="input-small"  x-model="item.content.title" name="title" placeholder="{{ __('Add main heading') }}">
                                              </div>
                                           </div>
                            
                                           <div class="input-box !mb-0">
                                            <label>{{ __('Description') }}</label>
                                            <div class="input-group">
                                               <textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="item.content.description" name="title" placeholder="{{ __('Add text here') }}"></textarea>
                                            </div>
                                         </div>
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
                                                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center icon-shadow" @click="item.image = ''; $dispatch('links_item_media:' + item.uuid, {
                                                        image: null,
                                                        public: null,
                                                        })">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </div>
                                                </div>
                                                </template>
                                                <template x-if="!item.image">
                                                <div class="w-full h-full flex items-center justify-center" @click="page = 'media'; $dispatch('mediaEventDispatcher', {
                                                    event: 'links_item_media:' + item.uuid, sectionBack:'navigatePage(\'__last_state\')'
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
                            <div x-cloak x-show="__tab == 'style'" data-tab-content>
                                <form class="px-[var(--s-2)] mt-2">
                                    <div class="input-box">
                                       <label for="text-size">{{ __('Customize') }}</label>
                                       <div class="input-group align-type">
                                          <button class="btn-nav !w-[50%]" type="button" @click="item.content.customize = true" :class="{'active': item.content.customize}">
                                             {{ __('Enable') }}
                                          </button>
                                          <button class="btn-nav !w-[50%]" type="button" @click="item.content.customize = false" :class="{'active': !item.content.customize}">
                                             {{ __('Disable') }}
                                          </button>
                                       </div>
                                    </div>

                                    <div>
                                        <template x-if="item.content.customize">
                                            <div class="flex flex-col gap-2">
                                                <div class="input-box" :class="{
                                                    '!hidden': !item.image
                                                }">
                                                   <label for="text-size">{{ __('Thumbnail') }}</label>
                                                   <div class="input-group align-type">
                                                      <button class="btn-nav !w-[50%]" type="button" @click="item.content.flip_thumbnail = true" :class="{'active': item.content.flip_thumbnail}">
                                                         {{ __('Right') }}
                                                      </button>
                                                      <button class="btn-nav !w-[50%]" type="button" @click="item.content.flip_thumbnail = false" :class="{'active': !item.content.flip_thumbnail}">
                                                         {{ __('Left') }}
                                                      </button>
                                                   </div>
                                                </div>
                                                <div class="input-box" :class="{
                                                    '!hidden': !item.image
                                                }">
                                                    <label for="text-size">{{ __('Thumb Style') }}</label>
                                                    <div class="input-group style w-[100%]">
                                                        <div class="style-block site-layout !p-0 pattern-image !grid-cols-3 w-[100%]">
                                                            <template x-for="(_i, index) in studio.pattern.themes" :key="index">
                                                                <button class="btn-layout" :class="{
                                                                 'active': item.content.thumbnail_pattern == _i
                                                             }" type="button" @click="item.content.thumbnail_pattern = _i">
                                                                 <span x-text="(index + 1)"></span>
                                                                 
                                                                 <div class="w-[100%] h-10 bg-gray-200 p-3 rounded-lg animate__animated animate__slow animate__infinite" :class="{
                                                                         [`--${_i}`]: true,
                                                                         '!bg-white': item.content.thumbnail_pattern == _i
                                                                     }"></div>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="input-box" :class="{
                                                    '!hidden': !item.image
                                                }">
                                                   <label for="text-size">{{__('Thumb Width')}}</label>
                                                   <div class="input-group">
                                                      <input type="range" class="input-small range-slider !rounded-l-none" min="36" max="200" step="5" x-model="item.content.thumb_width">

                                                      <p class="image-size-value" x-text="item.content.thumb_width ? item.content.thumb_width  + 'px' : ''"></p>
                                                   </div>
                                                </div>
                                                <div class="input-box">
                                                   <label for="text-size">{{__('Font Size')}}</label>
                                                   <div class="input-group">
                                                      <input type="range" class="input-small range-slider !rounded-l-none" min="5" max="21" step="1" x-model="item.content.font_size">

                                                      <p class="image-size-value" x-text="item.content.font_size ? item.content.font_size  + 'px' : ''"></p>
                                                   </div>
                                                </div>
                                                <div class="input-box">
                                                   <label for="text-size">{{__('Height')}}</label>
                                                   <div class="input-group">
                                                      <input type="range" class="input-small range-slider !rounded-l-none" min="52" max="150" step="5" x-model="item.content.height">

                                                      <p class="image-size-value" x-text="item.content.height ? item.content.height  + 'px' : ''"></p>
                                                   </div>
                                                </div>
                                                <div class="input-box">
                                                   <label for="text-size">{{__('Color')}}</label>
                                                   <div class="input-group">
                                                    <div class="colors-container w-[100%]">
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
                                                <div class="input-box">
                                                   <label for="text-size">{{__('Corner')}}</label>
                                                   <div class="input-group">
                                                
                                                    <div class="grid grid-cols-3 gap-4 w-[100%]">
                                                        @foreach (['straight', 'round', 'rounded'] as $key)
                                                        <label class="sandy-big-checkbox">
                                                           <input type="radio" class="sandy-input-inner" name="settings[radius]"
                                                           value="{{ $key }}" x-model="item.content.corners">
                                                           <div
                                                              class="checkbox-inner !p-3 !h-10 !border-2 !border-dashed !border-color--hover" :class="{
                                                                '!border-solid !border-black': item.content.corners == '{{ $key }}'
                                                              }">
                                                              <div class="checkbox-wrap">
                                                                 <div class="content">
                                                                    <a class="leo-avatar-o !bg-white !rounded-md">
                                                                       <div class="-avatar-inner !bg-gray-100 !p-0 !flex-none">
                                                                          <div class="--avatar !p-0 !rounded-md !flex !items-center !justify-center">
                                                                             <img src="{{ gs("assets/image/others/corner-$key.png") }}" alt="">
                                                                          </div>
                                                                       </div>
                                                                    </a>
                                                                 </div>
                                                              </div>
                                                           </div>
                                                        </label>
                                                        @endforeach
                                                    </div>
                                                   </div>
                                                </div>
                                                <div class="style">
                                                    <div class="style-block site-layout !grid-cols-3 !p-0 custom-styles-link">
                                                        <template x-for="(theme, index) in studio.links.themes" :key="index">
                                                            <button class="btn-layout" :class="{
                                                                'active': item.content.theme == theme
                                                            }" type="button" @click="item.content.theme = theme">
                                                                <span x-text="(index + 1)"></span>
                                                                
                                                                <div class="w-[100%] h-10 bg-gray-200 p-3 rounded-lg relative"
                                                                     :class="{
                                                                         [`--${theme}`]: true,
                                                                         '!bg-white': item.content.theme == theme
                                                                     }"></div>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                </form>
                                {{-- <x-livewire::components.bio.sections.links.partial.edit.style/> --}}
                            </div>
                            <div x-cloak x-show="__tab == 'animate'" data-tab-content>
                                <div class="style px-[var(--s-2)] mt-2">
                                    <div class="input-box features mb-1">
                                        <div class="input-label">{{ __('Runs') }}</div>
                                        <div class="input-group flex flex-col">
                                            <template x-for="(runs, index) in animation_runs">
                                                <button type="button" class="flex items-center justify-between w-[100%] p-[0.625rem] h-[calc(var(--unit)*_4)] !text-[14px] leading-[1.6] font-normal text-[var(--foreground)] m-0 border-[1px] border-solid border-[#eeee] cursor-pointer transition-none capitalize hover:opacity-70" :class="{
                                                    'bg-[#eee]': item.content.animation_runs == runs
                                                }" @click="item.content.animation_runs = runs">
                                                   <span x-text="runs.replace('-', ' ')"></span>
                                                </button>
                                            </template>
                                        </div>
                                     </div>
                                    <div class="style-block site-layout !grid-cols-3 !p-0">
                                        <template x-for="(animation, index) in animations" :key="index">
                                            <button class="btn-layout" :class="{
                                                'active': item.content.animation == animation
                                            }" type="button" @click="item.content.animation = animation">
                                                <span x-text="(index + 1)"></span>
                                                
                                                <div class="w-[100%] h-10 bg-gray-200 p-3 rounded-lg animate__animated animate__slow animate__infinite"
                                                     :class="{
                                                         [`animate__${animation}`]: true,
                                                         '!bg-white': item.content.animation == animation
                                                     }"></div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            {{-- <div x-cloak x-show="__tab == 'settings'" data-tab-content></div> --}}
                        </div>
                    </div>
                </div>
              </div>
          </div>
        
    </div>


  @script
  <script>
      Alpine.data('builder__sectionLinksItem', () => {
         return {
            __tab: 'content',
            is_delete:false,
            init(){
                let $this = this;
                window.addEventListener(`links_item_media:${$this.item.uuid}`, (event) => {
                    $this.item.image = event.detail.image;
                });
            }
         }
      });
  </script>
  @endscript
</div>