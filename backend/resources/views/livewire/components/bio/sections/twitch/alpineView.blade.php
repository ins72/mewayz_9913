<div>
    <div x-data="builder__twitch_section_view">
       <div>
         <div class="flex flex-col builder-twitch-block gap-4">
            <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
               <div class="builder-block -twitch-block-item" x-init="!item.content.fetch ? item.content.fetch = [] : ''">
                   <div class="-item-style" :class="{
                     '!rounded-none': site.settings.corners == 'straight',
                     '!rounded-xl': site.settings.corners == 'round',
                     '!rounded-3xl': site.settings.corners == 'rounded',
                  }">
                       <div class="--style">
                        <div class="generate--o-block bg-gray-200 flex items-center justify-center !pb-0 min-h-[272px] !border-[inherit]">
                           <template x-if="!item.content.fetch && !item.content.fetch.h">
                              <div class="default-image p-4 !flex bg-gray-200 items-center justify-center">
                                 {!! __i('--ie', 'broken-link-unlink-attachment-square.1', 'text-gray-400 !w-8 !h-8') !!}
                              </div>
                           </template>
                           <template x-if="item.content.fetch && item.content.fetch.h">
                              <div x-html="item.content.fetch.h" class="o-iframe-oo"></div>
                           </template>
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
       Alpine.data('builder__twitch_section_view', () => {
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