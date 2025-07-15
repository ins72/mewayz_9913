<div>
   @php
       
    $skeleton = [
      'first_name' => 'First Name',
      'last_name' => 'Last Name',
      'email' => 'Email Address',
      'phone' => 'Phone Number',
      'message' => 'Message',
    ];
   @endphp
   <div x-data="form__section">
      <template x-if="form__page == 'options'">
         <div>
            <div class="website-section">
               <div class="design-navbar">
                   <ul >
                       <li class="close-header !flex">
                         <a @click="form__page='main'">
                           <span>
                               {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                           </span>
                         </a>
                      </li>
                      <li class="!pl-0">{{ __('Options') }}</li>
                      <li></li>
                   </ul>
               </div>
               <div class="container-small p-[var(--s-2)] pb-[150px]">
                 <form class="form-panel">
                  <div class="options-type">
                     <div class="input-box">
                        <div class="input-label">{{ __('Button') }}</div>
                        <div class="input-group">
                           <input type="text" class="input-large" x-model="section.form.button_name" placeholder="{{ __('Sign up') }}">
                        </div>
                     </div>
                     <div class="input-box">
                        <div class="input-label">{{ __('Success') }}</div>
                        <div class="input-group">
                           <input type="text" class="input-large" x-model="section.form.success_message">
                        </div>
                     </div>
                  </div>
                 </form>
               </div>
           </div>
         </div>
      </template>

      <template x-if="form__page == 'main'">
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
                   <li class="!pl-0">{{ __('Form') }}</li>
                   <li></li>
                </ul>
            </div>
            <div class="container-small p-[var(--s-2)] pb-[150px]">
              <form class="form-panel">
                 <p >{{ __('Select what fields you want to collect and click to customize the placeholders for your audience.') }}</p>
                 <div class="collection-type">
                    @foreach ($skeleton as $key => $item)
                    <div class="input-box">
                       <div class="input-group">
                          <input type="text" x-model="section.form.{{ $key }}" placeholder="{{ $item }}" class="input-large">
                       </div>
                       <label class="filter-item">
                          <input type="checkbox" name="collect-option" @if($key !== 'email') x-model="section.form.{{ $key }}_enable" @endif {{ $key == 'email' ? 'checked disabled' : '' }}>
                          <span class="checkmark">
                             <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17L4 12" stroke="var(--foreground)" stroke-linecap="square"></path>
                             </svg>
                          </span>
                       </label>
                    </div>
                    @endforeach
                 </div>
                 <div class="other-form-section">
                    <div class="input-box cursor-pointer" @click="form__page='options'; clearTimeout(autoSaveTimer);">
                       <div class="input-group input-chevron">
                          <label >{{ __('Options') }}</label>
                          <span>
                             {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                          </span>
                       </div>
                    </div>
                 </div>
              </form>
            </div>
        </div>
      </template>
   </div>

   @script
      <script>
            Alpine.data('form__section', () => {
               return {
                  form__page: 'main'
               }
            });
      </script>
   @endscript
</div>
