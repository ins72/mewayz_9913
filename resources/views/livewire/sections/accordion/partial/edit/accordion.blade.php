<?php

?>

<div>

   <div class="website-section">
       <div class="design-navbar">
          <ul >
              <li class="close-header !flex">
                <a @click="__page = '-'">
                  <span>
                      {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                  </span>
                </a>
             </li>
             <li class="!pl-0" x-text="item.content.title"></li>
             <li class="!flex items-center !justify-center">
               <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" @click="__delete_item(item.uuid)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
            </li>
          </ul>
       </div>
       <div class="container-small p-[var(--s-2)] pb-[150px]">
         <form method="post">
            <div class="input-box">
               <div class="input-label">{{ __('Title') }}</div>
               <div class="input-group">
                  <input type="text" class="input-small" placeholder="{{ __('Add card title') }}" x-model="item.content.title">
               </div>
            </div>
            <div class="input-box">
               <div class="input-label">{{ __('Text') }}</div>
               <div class="input-group">
                  <textarea class="input-small resizable-textarea !h-[42px] overflow-y-hidden" name="title" placeholder="{{ __('Add card description') }}" x-model="item.content.text"></textarea>
               </div>
            </div>
         </form>
       </div>
    </div>

</div>