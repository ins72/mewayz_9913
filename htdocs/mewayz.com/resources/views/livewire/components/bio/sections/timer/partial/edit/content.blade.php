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
                   <label>{{ __('Subtitle') }}</label>
                   <div class="input-group">
                      <textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="section.content.subtitle" name="title" placeholder="{{ __('Add text here') }}"></textarea>
                   </div>
                </div>
                <div class="input-box">
                    <label>{{ __('Date') }}</label>
                    <div class="input-group">
                     <input type="datetime-local" class="input-small" x-model="section.content.date" name="title" placeholder="{{ __('Date') }}">
                    </div>
                 </div>
               </div>

              </div>
           </form>
        </div>
     </div>

</div>