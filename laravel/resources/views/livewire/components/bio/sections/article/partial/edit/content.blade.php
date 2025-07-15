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

               <div class="mt-0 bg-white pb-32" wire:ignore>
                  <div
                     x-ref="quillEditor"
                     x-init="
                        quill = new window.Quill($refs.quillEditor, {theme: 'snow'});
                        quill.on('text-change', function () {
                           section.content.text = quill.root.innerHTML;
                        });

                        quill.root.innerHTML = section.content.text;
                     "
                  >
                  
                  </div>
               </div>

              </div>
           </form>
        </div>
     </div>

</div>