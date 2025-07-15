
<div>
   <div class="style">
      
      <div class="mt-2 banner-text-size">
         <div>
            <div class="style-block site-layout !px-0 !pt-0 !grid-cols-2">
               <button class="btn-layout" type="button" @click="section.settings.style='1'" :class="{
                  'active': section.settings.style == '1'
               }">
                  <span >1</span>
                  <svg viewBox="0 0 51 34" width="30" height="30" xmlns="http://www.w3.org/2000/svg" ><path d="m50.695 16.402h-50v1h50v-1z" fill="var(--c-mix-2)" ></path><path d="m50.695 8.4023-50 1e-5v1l50-1e-5v-1z" fill="var(--c-mix-2)" ></path><path d="m50.695 0.40234-50 4e-6v1l50-1e-5v-1z" fill="var(--c-mix-2)" ></path><path d="m16.695 32.402 34-1e-4v1l-34 1e-4v-1z" fill="var(--c-mix-2)" ></path><path d="m50.695 24.402-34 1e-4v1l34-1e-4v-1z" fill="var(--c-mix-2)" ></path><path d="m0.69548 24.403h9v9h-9v-9z" fill="var(--c-mix-2)" ></path></svg>
               </button>
               <button class="btn-layout" type="button" @click="section.settings.style='2'" :class="{
                  'active': section.settings.style == '2'
               }">
                  <span >2</span>
                  <svg viewBox="0 0 51 41" width="30" height="30" xmlns="http://www.w3.org/2000/svg" ><path d="m50.443 16.703h-50v1h50v-1z" fill="var(--c-mix-2)" ></path><path d="m50.443 8.7031-50 1e-5v1l50-1e-5v-1z" fill="var(--c-mix-2)" ></path><path d="m50.443 0.70312-50 4e-6v1l50-1e-5v-1z" fill="var(--c-mix-2)" ></path><path d="m8.4434 39.703h34v1h-34v-1z" fill="var(--c-mix-2)" ></path><path d="m20.943 24.703h9v9h-9v-9z" fill="var(--c-mix-2)" ></path></svg>
               </button>
            </div>
         </div>
         <form>
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
            
            <div class="input-box" :class="{
               '!hidden': section.items.length <= 3
            }">
               <label for="text-size">{{ __('Display') }}</label>
               <div class="input-group align-type">
                  <button class="btn-nav !w-[50%] active" type="button" @click="section.settings.display = 'grid'" :class="{'active': section.settings.display == 'grid' || !section.settings.display}">
                     {{ __('Grid') }}
                  </button>
                  <button class="btn-nav !w-[50%]" type="button" @click="section.settings.display = 'carousel'" :class="{'active': section.settings.display =='carousel'}">
                     {{ __('Slider') }}
                  </button>
               </div>
            </div>
            <template x-if="section.settings.display == 'carousel'">
               <div class="input-box" x-data="{__showing: 'desktop'}">
                  <label for="text-size">{{__('Width')}}</label>
                  <div class="input-group">
                     <button type="button" class="btn !w-[40px] !h-[40px] !min-w-[40px] !aspect-square !flex !items-center !justify-center !bg-[var(--c-mix-1)] !rounded-tr-none !rounded-br-none" @click="__showing = __showing == 'mobile' ? 'desktop' : 'mobile'">
                        <div x-show="__showing == 'desktop'">
                           {!! __i('Computers Devices Electronics', 'Imac, Computer', 'w-4 h-4 text-[var(--foreground)]') !!}
                        </div>
                        <div x-show="__showing == 'mobile'">
                           {!! __i('Computers Devices Electronics', 'Iphone', 'w-4 h-4 text-[var(--foreground)]') !!}
                        </div>
                     </button>
                     <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'desktop'}" min="40" max="500" step="10" x-model="section.settings.desktop_width">
   
                     <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'mobile'}" min="40" max="500" step="10" x-model="section.settings.mobile_width">
                     
                     <p class="image-size-value" x-text="__showing == 'desktop' ? (section.settings.desktop_width ? section.settings.desktop_width  + 'px' : '') : (section.settings.mobile_width ? section.settings.mobile_width  + 'px' : '')"></p>
                  </div>
               </div>
            </template>
            <template x-if="section.settings.display == 'carousel' && section.settings.auto_scroll">
               <div class="input-box" x-data="{__showing: 'desktop'}">
                  <label for="text-size">{{__('Speed')}}</label>
                  <div class="input-group">
                     <input type="range" class="input-small range-slider !rounded-l-none" min="5" max="50" step="0.1" x-model="section.settings.speed">
   
                     <p class="image-size-value" x-text="section.settings.speed ? section.settings.speed  + 's' : ''"></p>
                  </div>
               </div>
            </template>
            <template x-if="section.settings.display == 'grid' || !section.settings.display">
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
            </template>
            <template x-if="section.settings.avatar">
               <div class="input-box">
                  <label for="text-size">{{ __('Shape') }}</label>
                  <div class="input-group align-type">
                     <button class="btn-nav !w-[50%] active" type="button" @click="section.settings.shape = 'square'" :class="{'active': section.settings.shape =='square'}">
                        {!! __i('Basic Shapes', 'Square') !!}
                     </button>
                     <button class="btn-nav !w-[50%]" type="button" @click="section.settings.shape = 'circle'" :class="{'active': section.settings.shape == 'circle'}">
                        {!! __i('Basic Shapes', 'Circle') !!}
                     </button>
                  </div>
               </div>
            </template>
            
            <template x-if="section.settings.style == '2'">
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
            </template>

            <div class="input-box banner-advanced banner-action">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="splitSection-switch" type="checkbox" x-model="section.settings.split_title" class="switchInput">

                     <label for="splitSection-switch" class="switchLabel">{{ __('Split Section') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>

            <template x-if="section.settings.display == 'carousel'">
               <div class="input-box banner-advanced banner-action">
                  <div class="input-group">
                     <div class="switchWrapper">
                        <input id="autoScroll-switch" type="checkbox" x-model="section.settings.auto_scroll" class="switchInput">

                        <label for="autoScroll-switch" class="switchLabel">{{ __('Auto Scroll') }}</label>
                        <div class="slider"></div>
                     </div>
                  </div>
               </div>
            </template>
            <div class="input-box banner-advanced banner-action">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="switch-avatar" type="checkbox" x-model="section.settings.avatar" class="switchInput">

                     <label for="switch-avatar" class="switchLabel">{{ __('Avatar') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>
            <div class="input-box banner-advanced banner-action">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="switch-rating" type="checkbox" x-model="section.settings.rating" class="switchInput">

                     <label for="switch-rating" class="switchLabel" x-text="section.settings.type == 'quote' ? '{{ __('Quote') }}' : '{{ __('Rating') }}'">{{ __('Rating') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>

            <div class="input-box banner-advanced banner-action">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="bg--switch" type="checkbox" x-model="section.settings.background" class="switchInput" x-on:input="$event.target.checked && section.settings.border ? section.settings.border = false : ''; !$event.target.checked ? section.settings.border = true : '';">

                     <label for="bg--switch" class="switchLabel">{{ __('Background') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>
            <div class="input-box banner-advanced banner-action">
               <div class="input-group">
                  <div class="switchWrapper">

                     <input id="border--switch" type="checkbox" x-model="section.settings.border" class="switchInput" x-on:input="$event.target.checked && section.settings.background ? section.settings.background = false : ''; !$event.target.checked ? section.settings.background = true : '';">

                     <label for="border--switch" class="switchLabel">{{ __('Border') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>
            
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