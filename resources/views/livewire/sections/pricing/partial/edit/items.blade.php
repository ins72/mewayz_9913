<?php

?>

<div>

   <div class="website-section" x-data="builder__pricings_single">
       <div class="design-navbar">
          <ul >
              <li class="close-header !flex">
                <a @click="__page = '-'">
                  <span>
                      {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                  </span>
                </a>
             </li>
             <li class="!pl-0" x-text="item.content.title">{{ __('Pricing') }}</li>
             <li class="!flex items-center !justify-center">
               <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" @click="__delete_item(item.uuid)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
            </li>
          </ul>
       </div>
       <div class="container-small p-[var(--s-2)] pb-[150px] box-features sub-panel">
         <form method="post">
            <div class="input-box">
               <div class="input-label">{{ __('Title') }}</div>
               <div class="input-group">
                  <input type="text" class="input-small blur-body" x-model="item.content.title" name="title" placeholder="{{ __('Add pricing title') }}">
               </div>
            </div>
            <div class="input-box mt-1">
               <div class="input-label">{{ __('Text') }}</div>
               <div class="input-group">
                  <x-builder.textarea type="text" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[65px]" x-model="item.content.text" name="title" placeholder="{{ __('Add pricing description') }}"/>
               </div>
            </div>

            <div class="input-box features">
               <div class="input-label">{{ __('Features') }}</div>
               <div class="input-group">
                  <template x-for="(feature, index) in item.content.features" :key="index">
                     <div class="input-block">
                        <input type="text" class="input-small" x-model="feature.name" placeholder="{{ __('Feature 1') }}">
                        <span class="minus-button cursor-pointer" @click="remove_feature(index)">
                           <i class="fi fi-rr-minus-small text-red-500"></i>
                        </span>
                     </div>
                  </template>
                  <button type="button" @click="create_feature" class="btn">
                     <span >{{ __('Add feature') }}</span>
                     <span class="plus-icon">
                        <i class="fi fi-rr-plus-small"></i>
                     </span>
                  </button>
               </div>
            </div>
            <div class="input-box mt-1">
               <div class="input-label">{{ __('Price') }}</div>

               <template x-if="section.settings.type == 'single'">
                  <div class="input-group">
                     <input type="tel" maxlength="10" x-model="item.content.single_price" class="input-small" placeholder="$0">
                  </div>
               </template>
               <template x-if="section.settings.type == 'plans'">
                  <div class="input-group amount-group">
                     <input type="tel" maxlength="10" x-model="item.content.month_price" class="input-small" placeholder="$0">
                     <input type="tel" maxlength="10" x-model="item.content.year_price" class="input-small" placeholder="$0">
                     <span class="monthly duration">{{ __('Monthly') }}</span>
                     <span class="yearly duration">{{ __('Yearly') }}</span>
                  </div>
               </template>
            </div>
            <div class="input-box popular-price mt-1">
               <div class="input-group">
                  <div class="input-label"></div>
                  <div class="switchWrapper">
                     <input :id="'showPopularPriceswitch' + item.uuid" type="checkbox" class="switchInput" x-model="item.content.popular_price" @input="setMostPopular">
                     <label :for="'showPopularPriceswitch' + item.uuid" class="switchLabel">{{ __('Popular Price') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>

            <div class="input-box mt-1">
               <div class="input-label">{{ __('Button') }}</div>
               <div class="input-group">
                  <input type="text" class="input-small blur-body" x-model="item.content.button" placeholder="{{ __('Sign up') }}">
               </div>
            </div>

            <div class="mt-1 input-box">
               <div class="input-label">{{ __('Link') }}</div>
               <div class="input-group button-input-group">

                  <x-builder.input>
                     <div class="relative link-options__main">
                        <input class="input-small main__link" type="text" x-model="item.content.button_link" placeholder="{{ __('Search site or paste link') }}" x-on:input="filter()" >
                     </div>
                  </x-builder.input>
               </div>
            </div>
         </form>
       </div>
    </div>

    @script
    <script>
        Alpine.data('builder__pricings_single', () => {
           return {


            create_feature(){
               let item = {
                  name: '{{ __('Add feature') }}'
               };
               this.item.content.features.push(item);
            },

            remove_feature(index){
               this.item.content.features.splice(index, 1);
            },

            setMostPopular(){

               this.section.items.forEach((e) => {

                  if(e.uuid !== this.item.uuid) {
                     e.content.popular_price = false;
                  }
               });

               // this.item.content.popular_price =! this.item.content.popular_price;
            },

            init(){
               var $this = this;
               
               if(this.item.content.features === undefined || this.item.content.features == null){
                  this.item.content.features = [];
               }
            }
           }
         });
    </script>
    @endscript
</div>