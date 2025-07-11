
<div>
   <div class="style">
      
      <div class="mt-2 px-[var(--s-2)] banner-text-size">
         <form>
            <div class="input-box">
               <label for="text-size">{{__('Height')}}</label>
               <div class="input-group">
                  <input type="range" class="input-small range-slider !rounded-l-none" min="40" max="500" step="10" x-model="section.settings.desktop_height">
                  
                  <p class="image-size-value" x-text="section.settings.desktop_height ? section.settings.desktop_height  + 'px' : ''"></p>
               </div>
            </div>
            <div class="input-box">
               <label for="text-size">{{__('Width')}}</label>
               <div class="input-group">
                  <input type="range" class="input-small range-slider !rounded-l-none" min="40" max="500" step="10" x-model="section.settings.desktop_width">
                  
                  <p class="image-size-value" x-text="section.settings.desktop_width ? section.settings.desktop_width  + 'px' : ''"></p>
               </div>
            </div>
            <template x-if="section.settings.auto_scroll">
               <div class="input-box" x-data="{__showing: 'desktop'}">
                  <label for="text-size">{{__('Speed')}}</label>
                  <div class="input-group">
                     <input type="range" class="input-small range-slider !rounded-l-none" min="5" max="50" step="0.1" x-model="section.settings.speed">
   
                     <p class="image-size-value" x-text="section.settings.speed ? section.settings.speed  + 's' : ''"></p>
                  </div>
               </div>
            </template>

            <div class="input-box banner-advanced banner-action !border border-solid border-[var(--c-mix-1)]">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="autoScroll-switch" type="checkbox" x-model="section.settings.auto_scroll" class="switchInput">

                     <label for="autoScroll-switch" class="switchLabel">{{ __('Auto Scroll') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>