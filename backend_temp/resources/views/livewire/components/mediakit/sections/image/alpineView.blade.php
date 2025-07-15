<div>
    <div x-data="builder__image_section_view">
       <div :class="{
         'custom-styles-link': site.settings.__theme !== 'default'
       }">
          <div class="multi-image-container builder-image-block !grid grid-cols-2 gap-4">
            <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
                <div class="builder-block -image-block-item inner-image-container w-[100%] h-full" :class="{
                    'col-span-2': (index + 1) % 2 !== 0 && (section.items.length == (index+1))
                }">
                   <div class="-item-style !bg-transparent">
                      <div class="--style">
                         <a class="inner-image" :class="{
                           '!rounded-none': site.settings.__theme == 'sand',
                           [`--${site.settings.__theme}`]: site.settings.__theme,
                        }"
                        :style="{
                           'box-shadow': site.settings.__theme == 'retro' ? `6px 6px 0px 0px ${$store.builder.hexToRgba(site.settings.color, 0.4)}` : false,
                        }" x-outlink="item.content.link">
                            <div class="thumbnail">
                                <template x-if="!item.image">
                                    <div class="default-image p-4 !flex bg-gray-200 items-center justify-center">
                                       {!! __i('--ie', 'image-picture', 'text-gray-400 !w-7 !h-7') !!}
                                    </div>
                                 </template>
                                 <template x-if="item.image">
                                    <img :src="$store.builder.getMedia(item.image)" alt="">
                                 </template>
                            </div>
                            <template x-if="item.content.title">
                                <h2 class="image-title" x-text="item.content.title"></h2>
                            </template>
                            <span class="fancy-drop"></span>
                         </a>
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
       Alpine.data('builder__image_section_view', () => {
          return {
             colClass: 'grid-cols-1',


             
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