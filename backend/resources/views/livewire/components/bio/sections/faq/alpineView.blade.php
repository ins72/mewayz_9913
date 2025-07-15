<div>
    <div x-data="builder__faq_section_view">
       <div :class="{
         'custom-styles-link': site.settings.__theme !== 'default'
       }">
         <div class="grid-cols-1 grid gap-3 faq-block-wrapper">
            <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
               <div class="builder-block -faq-block-item g--block-o inner-image-container w-[100%] h-full ">
           
                   <div class="-item-style" :class="{
                     '!rounded-none': site.settings.__theme == 'sand',
                     [`--${site.settings.__theme}`]: site.settings.__theme,
                  }" :style="{
                     'box-shadow': site.settings.__theme == 'retro' ? `6px 6px 0px 0px ${$store.builder.hexToRgba(site.settings.color, 0.4)}` : false,
                  }">
                       <div class="--style">
       
               
                        <div class="sandy-accordion !rounded-none" :class="{
                           '--active': expanded
                        }" x-data="{ expanded: false }">
                           <div class="sandy-accordion-head flex" @click="expanded = ! expanded">
                              <span x-text="item.content.title"></span>
                           </div>
                           <div class="sandy-accordion-body mt-5 !pb-0" x-show="expanded" x-collapse>
                              <div x-text="item.content.description"></div>
                           </div>
                         </div>
                       </div>
                       <div class="--item--bg"></div>
                   </div>
               </div>
            </template>
         </div>
       </div>
    </div>
    @script
    <script>
       Alpine.data('builder__faq_section_view', () => {
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