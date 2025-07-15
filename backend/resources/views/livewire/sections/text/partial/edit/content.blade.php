<?php

?>

<div wire:ignore>
    <div class="mt-2 content">
        <div class="panel-input mb-1 px-[var(--s-2)]">
           <form>
              <div class="input-box">
                 <label for="text">{{ __('Label') }}</label>
                 <div class="input-group">
                    <input type="text" class="input-small blur-body" x-model="section.content.label" name="title" placeholder="{{ __('Add label') }}">
                 </div>
              </div>
              <div class="input-box">
                 <label for="text">{{ __('Title') }}</label>
                 <div class="input-group">
                    <x-builder.textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[65px]" x-model="section.content.title" name="title" placeholder="{{ __('Add main heading') }}"></x-builder.textarea>
                 </div>
              </div>
              <div class="">
                  <textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="section.content.subtitle" name="title" placeholder="{{ __('Add text here') }}" x-ref="textarea_markdown"></textarea>
              </div>
           </form>
        </div>
     </div>

</div>