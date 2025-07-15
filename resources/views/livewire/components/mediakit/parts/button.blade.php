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
              <li class="!pl-0">{{ __('Button') }}</li>
              <li></li>
           </ul>
        </div>
        <div class="container-small p-[var(--s-2)] pb-[150px]">
         <form class="form-panel" action="" method="post" onsubmit="return false">
            <div class="input-box" x-data="{
               tippy: {
                  content: () => $refs.template.innerHTML,
                  allowHTML: true,
                  appendTo: document.body,
                  maxWidth: 360,
                  interactive: true,
                  trigger: 'click',
                  animation: 'scale',
               }
            }">
            <template x-ref="template">
               <div class="yena-menu-list !w-[100%]">
                  <a href="" class="yena-menu-list-item">
                     <div class="--icon">
                        {!! __icon('interface-essential', 'browser-web-loading', 'w-5 h-5') !!}
                     </div>
                     <span>{{ __('Webpage') }}</span>
                  </a>
                  <a href="" class="yena-menu-list-item">
                     <div class="--icon">
                        {!! __icon('Computers Devices Electronics', 'Phone', 'w-5 h-5') !!}
                     </div>
                     <span>{{ __('Phone') }}</span>
                  </a>
                  
                  <a href="" class="yena-menu-list-item">
                     <div class="--icon">
                        {!! __icon('emails', 'Email, @, Mail, Mail icon', 'w-5 h-5') !!}
                     </div>
                     <span>{{ __('Email') }}</span>
                  </a>
                  
               </div>
            </template>
               <div class="input-label">{{ __('Button 1') }}</div>
               <div class="input-group button-input-group">
                  <input type="text" class="input-small button-text" x-model="section.settings.button_one_text" placeholder="{{ __('Button 1 Text') }}">
                  <div class="link-options__container second mt-1">
                     
                     <x-builder.input input="section.settings.lol">

                        <div class="link-options__main relative">
                           <input class="input-small main__link" type="text" x-model="section.settings.button_one_link" placeholder="{{ __('Search site or paste link') }}" x-on:input="filter()">
{{--                            
                           <span class="w-[40px] h-[40px] flex justify-center items-center absolute cursor-pointer right-[0] top-[0] !hidden" x-tooltip="tippy">
                              {!! __icon('interface-essential', 'dots-menu', 'w-6 h-6') !!}
                           </span> --}}
                        </div>
                     </x-builder.input>
                     {{-- <div class="link-options__main relative">
                        <input class="input-small main__link" type="text" placeholder="{{ __('Search site or paste link') }}">
                        
                        <span class="w-[40px] h-[40px] flex justify-center items-center absolute cursor-pointer right-[0] top-[0] !hidden" x-tooltip="tippy">
                           {!! __icon('interface-essential', 'dots-menu', 'w-6 h-6') !!}
                        </span>
                     </div> --}}
                     <div class="link-options__support link mt-1 !hidden">
                        <div class="input-group open-in-new-tab">
                           <div class="switchWrapper">
                              <input id="input-newtab" type="checkbox" class="switchInput"><label for="input-newtab" class="switchLabel">Open in new tab</label>
                              <div class="slider"></div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="input-box mt-1">
               <div class="input-label">{{ __('Button 2') }}</div>
               <div class="input-group button-input-group">
                  <input type="text" class="input-small button-text" x-model="section.settings.button_two_text" placeholder="{{ __('Button 2 Text') }}">
                  <div class="link-options__container second mt-1">
                     
                     <x-builder.input input="section.settings.lol">
                        <div class="link-options__main relative">
                           <input class="input-small main__link" type="text" x-model="section.settings.button_two_link" placeholder="{{ __('Search site or paste link') }}" x-on:input="filter()">
                        </div>
                     </x-builder.input>
                  </div>
               </div>
            </div>
         </form>
        </div>
     </div>
</div>
