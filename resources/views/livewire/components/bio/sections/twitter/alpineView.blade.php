<div>
    <div x-data="builder__twitter_section_view">
       <div>
                  


         <div class="flex flex-col builder-twitter-block gap-4">
            <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
               <div class="builder-block -twitter-block-item" x-data="{}">
                  {{-- <div x-init="$root.querySelector('.--style').innerHTML = item.content.fetch.h"></div> --}}
                   <div class="-item-style">
                       <div class="--style">
                        <template x-if="item.content.fetch && item.content.fetch.h">
                           <div x-html="item.content.fetch.h"></div>
                        </template>
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
       Alpine.data('builder__twitter_section_view', () => {
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