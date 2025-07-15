<div>
    <div x-data="builder__contact_section_view">
       <div>

        
         <template bit-component="contact-template">
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
                               <div class="textarea-content">
                                   <div class="text" x-html="section.content.text">
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
            </div>
         </template>

         
         <div class="builder-block -contact-block w-[100%] h-full">
 
             <div class="-item-style">
                 <div class="--style">

                  <template x-if="section.settings.style == 'button'">
                     <div>
                        <div class="sandy-accordion m-0" x-data="{ expanded: false }">
                           <div class="sandy-accordion-head" @click="expanded = ! expanded">
                               <span x-text="section.content.heading"></span>
                           </div>
                           <div class="sandy-accordion-body mt-5 !pb-0" x-show="expanded" x-collapse>
                              <div x-bit:contact-template></div>
                           </div>
                       </div>
                     </div>
                  </template>

                  <template x-if="section.settings.style == 'card'">
                     <div>
                        <div x-bit:contact-template></div>
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

                      <div x-bit:contact-template></div>
                     </div>
                  </div>
               </div>
            </template>
         </template>
       </div>
    </div>
    @script
    <script>
       Alpine.data('builder__contact_section_view', () => {
          return {
            show_modal: false,
             
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