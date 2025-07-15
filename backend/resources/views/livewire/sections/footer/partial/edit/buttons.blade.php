<div>

    <div class="website-section">
        <div class="design-navbar">
           <ul >
               <li class="close-header !flex">
                  <a @click="__page='-'">
                   <span>
                       {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                   </span>
                 </a>
              </li>
              <li class="!pl-0">{{ __('Buttons') }}</li>
              <li></li>
           </ul>
        </div>
        <div class="container-small p-[var(--s-2)] pb-[150px]">
            <form class="form-panel" action="" method="post" onsubmit="return false">
               <div class="input-box">
                  <div class="input-label">{{ __('Button 1') }}</div>
                  <div class="input-group button-input-group">
                     <input type="text" class="input-small button-text" x-model="site.footer.button_one_text" placeholder="{{ __('Button 1 Text') }}">
                     <div class="link-options__container second mt-1">
                        
                        <x-builder.input>
   
                           <div class="link-options__main relative">
                              <input class="input-small main__link" type="text" x-model="site.footer.button_one_link" placeholder="{{ __('Search site or paste link') }}" x-on:input="filter()">
                           </div>
                        </x-builder.input>
                     </div>
                  </div>
               </div>
               <div class="input-box mt-1">
                  <div class="input-label">{{ __('Button 2') }}</div>
                  <div class="input-group button-input-group">
                     <input type="text" class="input-small button-text" x-model="site.footer.button_two_text" placeholder="{{ __('Button 2 Text') }}">
                     <div class="link-options__container second mt-1">
                        
                        <x-builder.input>
                           <div class="link-options__main relative">
                              <input class="input-small main__link" type="text" x-model="site.footer.button_two_link" placeholder="{{ __('Search site or paste link') }}" x-on:input="filter()">
                           </div>
                        </x-builder.input>
                     </div>
                  </div>
               </div>
            </form>
        </div>
     </div>
</div>
