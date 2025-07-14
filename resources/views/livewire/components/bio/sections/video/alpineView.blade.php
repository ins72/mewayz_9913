<div>
    <div x-data="builder__video_section_view">
       <div :class="{
         'custom-styles-link': site.settings.__theme !== 'default'
       }">
         <div class="flex flex-col builder-video-block gap-4">
            <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
               <div class="builder-block -video-block-item g--block-o" x-init="!item.content.fetch ? item.content.fetch = [] : ''">
                   <div class="-item-style" :class="{
                     '!rounded-none': site.settings.__theme == 'sand',
                  }" x-data="{show:false}">
                       <div class="--style" @click="!item.content.fetch.isIframe ? show = true : ''">
                        <div class="element-single-video !m-0 ![overflow:initial]" :class="{
                              '!rounded-none': site.settings.__theme == 'sand',
                              [`--${site.settings.__theme}`]: site.settings.__theme,
                              'is-iframe': item.content.fetch.isIframe
                           }" :style="{
                                 'box-shadow': site.settings.__theme == 'retro' ? `6px 6px 0px 0px ${$store.builder.hexToRgba(site.settings.color, 0.4)}` : false,
                              }">
                           <div class="element-single-video-container">
                              <template x-if="item.content.fetch.isIframe || show">
                                 <div>
                                    <iframe :src="item.content.fetch.iframe" frameborder="0"></iframe>
                                 </div>
                              </template>
                              <template x-if="!item.content.fetch.isIframe && !show">
                                 <div>
                                    <button class="play-button">
                                       <i class="ph ph-play"></i> 
                                    </button>
                                    <img :src="item.content.fetch.thumbnail" class="banner-img" alt="">
                                 </div>
                              </template>
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
       Alpine.data('builder__video_section_view', () => {
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