
<div>
   <div class="style">
      
      <div class="mt-2 banner-text-size">
         <form>
         
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
                  <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'desktop'}" min="1" max="4" step="1" x-model="section.settings.desktop_grid">
                  <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'mobile'}" min="1" max="4" step="1" x-model="section.settings.mobile_grid">
                  <p class="image-size-value" x-text="__showing == 'desktop' ? (section.settings.desktop_grid ? section.settings.desktop_grid : '') : (section.settings.mobile_grid ? section.settings.mobile_grid : '')"></p>
               </div>
            </div>
            </template>
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
                  <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'desktop'}" min="40" max="500" step="10" x-model="section.settings.desktop_height">

                  <input type="range" class="input-small range-slider !rounded-l-none" :class="{'!hidden': __showing !== 'mobile'}" min="40" max="500" step="10" x-model="section.settings.mobile_height">
                  
                  <p class="image-size-value" x-text="__showing == 'desktop' ? (section.settings.desktop_height ? section.settings.desktop_height  + 'px' : '') : (section.settings.mobile_height ? section.settings.mobile_height  + 'px' : '')"></p>
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