
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
         </form>
      </div>
   </div>
</div>