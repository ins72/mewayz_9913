<div>

   <div x-data="builder__video_section_edit_single">
      <div class="flex flex-col justify-center bg-white rounded-2xl border-2 border-gray-300 border-dashed p-3 bg-[#F7F7F7] =shadow relative">
         <div class="card-button p-3 mb-1 flex gap-2 bg-[var(--yena-colors-gray-100)] w-[100%] rounded-lg !hidden" x-cloak @click="$event.stopPropagation();" :class="{
            '!hidden': !is_delete
            }">
            <button class="btn btn-medium neutral !h-8 !flex !items-center !justify-center !rounded-full !text-black !bg-white" type="button" @click="is_delete=!is_delete">{{ __('Cancel') }}</button>
   
            <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-8 !flex !items-center !justify-center !rounded-full w-[100%]" type="button" @click="__delete_item(item.uuid)">{{ __('Yes, Delete') }}</button>
         </div>
         
         <button class="btn btn-save !w-[24px] !h-[24px] justify-center items-center !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex !absolute right-2 top-2 
         z-[88]" @click="is_delete=!is_delete" type="button">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
   
          <div class="flex items-center w-[100%]">
            <div class="MarginRightS FlexCenterCenter handle cursor-grab">
               {!! __i('custom', 'grip-dots-vertical', 'w-3 h-3') !!}
              </div>
              <div class="flex-auto z-[50] relative w-[100%] pr-5">
                  <div class="flex items-center gap-2">
                     <template x-for="(_i, index) in types" :key="index">
                        <button class="yena-button-o !h-auto !min-h-[44px] !items-center !p-2 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)]" type="button" @click="item.content.type = index" :class="{
                           '!border-[var(--yena-colors-purple-400)]': item.content.type == index
                        }">
                              <div>
                                 <div class="bg-[#f7f3f2] w-6 h-6 rounded-lg flex items-center justify-center">
                                    <img :src="_i.image" class="w-5 h-5 object-cover" alt="">
                                 </div>
                              </div>
                        </button>
                     </template>
                  </div>

                 <div class="flex items-end w-[100%] gap-3 mt-1">
                   <div class="flex flex-col gap-2 w-[100%] relative">
                      <input placeholder="{{ __('Link') }}" x-model="item.content.link" type="text" class="text-gray-600 p-0 overflow-hidden text-ellipsis !min-h-[21px] !border-0 !border-b border-solid border-gray-200 rounded-none resize-none p-2 w-[100%]">
                      
    
                      <div class="absolute left-0 top-[50%] [transform:translateY(-50%)] bg-white flex items-center justify-center h-6 w-6" :class="{
                         '!hidden': !loading,
                      }">
                         <span class="loader-o20 !text-[9px] !text-black"></span>
                      </div>
                   </div>
                 </div>
              </div>
          </div>
       </div>
   </div>



    @script
    <script>
      Alpine.data('builder__video_section_edit_single', () => {
         return {
            loading: false,
            is_delete:false,

            fetchBackend(){
               let $this = this;

               if(!$this.item.content.link) return;
               $this.loading = true;


               $this.$wire.fetch($this.item.content.link, $this.item.content.type).then(r => {
                  $this.loading = false;

                  if(r){
                     $this.item.content.fetch = r;
                  }
               });
            },
            
            init(){
               let $this = this;

               this.$watch('item.content.link', (value) => {
                  $this.fetchBackend();
               });
               this.$watch('item.content.type', (value) => {
                  $this.fetchBackend();
               });

               if(!$this.item.content.fetch.type){
                  $this.fetchBackend();
               }
            }
         }
      });
   </script>
    @endscript
 </div>