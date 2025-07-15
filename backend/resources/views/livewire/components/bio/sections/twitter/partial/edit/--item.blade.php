<div>

   <div x-data="builder__twitter_section_edit_single">
      <div class="flex items-center justify-center bg-white rounded-2xl border-2 border-gray-300 border-dashed p-5 bg-[#F7F7F7] =shadow relative">
      
         <button class="btn btn-save !w-[24px] !h-[24px] justify-center items-center !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex absolute right-4 top-2 z-[99]" @click="__delete_item(item.uuid)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
   
          <div class="MarginRightS FlexCenterCenter handle cursor-grab">
           {!! __i('custom', 'grip-dots-vertical', 'w-3 h-3') !!}
          </div>
          <div class="flex-auto z-[50] relative">
             <div class="flex items-end w-[100%] gap-3">
               <div class="flex flex-col gap-2 w-[100%] relative">
                  <input placeholder="{{ __('Link') }}" @input="fetchBackend" x-model="link" type="text" class="text text-gray-600 p-0 overflow-hidden text-ellipsis min-h-[21px] border border-solid border-gray-200 rounded-lg resize-none p-2 w-[100%] focus:border focus:border-solid">
                  

                  <div class="absolute left-2 top-[50%] [transform:translateY(-50%)] bg-white flex items-center justify-center h-6 w-6" :class="{
                     '!hidden': !loading,
                  }">
                     <span class="loader-o20 text-[9px] !text-black"></span>
                  </div>
               </div>
             </div>
          </div>
       </div>
   </div>



    @script
    <script>
      Alpine.data('builder__twitter_section_edit_single', () => {
         return {
            link: null,
            loading: false,

            fetchBackend(){
               let $this = this;

               if(!$this.link) return;
               $this.loading = true;
               $this.$wire.fetch($this.link).then(r => {
                  $this.loading = false;

                  if(r){
                     $this.item.content.fetch = r;
                  }
               });
            },
            
            init(){
               let $this = this;
               $this.link = $this.item.content.link;

               if(!$this.item.content.fetch.w){
                  $this.fetchBackend();
               }
            }
         }
      });
   </script>
    @endscript
 </div>