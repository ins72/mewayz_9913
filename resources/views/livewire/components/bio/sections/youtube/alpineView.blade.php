<div>
    <div x-data="builder__youtube_section_view">
       <div :class="{
         'custom-styles-link': site.settings.__theme !== 'default'
       }">
         <div class="flex flex-col builder-youtube-block gap-4">
            <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
               <div class="builder-block -youtube-block-item g--block-o" x-init="!item.content.fetch ? item.content.fetch = [] : ''">
                   <div class="-item-style" :class="{
                     '!rounded-none': site.settings.__theme == 'sand',
                     [`--${site.settings.__theme}`]: site.settings.__theme,
                  }" :style="{
                        'box-shadow': site.settings.__theme == 'retro' ? `6px 6px 0px 0px ${$store.builder.hexToRgba(site.settings.color, 0.4)}` : false,
                     }">
                       <div class="--style">
                        <div class="youtube-block-o flex items-center justify-center !pb-0 min-h-[272px] ![overflow:initial]" :class="{
                           'bg-gray-200': !item.content.fetch.h,
                           '!rounded-none': site.settings.corners == 'straight' || site.settings.__theme == 'sand',
                           '!rounded-xl': site.settings.corners == 'round',
                           '!rounded-3xl': site.settings.corners == 'rounded',
                        }">
                           <template x-if="!item.content.fetch.h">
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
       Alpine.data('builder__youtube_section_view', () => {
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