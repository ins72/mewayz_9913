<?php

?>

<div>

   <div class="website-section" x-data="builder__logos_single">
       <div class="design-navbar">
          <ul >
              <li class="close-header !flex">
                <a @click="_editSection=null">
                  <span>
                      {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                  </span>
                </a>
             </li>
             <li class="!pl-0">{{ __('Product') }}</li>
             <li class="!flex items-center !justify-center">
               <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" @click="__delete_item(item.uuid)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
            </li>
          </ul>
       </div>
       <div class="container-small p-[var(--s-2)] pb-[150px]">
         <form method="post">
            <div class="input-box">
               <label for="text-size">{{ __('Button Styles') }}</label>
               <div class="input-group align-type">
                  <button class="btn-nav" :class="{'active': item.content.style == '-'}" type="button" @click="item.content.style = '-'">
                     <i class="ph ph-prohibit text-lg"></i>
                  </button>
                  <button class="btn-nav" :class="{'active': item.content.style == 'cart'}" type="button" @click="item.content.style = 'cart'">
                     {!! __i('shopping-ecommerce', 'Shopping Cart') !!}
                  </button>
                  <button class="btn-nav" :class="{'active': item.content.style == 'download'}" type="button" @click="item.content.style = 'download'">
                     {!! __i('--ie', 'download-arrow') !!}
                  </button>
                  <button class="btn-nav" :class="{'active': item.content.style == 'button'}" type="button" @click="item.content.style = 'button'">
                     {!! __i('--ie', 'cursor-button') !!}
                  </button>
               </div>
            </div>
            <div class="input-box">
               <label for="text-size">{{ __('Stickers') }}</label>
               <div class="input-group align-type pattern-image !grid grid-cols-4">
                  <button class="btn-nav !w-full" :class="{'active': item.content.sticker == '-'}" type="button" @click="item.content.sticker = '-'">
                     <i class="ph ph-prohibit text-lg"></i>
                  </button>
                  <button class="btn-nav !w-full" :class="{'active': item.content.sticker == 'badge'}" type="button" @click="item.content.sticker = 'badge'">
                     <div class="--flower bg-gray-300 w-7 h-7 rounded-full text-[10px] flex items-center justify-center">
                        {{ __('New') }}
                     </div>
                  </button>
                  <button class="btn-nav !w-full" :class="{'active': item.content.sticker == 'hottwo'}" type="button" @click="item.content.sticker = 'hottwo'">
                     <div class="bg-gray-300 w-10 h-6 rounded-r-full text-[10px] flex items-center justify-start pl-1">
                        {{ __('New') }}
                     </div>
                  </button>
                  <button class="btn-nav !w-full" :class="{'active': item.content.sticker == 'sale'}" type="button" @click="item.content.sticker = 'sale'">
                     <div class="bg-gray-300 w-7 h-7 rounded-b-md text-[10px] flex items-center justify-start pl-1">
                        {{ __('New') }}
                     </div>
                  </button>
                  <button class="btn-nav !w-full" :class="{'active': item.content.sticker == 'star'}" type="button" @click="item.content.sticker = 'star'">
                     <div class="--star bg-gray-300 w-7 h-7 rounded-full text-[10px] flex items-center justify-center">
                        {{ __('New') }}
                     </div>
                  </button>
                  <button class="btn-nav !w-full" :class="{'active': item.content.sticker == 'hot'}" type="button" @click="item.content.sticker = 'hot'">
                     <div class="bg-gray-300 w-10 h-6 rounded-br-[100%] text-[10px] flex items-center justify-start pl-1">
                        {{ __('New') }}
                     </div>
                  </button>
               </div>
            </div>

            <template x-if="item.content.sticker!=='-'">
              <div class="input-box">
                 <label for="text">{{ __('Sticker Text') }}</label>
                 <div class="input-group">
                    <input type="text" class="input-small resizable-textarea blur-body" x-model="item.content.sticker_text" name="title" placeholder="{{ __('Add text') }}">
                 </div>
              </div>
            </template>

            <template x-if="item.content.sticker!=='-'">
               <div class="colors-container">
                  <div class="input-box">
                      <div class="input-label">{{ __('Sticker Color') }}</div>
                      <div class="input-group">
                          <div class="custom-color !block">
                              <form onsubmit="return false;">
                              <div class="input-box !pb-0">
                                  <div class="input-group">
                                      <input type="color" class="input-small input-color" x-model="item.content.sticker_color" :style="{
                                          'background-color': item.content.sticker_color,
                                          'color': $store.builder.getContrastColor(item.content.sticker_color)
                                          }" maxlength="6">
                                          
                                      <span class="hash"  :style="{
                                          'color': $store.builder.getContrastColor(item.content.sticker_color)
                                          }">#</span>
                                      <span class="color-generator" :style="{
                                          'background-color': item.content.sticker_color,
                                          'color': $store.builder.getContrastColor(item.content.sticker_color)
                                          }"></span>
                                  </div>
                              </div>
                              </form>
                          </div>
                      </div>
                  </div>
               </div>
            </template>
            <div class="input-box">
               <label for="text-size">{{ __('Name & Rating') }}</label>
               <div class="input-group align-type">
                  <button class="btn-nav !w-[50%]" type="button" @click="item.content.enable_product_name = true" :class="{'active': item.content.enable_product_name}">
                     {{ __('Enable') }}
                  </button>
                  <button class="btn-nav !w-[50%]" type="button" @click="item.content.enable_product_name = false" :class="{'active': !item.content.enable_product_name}">
                     {{ __('Disable') }}
                  </button>
               </div>
            </div>
            <div class="input-box">
               <label for="text-size">{{ __('Price') }}</label>
               <div class="input-group align-type">
                  <button class="btn-nav !w-[50%]" type="button" @click="item.content.enable_product_price = true" :class="{'active': item.content.enable_product_price}">
                     {{ __('Enable') }}
                  </button>
                  <button class="btn-nav !w-[50%]" type="button" @click="item.content.enable_product_price = false" :class="{'active': !item.content.enable_product_price}">
                     {{ __('Disable') }}
                  </button>
               </div>
            </div>
         </form>
       </div>
    </div>

    @script
    <script>
        Alpine.data('builder__logos_single', () => {
           return {


            init(){

               var $this = this;
               window.addEventListener("logosMediaEvent:" + this.item.uuid, (event) => {
                  $this.item.content.image = event.detail.image;
                  $this.dispatchSections();
                  $this._save();
               });
            }
           }
         });
    </script>
    @endscript
</div>