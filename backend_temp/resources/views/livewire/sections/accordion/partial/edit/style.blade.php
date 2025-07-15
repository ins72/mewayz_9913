
<div>
   <div class="style">
      
      <div class="mt-2 banner-text-size">
         <form>
         
            <div class="input-box">
               <label for="text-size">{{ __('Icon') }}</label>
               <div class="input-group align-type">
                  <button class="btn-nav !w-[50%]" type="button" @click="section.settings.icon = 'arrow'" :class="{'active': section.settings.icon =='arrow'}">
                     {!! __i('Arrows, Diagrams', 'Arrow.5', '!w-5 !h-5') !!}
                  </button>
                  <button class="btn-nav !w-[50%]" type="button" @click="section.settings.icon = 'plus'" :class="{'active': section.settings.icon =='plus'}">
                     {!! __i('custom', 'plus', '!w-4 !h-4') !!}
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

            <div class="input-box banner-advanced banner-action">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="bg--switch" type="checkbox" x-model="section.settings.background" class="switchInput">

                     <label for="bg--switch" class="switchLabel">{{ __('Background') }}</label>
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