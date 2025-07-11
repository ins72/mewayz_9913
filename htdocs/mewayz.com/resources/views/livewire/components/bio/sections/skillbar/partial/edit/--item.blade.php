<div>

   <div x-data="builder__faq_item__single">
    
      <div class="flex flex-col justify-center bg-white rounded-2xl border-2 border-gray-300 border-dashed p-5 bg-[#F7F7F7] =shadow relative" x-data="{is_delete:false}">
         <div class="card-button p-3 mb-1 flex gap-2 bg-[var(--yena-colors-gray-100)] w-[100%] rounded-lg !hidden" x-cloak @click="$event.stopPropagation();" :class="{
            '!hidden': !is_delete
            }">
            <button class="btn btn-medium neutral !h-8 !flex !items-center !justify-center !rounded-full !text-black !bg-white" type="button" @click="is_delete=!is_delete">{{ __('Cancel') }}</button>
   
            <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-8 !flex !items-center !justify-center !rounded-full w-[100%]" type="button" @click="__delete_item(item.uuid)">{{ __('Yes, Delete') }}</button>
         </div>
         
         <button class="btn btn-save !w-[24px] !h-[24px] justify-center items-center !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex !absolute right-2 top-2" @click="is_delete=!is_delete" type="button">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
   
          <div class="flex items-center w-[100%]">
            <div class="MarginRightS FlexCenterCenter handle cursor-grab">
               {!! __i('custom', 'grip-dots-vertical', 'w-3 h-3') !!}
              </div>
              <div class="flex-auto overflow-clip">
                 <div class="flex items-start gap-3">
                   <div class="flex flex-col gap-2 grow shrink">
                      <div class="">
                         <div>
                              <input placeholder="{{ __('Title') }}" x-model="item.content.title" type="text" class=" text text-gray-600 p-0 overflow-hidden text-ellipsis !min-h-[21px]">
                          </div>
                      </div>
                      <div class="">
                         <div>
                            <div class="input-box">
                               <label for="text-size">{{__('Skillbar')}}</label>
                               <div class="input-group">
                                  <input type="range" class="input-small range-slider" min="1" max="100" step="1" x-model="item.content.skillbar">
                                  <p class="image-size-value" x-text="item.content.skillbar + '%'"></p>
                               </div>
                            </div>
                          </div>
                      </div>
                   </div>
                 </div>
              </div>
          </div>
       </div>
   </div>
   @script
   <script>
       Alpine.data('builder__faq_item__single', () => {
          return {


           init(){

              var $this = this;
              window.addEventListener(`_item_media:${this.item.uuid}`, (event) => {
                 $this.item.image = event.detail.image;
                 $this._save();
              });
           }
          }
        });
   </script>
   @endscript
 </div>