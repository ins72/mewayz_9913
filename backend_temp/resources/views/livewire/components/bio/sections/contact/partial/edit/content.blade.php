<?php

?>

<div>
    <div class="mt-2 content">
        <div class="panel-input mb-1 px-[var(--s-2)]">
           <form>

                           
               <div x-init="window.addEventListener(`section_media::${section.uuid}`, (event) => {
                  section.content.image = event.detail.image;
                  _save();
               });"></div>
              <div class="flex flex-col gap-3">

               <div class="input-box">
                  <label>{{ __('Title') }}</label>
                  <div class="input-group">
                     <input type="text" class="input-small"  x-model="section.content.heading" name="title" placeholder="{{ __('Add main heading') }}">
                  </div>
               </div>

               <div class="input-box">
                  <label>{{ __('Button') }}</label>
                  <div class="input-group">
                     <input type="text" class="input-small" x-model="section.content.contact_me_button" placeholder="{{ __('Add button label') }}">
                  </div>
               </div>


               <div class="advanced-section-settings">
                  <form onsubmit="return false">
                     
                     <div class="input-box open-tab-box">
                        <div class="input-group">
                           <div class="switchWrapper">
                              <input id="showCBTN-switch" x-model="section.content.show_tnk" type="checkbox" class="switchInput">
                              
                              <label for="showCBTN-switch" class="switchLabel">{{__('Custom Thank You Message')}}</label>
                              <div class="slider"></div>
                           </div>
                        </div>
                     </div>
                  </form>
                  <template x-if="section.content.show_tnk">
                     <div>
                        <div class="input-box mt-4">
                           <label>{{ __('Thank you message') }}</label>
                           <div class="input-group">
                              <textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="section.content.custom_thank_you" placeholder="{{ __('Add text here') }}"></textarea>
                           </div>
                        </div>
                     </div>
                  </template>
               </div>

              </div>
           </form>
        </div>
     </div>

</div>