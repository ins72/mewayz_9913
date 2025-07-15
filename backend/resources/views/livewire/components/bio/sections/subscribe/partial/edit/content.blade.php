<?php

?>

<div>
    <div class="mt-2 content">
        <div class="panel-input mb-1 px-[var(--s-2)]">
           <form>

                           
               <div x-init="window.addEventListener(`section_media::${section.uuid}`, (event) => {
                  section.content.image = event.detail.image;
               });"></div>
              <div class="flex flex-col gap-2">

               <div class="input-box">
                  <label>{{ __('Title') }}</label>
                  <div class="input-group">
                     <input type="text" class="input-small"  x-model="section.content.heading" name="title" placeholder="{{ __('Add main heading') }}">
                  </div>
               </div>
                <div class="input-box">
                   <label>{{ __('Button') }}</label>
                   <div class="input-group">
                      <input type="text" class="input-small"  x-model="section.content.button_text" name="title" placeholder="{{ __('Add button text') }}">
                   </div>
                </div>

               <div class="input-box banner-advanced banner-action !border border-solid border-[var(--c-mix-1)]">
                  <div class="input-group">
                     <div class="switchWrapper">
                        <input id="thankYou-switch" type="checkbox" x-model="section.content.custom_thank_you_enable" class="switchInput">
   
                        <label for="thankYou-switch" class="switchLabel">{{ __('Custom Thank You') }}</label>
                        <div class="slider"></div>
                     </div>
                  </div>
               </div>
               <template x-if="section.content.custom_thank_you_enable">
                  <div class="input-box">
                     <label>{{ __('Thank You') }}</label>
                     <div class="input-group">
                        <textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="section.content.custom_thank_you" name="title" placeholder="{{ __('Add thank you message here.') }}"></textarea>
                     </div>
                  </div>
               </template>
               <div class="input-box banner-advanced banner-action !border border-solid border-[var(--c-mix-1)]">
                  <div class="input-group">
                     <div class="switchWrapper">
                        <input id="showConsent-switch" type="checkbox" x-model="section.content.show_consent" class="switchInput">
   
                        <label for="showConsent-switch" class="switchLabel">{{ __('Show Consent Checkbox') }}</label>
                        <div class="slider"></div>
                     </div>
                  </div>
               </div>
               <div class="input-box banner-advanced banner-action !border border-solid border-[var(--c-mix-1)]">
                  <div class="input-group">
                     <div class="switchWrapper">
                        <input id="customConsent-switch" type="checkbox" x-model="section.content.custom_consent_text" class="switchInput">
   
                        <label for="customConsent-switch" class="switchLabel">{{ __('Custom Consent Text') }}</label>
                        <div class="slider"></div>
                     </div>
                  </div>
               </div>
               <div x-show="section.content.custom_consent_text" x-cloak>
                  <div class="input-box">
                     <label>{{ __('Consent') }}</label>
                     <div class="input-group">
                        <textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="section.content.custom_consent" placeholder="{{ __('Add custom consent.') }}"></textarea>
                     </div>
                  </div>
               </div>

              </div>
           </form>
        </div>
     </div>

</div>