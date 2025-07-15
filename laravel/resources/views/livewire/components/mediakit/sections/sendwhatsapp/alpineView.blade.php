<div>
   <div>



      <div x-data="builder__whatsapp_section_view" wire:ignore>
         <div :class="{
            'custom-styles-link': site.settings.__theme !== 'default'
          }">
          
           <template bit-component="whatsapp-template">
              <div>
                 <div class="generic-card-o overflow-hidden" :class="{
                    '!rounded-none !p-5 !bg-white !text-black': section.settings.style == 'popup'
                 }">
                     <template x-if="section.settings.style !== 'button'">
                        <div class="-header border-b border-gray-100">
                           <div class="--header-inner">
                              <div class="--title" :class="{
                                '!text-black': section.settings.style == 'popup'
                             }" x-text="section.content.heading"></div>
                           </div>
                        </div>
                     </template>
                 
                     <div class="-content" :class="{
                       '!p-0': section.settings.style !== 'card'
                      }">
                         <div class="--content-inner" :class="{
                          '!p-0': section.settings.style !== 'card'
                         }">
                             <div class="-content-body">
  
                                <form @submit.prevent="_submit">
  
                                   <div class="flex flex-col gap-3">
                                       <div>
               
                                           <textarea name="message" class="block flex-grow rounded-r-md border-noen disabled:opacity-60 py-2.5 px-2 text-sm focus:ring-primary focus:border-primary border-gray-300 rounded-md w-full !text-black !bg-[#f7f3f2] !text-black" placeholder="{{ __('Type your message') }}" cols="30" rows="5" x-model="message"></textarea>
                                       </div>
                                   </div>
                                   
                                   <button class="yena-button-stack w-[100%] mt-2 !text-[color:var(--contrast-color)]" x-text="section.content.send_to_whatsapp_button ? section.content.send_to_whatsapp_button : '{{ __('Contact Me') }}'"></button>
                               </form>
                             </div>
                         </div>
                     </div>
                 </div>
              </div>
           </template>
  
           
           <div class="builder-block -whatsapp-block w-[100%] h-full g--block-o" :class="{
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
                       <div>
                          <div class="sandy-accordion m-0" :class="{
                           '--active': expanded
                        }" x-data="{ expanded: false }">
                             <div class="sandy-accordion-head" @click="expanded = ! expanded">
                                 <span x-text="section.content.heading"></span>
                             </div>
                             <div class="sandy-accordion-body mt-5 !pb-0" x-show="expanded" x-collapse>
                                <div x-bit:whatsapp-template></div>
                             </div>
                         </div>
                       </div>
                    </template>
  
                    <template x-if="section.settings.style == 'card'">
                       <div>
                          <div x-bit:whatsapp-template></div>
                       </div>
                    </template>
                    <template x-if="section.settings.style == 'popup'">
                       <div>
       
                          <div class="m-0 p-5 text-sm cursor-pointer" x-on:click="show_modal = true;">
                              <div class="text-match-theme">
                                  <p x-text="section.content.heading"></p>
                                  <p class="--link-text"></p>
                              </div>
                          </div>
                       </div>
                    </template>
                   </div>
                   <div class="--item--bg"></div>
               </div>
           </div>
  
           <template x-if="section.settings.style == 'popup'">
              <template x-teleport="body">
  
                 <div x-cloak x-show="show_modal" x-transition.opacity.duration.500ms class="alpine-dialog-modal flex-col justify-center max-h-screen" :class="{'flex': show_modal}">
  
                    <div class="sandy-dialog-modal bg-transparent" x-show="show_modal" x-cloak>
                       <button class="group absolute right-0 top-0 z-20 m-3 hidden rounded-full p-2 text-gray-500 transition-all duration-75 hover:bg-gray-100 focus:outline-none active:bg-gray-200 md:block" x-on:click="show_modal = false; document.body.classList.remove('open-modal-alpine');">
                          <svg fill="none" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="14" height="14" class="h-5 w-5"><path d="M18 6L6 18"></path><path d="M6 6l12 12"></path></svg>
                       </button>
                       <div class="-alpine-modal-overflow z-40" x-on:click="show_modal = false;  document.body.classList.remove('open-modal-alpine');" x-show="show_modal" x-cloak></div>
                       
                       <div class="sandy-bio-element-dialog sandy-dialog block z-50">
                        <div>
                            <div class="iframe-header">
                                <div class="icon" x-on:click="show_modal = false; document.body.classList.remove('open-modal-alpine');">
                                    <i class="flaticon2-cross"></i>
                                </div>
                            </div>
                        </div>
  
                        <div x-bit:whatsapp-template></div>
                       </div>
                    </div>
                 </div>
              </template>
           </template>
         </div>
      </div>
      @script
      <script>
         Alpine.data('builder__whatsapp_section_view', () => {
            return {
              show_modal: false,
              message: '',
              success: false,
              _submit(){
                  let $this = this;
                  let $el = document.querySelector(`.section-post-${this.section.section} .livewire-comp`);
                  let $wire = Livewire.find($el.getAttribute('wire:id'));
                  $this.success = false;

                  $wire.sendMessage(this.section, this.message).then(r => {
                     $this.success = true;
                  });
              },
               
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
 </div>