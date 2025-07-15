<div>
    <div x-data="builder__features_section_view">
       <div :class="{
         'custom-styles-link': site.settings.__theme !== 'default'
       }">

         <div class="grid-cols-1 grid gap-3 features-block-wrapper" :class="`-${section.settings.style}`">
            <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
            
               <div class="builder-block g--block-o w-[100%] h-full ">
         
                  <div class="-item-style" :class="{
                     '!rounded-none': site.settings.__theme == 'sand',
                     [`--${site.settings.__theme}`]: site.settings.__theme,
                  }" :style="{
                     'box-shadow': site.settings.__theme == 'retro' ? `6px 6px 0px 0px ${$store.builder.hexToRgba(site.settings.color, 0.4)}` : false,
                  }">
                      <div class="--style">
        
                        <div class="features-wrapper-div -features-item-c !rounded-none m-0">
                
                          <div class="--thumbnail">
                              <template x-if="!item.image">
                                 <div class="default-image p-1 !flex bg-gray-200 items-center justify-center h-full">
                                    {!! __i('--ie', 'image-picture', 'text-gray-400 !w-7 !h-7') !!}
                                 </div>
                              </template>
                              <template x-if="item.image">
                                 <img :src="$store.builder.getMedia(item.image)" alt="">
                              </template>
                          </div>
                
                          <div class="--content">
                            <p class="--name" x-text="item.content.title"></p>
                            <p class="--desc" x-text="item.content.subtitle"></p>
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
       Alpine.data('builder__features_section_view', () => {
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