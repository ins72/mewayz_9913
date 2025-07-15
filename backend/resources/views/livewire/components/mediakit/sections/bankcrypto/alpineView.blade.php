<div>
    <div x-data="builder__bankcrypto_section_view">
       <div>


         <template bit-component="crypto-template">
            <div>
               <div class="bank-crypto-wallet bg-white cursor-pointer w-[100%]">
                 <div>
                   <div class="flex">
                     <template x-if="item.image">
                        <img :src="$store.builder.getMedia(item.image)" class="--img"  alt="">
                     </template>
                     
                     <div class="--name" x-text="item.content.title"></div>
                   </div>
 
                   <div class="--desc" x-text="item.content.wallet"></div>
                 </div>
 
                 <div>
                   <div class="-copy cursor-pointer" @click="$clipboard(item.content.wallet)">
                     {!! __i('interface-essential', 'copy-paste-select-add-plus.2') !!}
                   </div>
                 </div>
               </div>
            </div>
         </template>




         <div class="grid-cols-1 grid gap-3 bank_crypto-block-wrapper">
            <div class="builder-block w-[100%] h-full ">
         
               <div class="-item-style">
                   <div class="--style">
                     <template x-if="section.items.length < 2">
                        <div x-bit:crypto-template x-data="{item: section.items[0]}"></div>
                     </template>
                     <template x-if="section.items.length >= 2">
                        <div class="sandy-accordion bg-white p-0" x-data="{ expanded: false }">
                           <div class="sandy-accordion-head flex before:!content-[initial]" @click="expanded = ! expanded">
                              <div class="bank-crypto-wallet bg-white cursor-pointer w-[100%] !m-0">
                                <div>
                                  <div class="flex">
                                    <template x-if="section.items[0].image">
                                       <img :src="$store.builder.getMedia(section.items[0].image)" class="--img" alt="">
                                    </template>
                                    
                                    <div class="--name">{{ __('Wallet') }}</div>
                                  </div>
                
                                  <div class="--desc">{{ __('Tap to see all') }}</div>
                                </div>
                
                                <div>
                                  <div class="-copy text-black cursor-pointer">
                                    {!! __i('money', 'wallet copy') !!}
                                  </div>
                                </div>
                              </div>
                           </div>
                           <div class="sandy-accordion-body mt-5 pb-0" x-show="expanded" x-collapse>
               
                              <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
                                 <div x-bit:crypto-template></div>
                              </template>
                           </div>
                         </div>
                     </template>
                   </div>
                   <div class="--item--bg"></div>
               </div>
           </div>
         </div>
       </div>
    </div>
    @script
    <script>
       Alpine.data('builder__bankcrypto_section_view', () => {
          return {

             
             init(){
                var $this = this;
                window.addEventListener('section::' + this.section.uuid, (event) => {
                   $this.section = event.detail;
                });
             }
          }
       });
    </script>
    @endscript
 </div>