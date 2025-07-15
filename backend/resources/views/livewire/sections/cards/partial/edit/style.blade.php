
<div>
   <div class="style">
      
      <div class="mt-2 banner-text-size">
         <div>
            <div class="style-block site-layout !px-0 !pt-0 !grid-cols-2">
               <button class="btn-layout" type="button" @click="section.settings.style='1'" :class="{
                  'active': section.settings.style == '1'
               }">
                  <span >1</span>
                  <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg" >
                     <path d="M50.124 0.154297H0.124023V24.1543H50.124V0.154297Z" fill="var(--c-mix-2)" ></path>
                     <path d="M50.1241 33.1543L0.124077 33.1543V34.1543L50.1241 34.1543V33.1543Z" fill="var(--c-mix-2)" ></path>
                     <path d="M50.1241 41.1543L0.124123 41.1543L0.124123 42.1543L50.1241 42.1543V41.1543Z" fill="var(--c-mix-2)" ></path>
                     <path d="M0.124352 49.1543L50.1244 49.1543V50.1543L0.124352 50.1543L0.124352 49.1543Z" fill="var(--c-mix-2)" ></path>
                  </svg>
               </button>
               <button class="btn-layout" type="button" @click="section.settings.style='2'" :class="{
                  'active': section.settings.style == '2'
               }">
                  <span >2</span>
                  <svg width="51" height="51" viewBox="0 0 51 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path fill-rule="evenodd" clip-rule="evenodd" d="M0.152344 0.137695H50.1523V50.1377H0.152344V0.137695ZM7.15234 33.1377H43.1523V43.1377H7.15234V33.1377ZM11.6523 37.6377L38.6523 37.6377V38.6377L11.6523 38.6377V37.6377Z" fill="var(--c-mix-2)"></path>
                  </svg>
               </button>
            </div>
         </div>
         <form>
            <div class="input-box" :class="{'!hidden': section.settings.split_title}">
               <label for="text-size">{{ __('Layout') }}</label>

               <template x-if="section.settings.style == '1'">
                  <div class="input-group align-type">
                     <button class="btn-nav" type="button" :class="{'active': section.settings.layout_align == 'top'}" @click="section.settings.layout_align = 'top'">
                        {!! __i('Type, Paragraph, Character', 'horizontal-align-center', '!w-5 !h-5') !!}
                     </button>
                     <button class="btn-nav" type="button" :class="{'active': section.settings.layout_align == 'center'}" @click="section.settings.layout_align = 'center'">
                        {!! __i('Type, Paragraph, Character', 'arrange-top', '!w-5 !h-5') !!}
                     </button>
                     <button class="btn-nav" type="button" :class="{'active': section.settings.layout_align == 'bottom'}" @click="section.settings.layout_align = 'bottom'">
                        {!! __i('--ie', 'align-center', '!w-5 !h-5') !!}
                     </button>
                  </div>
               </template>

               <template x-if="section.settings.style == '2'">
                  <div class="input-group align-type">
                     <button class="btn-nav" type="button" :class="{'active': section.settings.layout_align == 'top'}" @click="section.settings.layout_align = 'top'">
                        {!! __i('Type, Paragraph, Character', 'align-top 2', '!w-5 !h-5') !!}
                     </button>
                     <button class="btn-nav" type="button" :class="{'active': section.settings.layout_align == 'center'}" @click="section.settings.layout_align = 'center'">
                        {!! __i('Type, Paragraph, Character', 'align-center', '!w-5 !h-5') !!}
                     </button>
                     <button class="btn-nav" type="button" :class="{'active': section.settings.layout_align == 'bottom'}" @click="section.settings.layout_align = 'bottom'">
                        {!! __i('Type, Paragraph, Character', 'align-bottom 2', '!w-5 !h-5') !!}
                     </button>
                  </div>
               </template>
            </div>
            
            <div class="input-box" x-data="{__showing: 'desktop'}">
               <label for="text-size">{{__('Grid')}}</label>
               <div class="input-group">
                  <button type="button" class="btn !w-[40px] !h-[40px] !min-w-[40px] !aspect-square !flex !items-center !justify-center !bg-[var(--c-mix-1)] !rounded-tr-none !rounded-br-none" @click="__showing = __showing == 'mobile' ? 'desktop' : 'mobile'">
                     <div x-show="__showing == 'desktop'">
                        {!! __i('Computers Devices Electronics', 'Imac, Computer', 'w-4 h-4 text-[var(--foreground)]') !!}
                     </div>
                     <div x-show="__showing == 'mobile'">
                        {!! __i('Computers Devices Electronics', 'Iphone', 'w-4 h-4 text-[var(--foreground)]') !!}
                     </div>
                  </button>
                  <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'desktop'}" min="1" max="3" step="1" x-model="section.settings.desktop_grid">

                  <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'mobile'}" min="1" max="2" step="1" x-model="section.settings.mobile_grid">

                  <p class="image-size-value" x-text="__showing == 'desktop' ? (section.settings.desktop_grid ? section.settings.desktop_grid : '') : (section.settings.mobile_grid ? section.settings.mobile_grid : '')"></p>
               </div>
            </div>
            
            <div class="input-box" x-data="{__showing: 'desktop'}">
               <label for="text-size">{{__('Height')}}</label>
               <div class="input-group">
                  <button type="button" class="btn !w-[40px] !h-[40px] !min-w-[40px] !aspect-square !flex !items-center !justify-center !bg-[var(--c-mix-1)] !rounded-tr-none !rounded-br-none" @click="__showing = __showing == 'mobile' ? 'desktop' : 'mobile'">
                     <div x-show="__showing == 'desktop'">
                        {!! __i('Computers Devices Electronics', 'Imac, Computer', 'w-4 h-4 text-[var(--foreground)]') !!}
                     </div>
                     <div x-show="__showing == 'mobile'">
                        {!! __i('Computers Devices Electronics', 'Iphone', 'w-4 h-4 text-[var(--foreground)]') !!}
                     </div>
                  </button>
                  <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'desktop'}" min="100" max="500" step="10" x-model="section.settings.desktop_height">

                  <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'mobile'}" min="100" max="500" step="10" x-model="section.settings.mobile_height">
                  
                  <p class="image-size-value" x-text="__showing == 'desktop' ? (section.settings.desktop_height ? section.settings.desktop_height  + 'px' : '') : (section.settings.mobile_height ? section.settings.mobile_height  + 'px' : '')"></p>
               </div>
            </div>
            <div class="input-box">
               <label for="text-size">{{ __('Text') }}</label>
               <div class="input-group align-type">
                  <button class="btn-nav" type="button" :class="{'active': section.settings.text == 's'}" @click="section.settings.text = 's'">
                     S
                  </button>
                  <button class="btn-nav" type="button" :class="{'active': section.settings.text == 'm'}" @click="section.settings.text = 'm'">
                     M
                  </button>
                  <button class="btn-nav" type="button" :class="{'active': section.settings.text == 'l'}" @click="section.settings.text = 'l'">
                     L
                  </button>
               </div>
            </div>
            <div class="input-box" :class="{'!hidden': section.settings.split_title}">
               <label for="text-size">{{ __('Align') }}</label>
               <div class="input-group align-type">
                  <button class="btn-nav" type="button" :class="{'active': section.settings.align == 'left'}" @click="section.settings.align = 'left'">
                     {!! __i('Type, Paragraph, Character', 'align-left') !!}
                  </button>
                  <button class="btn-nav" type="button" :class="{'active': section.settings.align == 'center'}" @click="section.settings.align = 'center'">
                     {!! __i('Type, Paragraph, Character', 'align-center') !!}
                  </button>
                  <button class="btn-nav" type="button" :class="{'active': section.settings.align == 'right'}" @click="section.settings.align = 'right'">
                     {!! __i('Type, Paragraph, Character', 'align-right') !!}
                  </button>
               </div>
            </div>

            <div class="input-box banner-advanced banner-action">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="splitSection-switch" type="checkbox" x-model="section.settings.split_title" class="switchInput">

                     <label for="splitSection-switch" class="switchLabel">{{ __('Split Section') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>
            <div class="input-box banner-advanced banner-action" :class="{'!hidden': section.settings.style !== '1'}">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="bg--image-switch" type="checkbox" x-model="section.settings.enable_image" class="switchInput">

                     <label for="bg--image-switch" class="switchLabel">{{ __('Image') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>
            <div class="input-box banner-advanced banner-action" :class="{'!hidden': section.settings.style !== '1'}">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="bg--switch" type="checkbox" x-model="section.settings.background" class="switchInput" x-on:input="$event.target.checked && section.settings.border ? section.settings.border = false : ''">

                     <label for="bg--switch" class="switchLabel">{{ __('Background') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>
            <div class="input-box banner-advanced banner-action" :class="{'!hidden': section.settings.style !== '1'}">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="border--switch" type="checkbox" x-model="section.settings.border" class="switchInput" x-on:input="$event.target.checked && section.settings.background ? section.settings.background = false : ''">

                     <label for="border--switch" class="switchLabel">{{ __('Border') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>

            <template x-if="section.settings.style == '2'">
               <div class="input-box banner-advanced banner-action">
                  <div class="input-group">
                     <div class="switchWrapper">
                        <input id="bg-title-glass" type="checkbox" x-model="section.settings.glass" class="switchInput">
   
                        <label for="bg-title-glass" class="switchLabel">{{ __('Glass') }}</label>
                        <div class="slider"></div>
                     </div>
                  </div>
               </div>
            </template>
            
            <div class="cursor-pointer input-box banner-advanced banner-action" @click="__page = 'section'">
               <div class="input-group" >
                  <div class="section-background" >
                     <label for="showSection-switch" class="" >{{ __('Section Background') }}</label>
                     <span>
                        {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                     </span>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>