
<div>
   <div class="style">
      
      <div class="mt-2 banner-text-size">
         <div>
            <div class="style-block site-layout !px-0 !pt-0 !grid-cols-2">
               <button class="btn-layout" type="button" @click="section.settings.style='1'" :class="{
                  'active': section.settings.style == '1'
               }">
                  <span >1</span>
                  <svg viewBox="0 0 51 41" xmlns="http://www.w3.org/2000/svg" width="30" height="30"><path d="m50.695 8.6797h-50v1h50v-1z" fill="var(--c-mix-10)"></path><path d="m38.195 0.67969-25 3e-6v1h25v-1z" fill="var(--c-mix-10)"></path><path d="m50.695 16.68h-50v1h50v-1z" fill="var(--c-mix-10)"></path><path d="m50.695 24.68h-50v1h50v-1z" fill="var(--c-mix-10)"></path><path d="m30.695 35.68h-10v5h10v-5z" fill="var(--c-mix-10)"></path></svg>
               </button>
               <button class="btn-layout" type="button" @click="section.settings.style='2'" :class="{
                  'active': section.settings.style == '2'
               }">
                  <span >2</span>
                  <svg viewBox="0 0 51 41" xmlns="http://www.w3.org/2000/svg" width="30" height="30"><path d="m37.943 0.67969-25 3e-6v1h25v-1z" fill="var(--c-mix-10)"></path><path d="m50.443 23.68h-50v1h50v-1z" fill="var(--c-mix-10)"></path><path d="m50.443 31.68h-50v1h50v-1z" fill="var(--c-mix-10)"></path><path d="m50.443 39.68h-50v1h50v-1z" fill="var(--c-mix-10)"></path><path d="m30.443 8.6797h-10v5h10v-5z" fill="var(--c-mix-10)"></path></svg>
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