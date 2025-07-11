<div>
    <div x-data="builder__location_section_view">
       <div :class="{
         'custom-styles-link': site.settings.__theme !== 'default'
       }">
         

         <div class="element-video builder-video-block gap-4">
            <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
               <div class="builder-block -video-block-item g--block-o">

                  <div class="-item-style" :class="{
                     '!rounded-none': site.settings.__theme == 'sand',
                  }">
                        <div class="--style">
                           <div class="element-single-video !m-0 is-iframe ![overflow:initial]" :class="{
                              '!rounded-none': site.settings.__theme == 'sand',
                              [`--${site.settings.__theme}`]: site.settings.__theme,
                           }" :style="{
                                 'box-shadow': site.settings.__theme == 'retro' ? `6px 6px 0px 0px ${$store.builder.hexToRgba(site.settings.color, 0.4)}` : false,
                              }">
                              <div class="element-single-video-container">

                                    <iframe scrolling="no" marginheight="0" marginwidth="0" :src="`https://google.com/maps?q=${item.content.location}&output=embed&loading=async`" width="100%" height="240" frameborder="0"></iframe>
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
       Alpine.data('builder__location_section_view', () => {
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