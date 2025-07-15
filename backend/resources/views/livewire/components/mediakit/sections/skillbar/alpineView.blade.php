<div>
    <div x-data="builder__skillbar_section_view">
       <div>

                  

         <div class="flex flex-col builder-skillbar-block gap-4">
            <div class="builder-block -skillbar-block-item">

               <div class="-item-style">
                  <div class="--style">
                        
                      <div class="skill-bars flex flex-col gap-4 bg-white  overflow-hidden">
                         <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
                            <div class="-bar m-0">
                                  <div class="-info">
                                     <span x-text="item.content.title"></span>
                                  </div>
                                  <div class="-progress-line html">
                                     <span :style="{'width': item.content.skillbar + '%'}">
                                     </span>
                                        <div class="-text" x-text="item.content.skillbar + '%'"></div>
                                  </div>
                            </div>
                         </template>
                      </div>
                  </div>
                  <div class="--item--bg"></div>
               </div>
            </div>
         </div>
       </div>
    </div>
    @script
    <script>
       Alpine.data('builder__skillbar_section_view', () => {
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