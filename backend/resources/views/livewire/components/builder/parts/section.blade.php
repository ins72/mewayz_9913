<div>

    <div class="website-section" x-data="builder__section_part">
        <div class="design-navbar">
           <ul >
               <li class="close-header !flex">
                 <a @click="__page='-'">
                   <span>
                       {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                   </span>
                 </a>
              </li>
              <li class="!pl-0">{{ __('Section') }}</li>
              <li></li>
           </ul>
        </div>
        <div class="container-small p-[var(--s-2)] pb-[150px]">
            <form onsubmit="return false">
                <div class="input-box">
                   <div class="input-label">{{ __('Color') }}</div>
                   <div class="input-group btns" id="color">
                      <button class="btn-nav" :class="{
                         'active' : section.section_settings !== null && section.section_settings.color == 'transparent',
                      }" type="button" @click="section.section_settings.color = 'transparent'; section.section_settings.width = 'fill';">
                         {!! __i('--ie', 'delete-disabled-ross-hexagon.1', 'w-5 h-5') !!}
                      </button>
                      <button class="btn-nav" :class="{
                         'active' :  section.section_settings !== null && section.section_settings.color == 'default',
                      }" type="button" @click="section.section_settings.color = 'default'">
                        <span class="w-4 h-4 rounded-full bg-[#ccc]"></span>
                      </button>
                      <button class="btn-nav" :class="{
                         'active' :  section.section_settings !== null && section.section_settings.color == 'accent',
                      }" type="button" @click="section.section_settings.color = 'accent'">
                        <span class="w-4 h-4 rounded-full bg-[var(--accent)]"></span>
                      </button>
                   </div>
                </div>

                <template x-if="section.section_settings.image">
                  <div class="input-box">
                     <div class="input-label">{{ __('Text') }}</div>
                     <div class="input-group btns two-col-btns">
                       <button class="btn" :class="{
                          'active' : section.section_settings !== null && section.section_settings.text_color == 'light',
                       }" type="button" @click="section.section_settings.text_color = 'light'">{{ __('Light') }}</button>
                       <button class="btn" :class="{
                          'active' : section.section_settings !== null && section.section_settings.text_color == 'dark' || !section.section_settings.text_color,
                       }" type="button" @click="section.section_settings.text_color = 'dark'">{{ __('Dark') }}</button>
                    </div>
                  </div>
                </template>
                
                <template x-if="section.section !== 'pricing'">
                  <div class="relative block h-20 mb-1 text-center border-2 border-dashed rounded-lg cursor-pointer group bg-white- hover:border-solid hover:border-yellow-600" :class="{
                     'border-gray-200': !section.section_settings.image,
                     'border-transparent': section.section_settings.image,
                    }">
                     <template x-if="section.section_settings.image">
                        <div class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden w-full h-full group-hover:flex">
                           <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full icon-shadow" @click="section.section_settings.image = ''; $dispatch('sectionSettingsMediaEvent:' + section.uuid, {
                            image: null,
                            public: null,
                            })">
                               <i class="fi fi-rr-trash"></i>
                           </div>
                       </div>
                     </template>
                     <template x-if="!section.section_settings.image">
                        <div class="flex items-center justify-center w-full h-full" @click="page = 'media'; $dispatch('mediaEventDispatcher', {
                         event: 'sectionSettingsMediaEvent:' + section.uuid, sectionBack:'navigatePage(\'__last_state\')'
                         })">
                            <div>
                                <span class="m-0 -mt-2 text-black loader-line-dot-dot font-2"></span>
                            </div>
                            <i class="fi fi-ss-plus"></i>
                        </div>
                     </template>
                     <template x-if="section.section_settings.image">
                        <div class="h-full w-[100%]">
                            <img :src="$store.builder.getMedia(section.section_settings.image)" class="h-full w-[100%] object-cover rounded-md" alt="">
                        </div>
                     </template>
                     </div>
                </template>
                 
                <div class="input-box">
                   <div class="input-label">{{ __('Height') }}</div>
                   <div class="input-group btns two-col-btns">
                     <button class="btn" :class="{
                        'active' : section.section_settings !== null && section.section_settings.height == 'fill',
                     }" type="button" @click="section.section_settings.height = 'fill'">{{ __('Fill') }}</button>
                     <button class="btn" :class="{
                        'active' : section.section_settings !== null && section.section_settings.height == 'fit',
                     }" type="button" @click="section.section_settings.height = 'fit'">{{ __('Fit') }}</button>
                  </div>
                </div>
                
               <template x-if="section.section_settings.image || section.section_settings.color !== 'transparent'">
                <div class="input-box">
                   <div class="input-label">{{ __('Width') }}</div>
                   <div class="input-group btns two-col-btns">
                     <button class="btn" :class="{
                        'active' : section.section_settings !== null && section.section_settings.width == 'fill',
                     }" type="button" @click="section.section_settings.width = 'fill'">{{ __('Fill') }}</button>
                     <button class="btn" :class="{
                        'active' : section.section_settings !== null && section.section_settings.width == 'fit',
                     }" type="button" @click="section.section_settings.width = 'fit'">{{ __('Fit') }}</button>
                  </div>
                </div>
               </template>
                

               <template x-if="section.section_settings !== null && section.section_settings.image && section.section_settings.overlay && section.section_settings.color !== 'transparent'">
                  <div class="input-box">
                     <div class="input-label">{{__('Overlay')}}</div>
                     <div class="input-group btns">
                        <button class="btn" type="button" :class="{'active': section.section_settings !== null && section.section_settings.overlay_size == 's'}" @click="section.section_settings.overlay_size = 's'">S</button>
                        <button class="btn" type="button" :class="{'active': section.section_settings !== null && section.section_settings.overlay_size == 'm'}" @click="section.section_settings.overlay_size = 'm'">M</button>
                        <button class="btn" type="button" :class="{'active': section.section_settings !== null && section.section_settings.overlay_size == 'l'}" @click="section.section_settings.overlay_size = 'l'">L</button>
                     </div>
                  </div>
               </template>

               <template x-if="section.section_settings !== null && section.section_settings.blur && section.section_settings.image && !section.section_settings.parallax">
                  <div class="input-box">
                     <div class="input-label">{{__('Blur')}}</div>
                     <div class="input-group btns">
                        <button class="btn" type="button" :class="{'active': section.section_settings !== null && section.section_settings.blur_size == 's'}" @click="section.section_settings.blur_size = 's'">S</button>
                        <button class="btn" type="button" :class="{'active': section.section_settings !== null && section.section_settings.blur_size == 'm'}" @click="section.section_settings.blur_size = 'm'">M</button>
                        <button class="btn" type="button" :class="{'active': section.section_settings !== null && section.section_settings.blur_size == 'l'}" @click="section.section_settings.blur_size = 'l'">L</button>
                     </div>
                  </div>
               </template>

               <template x-if="section.section_settings !== null && section.section_settings.height == 'fit'">
                  <div class="input-box">
                     <div class="input-label">{{__('Spacing')}}</div>
                     <div class="input-group btns">
                        <button class="btn" type="button" :class="{'active': section.section_settings !== null && section.section_settings.spacing == 's'}" @click="section.section_settings.spacing = 's'">S</button>
                        <button class="btn" type="button" :class="{'active': section.section_settings !== null && section.section_settings.spacing == 'm'}" @click="section.section_settings.spacing = 'm'">M</button>
                        <button class="btn" type="button" :class="{'active': section.section_settings !== null && section.section_settings.spacing == 'l'}" @click="section.section_settings.spacing = 'l'">L</button>
                        <button class="btn" type="button" :class="{'active': section.section_settings !== null && section.section_settings.spacing == 'xl'}" @click="section.section_settings.spacing = 'xl'">XL</button>
                     </div>
                  </div>
               </template>

               <template x-if="section.section_settings !== null && section.section_settings.height == 'fill'">
                  <div class="input-box">
                     <div class="input-label">{{__('Align')}}</div>
                     <div class="input-group btns">
                        <button class="btn-nav" :class="{'active': section.section_settings !== null && section.section_settings.align == 'top'}" type="button" @click="section.section_settings.align = 'top'">
                           {!! __i('Type, Paragraph, Character', 'align-left') !!}
                        </button>
                        <button class="btn-nav" :class="{'active': section.section_settings !== null && section.section_settings.align == 'center'}" type="button" @click="section.section_settings.align = 'center'">
                           {!! __i('Type, Paragraph, Character', 'align-center') !!}
                        </button>
                        <button class="btn-nav" :class="{'active': section.section_settings !== null && section.section_settings.align == 'bottom'}" type="button" @click="section.section_settings.align = 'bottom'">
                           {!! __i('Type, Paragraph, Character', 'align-right') !!}
                        </button>
                     </div>
                  </div>
               </template>
               
               <template x-if="section.section_settings.image">
                  <div class="advanced-section-settings">
                     <form onsubmit="return false">
                        <div class="input-box open-tab-box" :class="{'!hidden': section.section_settings.color == 'transparent'}">
                           <div class="input-group">
                              <div class="switchWrapper">
                                 <input id="showOverlay-switch" x-model="section.section_settings.overlay" type="checkbox" class="switchInput">
                                 
                                 <label for="showOverlay-switch" class="switchLabel">{{ __('Overlay') }}</label>
                                 <div class="slider"></div>
                              </div>
                           </div>
                        </div>
                        <div class="input-box open-tab-box" :class="{'!hidden': section.section_settings.parallax}">
                           <div class="input-group">
                              <div class="switchWrapper">
                                 <input id="showBlur-switch" x-model="section.section_settings.blur" type="checkbox" class="switchInput">
                                 
                                 <label for="showBlur-switch" class="switchLabel">{{ __('Blur') }}</label>
                                 <div class="slider"></div>
                              </div>
                           </div>
                        </div>
                        <div class="input-box open-tab-box">
                           <div class="input-group">
                              <div class="switchWrapper">
                                 <input id="showGreyscale-switch" x-model="section.section_settings.greyscale" type="checkbox" class="switchInput">
                                 
                                 <label for="showGreyscale-switch" class="switchLabel">{{ __('Greyscale') }}</label>
                                 <div class="slider"></div>
                              </div>
                           </div>
                        </div>
                        <div class="input-box open-tab-box">
                           <div class="input-group">
                              <div class="switchWrapper">
                                 <input id="showParallax-switch" x-model="section.section_settings.parallax" type="checkbox" class="switchInput">
                                 
                                 <label for="showParallax-switch" class="switchLabel">{{ __('Parallax') }}</label>
                                 <div class="slider"></div>
                              </div>
                           </div>
                        </div>
                     </form>
                  </div>
               </template>
             </form>
        </div>
     </div>
      @script
      <script>
         Alpine.data('builder__section_part', () => {
            return {


               init(){
                  var $this = this;
                  window.addEventListener("sectionSettingsMediaEvent:" + this.section.uuid, (event) => {
                     this.section.section_settings.image = event.detail.image;
                     $this._save();
                  });
               }
            }
         });
      </script>
      @endscript
</div>
