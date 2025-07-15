<?php

?>

<div>
    <div class="mt-2 content">
        <div class="panel-input mb-1 px-[var(--s-2)]">
           <form>
              <div class="flex flex-col gap-2">

                  <div class="input-box">
                     <label>{{ __('Title') }}</label>
                     <div class="input-group">
                        <input type="text" class="input-small"  x-model="section.content.heading" name="title" placeholder="{{ __('Add main heading') }}">
                     </div>
                  </div>
                     
                  <div class="input-box">
                     <label>{{ __('Button Text') }}</label>
                     <div class="input-group">
                        <input type="text" class="input-small"  x-model="section.content.button_text" name="title" placeholder="{{ __('Add button text') }}">
                     </div>
                  </div>

                  <div class="input-box features">
                     <div class="input-label">{{ __('Features') }}</div>
                     <div class="input-group">
                        <template x-for="(price, index) in section.content.prices" :key="index">
                           <div class="input-block">
                              <input type="text" class="input-small" x-model="price.name" placeholder="{{ __('Price 1') }}">
                              <span class="absolute right-[10px] top-2/4 transform -translate-y-1/2 px-[0] py-[2px] flex justify-center items-center cursor-pointer" @click="remove_price(index)">
                                 <i class="fi fi-rr-minus-small text-red-500"></i>
                              </span>
                           </div>
                        </template>
                        <button type="button" @click="create_price" class="btn">
                           <span >{{ __('Add Price') }}</span>
                           <span class="plus-icon">
                              <i class="fi fi-rr-plus-small"></i>
                           </span>
                        </button>
                     </div>
                  </div>
                  
                  <div class="input-box banner-advanced banner-action !border border-solid border-[var(--c-mix-1)]">
                     <div class="input-group">
                        <div class="switchWrapper">
                           <input id="customAmount-switch" type="checkbox" x-model="section.content.custom_amount" class="switchInput">
      
                           <label for="customAmount-switch" class="switchLabel">{{ __('Custom Amount') }}</label>
                           <div class="slider"></div>
                        </div>
                     </div>
                  </div>


              </div>
           </form>
        </div>
     </div>

</div>