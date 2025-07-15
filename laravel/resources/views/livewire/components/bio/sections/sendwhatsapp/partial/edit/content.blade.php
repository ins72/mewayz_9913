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
                  <label>{{ __('Phone') }}</label>
                  <div class="input-group">
                     <input type="text" class="input-small" x-model="section.content.phone" name="title" placeholder="{{ __('+1xxxxxxxxx') }}">
                  </div>
               </div>
               <div class="input-box">
                  <label>{{ __('Button name') }}</label>
                  <div class="input-group">
                     <input type="text" class="input-small"  x-model="section.content.send_to_whatsapp_button">
                  </div>
               </div>

              </div>
           </form>
        </div>
     </div>

</div>