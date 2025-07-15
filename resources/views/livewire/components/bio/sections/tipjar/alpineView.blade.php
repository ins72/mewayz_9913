<div>
    <div x-data="builder__tipjar_section_view">
       <div :class="{
         'custom-styles-link': site.settings.__theme !== 'default'
       }">

        
         <template bit-component="tipjar-template">
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
                              <div>
                                 <div x-cloak x-show="checkout">
                                    <div>
                                       <a class="button-reset outline-none cursor-pointer !mb-2 block" x-on:click="checkout = false">
                            
                                          <div class="flex items-center flex-row space-x-2">
                                              <div class="inline-flex items-center justify-center h-10 w-10 rounded-md !bg-[#f7f3f2] rounded-full">
                                                  <i class="fi fi-rr-arrow-small-left text-lg flex !text-black"></i>
                                              </div>
                                          </div>
                                      </a>
                                       <form class="flex flex-col gap-3" @submit.prevent="pay">
                                          <div class="!bg-transparent">
                                             <input type="text" class="block flex-grow rounded-r-md border-noen disabled:opacity-60 py-2 px-2 text-sm focus:ring-primary focus:border-primary border-gray-300 rounded-md w-full !bg-[#f7f3f2] !text-black" x-model="name" placeholder="{{ __('Enter your name') }}">
                                          </div>
                                          <div class="!bg-transparent">
                                             <input type="text" class="block flex-grow rounded-r-md border-noen disabled:opacity-60 py-2 px-2 text-sm focus:ring-primary focus:border-primary border-gray-300 rounded-md w-full !bg-[#f7f3f2] !text-black" x-model="email" placeholder="{{ __('Enter your email') }}">
                                          </div>
                                          <template x-if="backendError">
                                             <div class="bg-red-200 text-[11px] p-1 px-2 rounded-md mt-1">
                                                <div class="flex items-center">
                                                   <div>
                                                      <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                                   </div>
                                                   <div class="flex-grow ml-1 text-xs" x-text="backendError"></div>
                                                </div>
                                             </div>
                                          </template>
                                          <div class="grid grid-cols-2 gap-2">
                                             <a @click="duration='onetime'" class="yena-black-btn !bg-[#f7f3f2] !text-black" :class="{
                                                '!border-2 !border-solid !border-black p-1': duration=='onetime'
                                             }">{{ __('One Time') }}</a>
                                             
                                             <a @click="duration='recurring'" class="yena-black-btn !bg-[#f7f3f2] !text-black" :class="{
                                                '!border-2 !border-solid !border-black p-1': duration=='recurring'
                                             }">{{ __('Monthly') }}</a>
                                          </div>
                                          <button class="yena-black-btn !bg-black !text-white !text-center w-[100%] !justify-center ![--accent:#000]" x-html="'{{ __('Pay') }}' +' '+ currency.code + amount"></button>
                                       </form>
                                    </div>
                                 </div>
                                 <div x-cloak x-show="!checkout">
                                    <template x-if="section.content.prices">
                                       <div class="grid grid-cols-2 gap-2">
                                          <template x-for="(price, index) in item.content.prices" :key="index">
                                             <a @click="amount=price.name; checkout=true;" class="yena-black-btn" x-html="currency.code + price.name"></a>
                                          </template>
                                       </div>
                                    </template>
                                    <template x-if="section.content.custom_amount">
                                       <div>
                                          <div class="flex items-center flex-row gap-[var(--yena-space-3)] !w-full h-[var(--yena-sizes-10)] my-4">
                                             <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Or') }}</span>
                                             <hr class="opacity-60 [border-image:initial] border-solid !w-full border-[var(--yena-colors-blackAlpha-400)]">
                                          </div>
                                          <div class="custom-content-input border-2 border-dashed">
                                             <label class="h-10 !flex items-center px-5" x-html="currency.code"></label>
                                             <input type="text" x-model="amount" placeholder="{{ __('Amount') }}" class="w-[100%] !bg-gray-100">
                                          </div>
                                          <a @click="checkout=true" class="yena-black-btn mt-1" x-html="section.content.button_text ? section.content.button_text : '{{ __('Leave a Tip') }}'"></a>
                                       </div>
                                    </template>
                                 </div>
                              </div>
                           </div>
                       </div>
                   </div>
               </div>
            </div>
         </template>

         
         <div class="builder-block -tipjar-block w-[100%] h-full g--block-o" :class="{
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
                              <div x-bit:tipjar-template></div>
                           </div>
                       </div>
                     </div>
                  </template>

                  <template x-if="section.settings.style == 'card'">
                     <div>
                        <div x-bit:tipjar-template></div>
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

                      <div x-bit:tipjar-template></div>
                     </div>
                  </div>
               </div>
            </template>
         </template>
       </div>
    </div>
    @script
    <script>
       Alpine.data('builder__tipjar_section_view', () => {
          return {
            amount: 0,
            checkout: false,
            show_modal: false,
            success: false,
            backendError: false,
            duration: 'onetime',
            name: '',
            email: '',
            pay(){
               let $this = this;
               let $el = document.querySelector(`.section-post-${this.section.section} .livewire-comp`);
               let $wire = Livewire.find($el.getAttribute('wire:id'));
               $this.success = false;
               $this.backendError = false;

               let data = {
                  email: $this.email,
                  name: $this.name,
                  amount: $this.amount,
                  duration: $this.duration,
               };
               $wire.post(this.section, data).then(r => {
                   if(r.status === 'error'){
                      $this.backendError = r.response;
                   }
                   if(r.status === 'success'){
                      $this.success = true;
                   }
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