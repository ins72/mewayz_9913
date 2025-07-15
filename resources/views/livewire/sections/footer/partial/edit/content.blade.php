<div>
    <div class="mt-2 content">
        <div class="panel-input mb-1 px-[var(--s-2)] box-features">
           <form>
               <div class="input-box popular-price mt-1">
                  <div class="input-group">
                     <div class="switchWrapper">
                        <input id="showFooter__Logo" type="checkbox" class="switchInput" x-model="site.footer.enable_logo">
                        <label for="showFooter__Logo" class="switchLabel">{{ __('Site Logo') }}</label>
                        <div class="slider"></div>
                     </div>
                  </div>
               </div>
           </form>
        </div>

        <div class="form-action !block">
           <div class="cursor-pointer input-box !mb-0" @click="__page='text'; clearTimeout(autoSaveTimer);">
              <div class="input-group !rounded-b-none">
                 <div class="input-chevron" >
                    <label>{{ __('Text') }}</label>
                    <span>
                       {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                    </span>
                 </div>
              </div>
           </div>

           <div class="cursor-pointer input-box !mb-0" @click="__page='groups'; clearTimeout(autoSaveTimer);">
              <div class="input-group !rounded-t-none !border-t-0 !rounded-b-none">
                 <div class="input-chevron" >
                    <label>{{ __('Links') }}</label>
                    <span>
                       {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                    </span>
                 </div>
              </div>
           </div>

           <div class="cursor-pointer input-box !mb-0" @click="__page='buttons'; clearTimeout(autoSaveTimer);">
              <div class="input-group !rounded-t-none !border-t-0 !rounded-b-none">
                 <div class="input-chevron" >
                    <label>{{ __('Buttons') }}</label>
                    <span>
                       {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                    </span>
                 </div>
              </div>
           </div>

           <div class="cursor-pointer input-box !mb-0" @click="__page='socials'; clearTimeout(autoSaveTimer);">
              <div class="input-group !rounded-t-none !border-t-0 !rounded-b-none">
                 <div class="input-chevron" >
                    <label>{{ __('Social') }}</label>
                    <span>
                       {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                    </span>
                 </div>
              </div>
           </div>

           <div class="cursor-pointer input-box !mb-0" @click="__page='copyright'; clearTimeout(autoSaveTimer);">
              <div class="input-group !rounded-t-none !border-t-0">
                 <div class="input-chevron" >
                    <label>{{ __('Copyright') }}</label>
                    <span>
                       {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                    </span>
                 </div>
              </div>
           </div>


        </div>
     </div>

</div>