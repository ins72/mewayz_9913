<div>
    <div x-data="builder__coupon_section_view">
       <div :class="{
         'custom-styles-link': site.settings.__theme !== 'default'
       }">


         <template bit-component="coupon-template">
            <div>

               <div class="generic-card-o overflow-hidden">
                  <template x-if="section.settings.style == 'card'">
                     <div class="-header border-b border-gray-100">
                        <div class="--header-inner">
                           <div class="--title" x-text="section.content.title"></div>
                        </div>
                     </div>
                  </template>
              
                  <div class="-content">
                      <div class="--content-inner" :class="{
                        '!p-0': section.settings.style == 'button'
                      }">
                          <div class="-content-body">
              
                              <div class="h-56 p-2">
                                 <template x-if="!section.content.image">
                                    <div class="default-image p-4 !flex bg-gray-200 items-center justify-center h-full">
                                       {!! __i('--ie', 'broken-link-unlink-attachment-square.1', 'text-gray-400 !w-8 !h-8') !!}
                                    </div>
                                 </template>
                                 <template x-if="section.content.image">
                                    <img :src="$store.builder.getMedia(section.content.image)" class="w-[100%] h-full rounded-lg object-cover">
                                 </template>
                              </div>
                              <div class="text-center my-3">
                                  
                                  <p class="text-[16px] whitespace-pre-line mb-1" x-text="section.content.coupon_title"></p>
              
                                  <p class="text-[13px] whitespace-pre-line" x-text="section.content.coupon_desc"></p>
                              </div>
              
                              <div>
                                  <div class="flex flex-grow flex-col gap-6 overflow-y-auto px-0 py-0">
                                      <div class="flex flex-col gap-4">
                                         <div class="flex flex-col gap-2">
                                            <div class="w-[100%] rounded-md border-2 px-4 py-3 --text-gray-500 shadow-sm copy-url border-dashed cursor-pointer" @click="$clipboard(section.content.coupon_code)">
                                               <div class="flex items-center gap-2 truncate">
                                                  <p class="truncate text-sm mx-auto" x-text="section.content.coupon_code"></p>
                                                  
                  
                                                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke="currentColor" fill="none" style="" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                     <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                                     <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                                  </svg>
                                               </div>
                                            </div>
                                         </div>
                                      </div>
                                   </div>
                              </div>
              
                              <template x-if="section.content.show_coupon_button">
                                 <a class="yena-btn-o mt-2" :href="section.content.link_button_link" target="_blank" x-text="section.content.link_button_text"></a>
                              </template>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
         </template>
         <div class="--not-too-much-radius">


            <div class="builder-block -coupon-block w-[100%] g--block-o h-full" :class="{
               'g--rounded-full': section.settings.style == 'popup'
            }">
        
                <div class="-item-style" :style="{
                  'box-shadow': site.settings.__theme == 'retro' ? `6px 6px 0px 0px ${$store.builder.hexToRgba(site.settings.color, 0.4)}` : false,
               }" :class="{
                  '!rounded-none': site.settings.__theme == 'sand',
                  [`--${site.settings.__theme}`]: site.settings.__theme,
               }">
                    <div class="--style">
        
                        <template x-if="section.settings.style == 'button'">
                           <div class="sandy-accordion m-0" :class="{
                              '--active': expanded
                           }" x-data="{ expanded: false }">
                              <div class="sandy-accordion-head" @click="expanded = ! expanded">
                                  <span x-text="section.content.title"></span>
                              </div>
                              <div class="sandy-accordion-body mt-5 !pb-0" x-show="expanded" x-collapse>
                                 <div x-bit:coupon-template></div>
                              </div>
                          </div>
                        </template>
        
                        <template x-if="section.settings.style !== 'button'">
                           <div x-bit:coupon-template></div>
                        </template>
                    </div>
                    <div class="--item--bg"></div>
                </div>
            </div>
        
        </div>
        
       </div>
    </div>
    @script
    <script>
       Alpine.data('builder__coupon_section_view', () => {
          return {

             
             init(){
                var $this = this;
                window.addEventListener('section::' + this.section.uuid, (event) => {
                   $this.section = event.detail;
                });
             }
          }
       });
    </script>
    @endscript
 </div>