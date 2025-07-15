
<div>
   <div class="style">
      
      <div class="mt-2 banner-text-size">
         <form>
            <div class="input-box">
               <label for="text-size">{{ __('Display') }}</label>
               <div class="input-group align-type">
                  <button class="btn-nav !w-[50%]" type="button" @click="section.settings.display = 'grid'" :class="{'active': section.settings.display =='grid'}">
                     {{ __('Grid') }}
                  </button>
                  <button class="btn-nav !w-[50%]" type="button" @click="section.settings.display = 'carousel'" :class="{'active': section.settings.display =='carousel'}">
                     {{ __('Slider') }}
                  </button>
               </div>
            </div>

            <template x-if="section.settings.display == 'grid'">
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
            <template x-if="section.settings.display == 'carousel'">
               <div class="input-box" x-data="{__showing: 'desktop'}">
                  <label>{{__('Width')}}</label>
                  <div class="input-group">
                     <button type="button" class="btn !w-[40px] !h-[40px] !min-w-[40px] !aspect-square !flex !items-center !justify-center !bg-[var(--c-mix-1)] !rounded-tr-none !rounded-br-none" @click="__showing = __showing == 'mobile' ? 'desktop' : 'mobile'">
                        <div x-show="__showing == 'desktop'">
                           {!! __i('Computers Devices Electronics', 'Imac, Computer', 'w-4 h-4 text-[var(--foreground)]') !!}
                        </div>
                        <div x-show="__showing == 'mobile'">
                           {!! __i('Computers Devices Electronics', 'Iphone', 'w-4 h-4 text-[var(--foreground)]') !!}
                        </div>
                     </button>

                     <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'desktop'}" min="200" max="400" step="10" x-model="section.settings.desktop_width">

                     <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'mobile'}" min="200" max="400" step="10" x-model="section.settings.mobile_width">

                     <p class="image-size-value" x-text="__showing == 'desktop' ? (section.settings.desktop_width ? section.settings.desktop_width  + 'px' : '') : (section.settings.mobile_width ? section.settings.mobile_width  + 'px' : '')"></p>
                  </div>
               </div>
            </template>

            <div class="input-box" x-data="{__showing: 'desktop'}">
               <label for="text-size">{{__('Height')}}</label>
               <div class="input-group">
                  <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'desktop'}" min="200" max="400" step="5" x-model="section.settings.desktop_height">

                  <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'mobile'}" min="200" max="400" step="5" x-model="section.settings.mobile_height">
                  
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