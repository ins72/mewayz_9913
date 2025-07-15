<div>
    <div x-data="builder__embed_section_view">
       <div :class="{
         'custom-styles-link': site.settings.__theme !== 'default'
       }">
         <div class="flex flex-col builder-embed-block gap-4 g--block-o">
            <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
               <div class="link-button builder-block custom-link-container-o" x-init="!item.content.fetch ? item.content.fetch = [] : ''" :class="{
                  '!hidden': item.content.error
               }">
                  <div class="-item -item-style !m-0" :class="{
                           '!rounded-none': site.settings.__theme == 'sand',
                           [`--${site.settings.__theme}`]: site.settings.__theme,
                        }" :style="{
                           'box-shadow': site.settings.__theme == 'retro' ? `6px 6px 0px 0px ${$store.builder.hexToRgba(site.settings.color, 0.4)}` : false,
                        }">
                      <div class="--style custom-link-container layout-description !m-0 ![border-radius:initial]">
                          <div class="w-[100%] h-full">
              
                              <a class="custom-link-content-wrapper --control">
                                 <template x-if="item.image">
                                    <div class="--link-icon">
                                       <img :src="item.image" alt=" " class="custom-link-preview-image default-transparent-on-hover" :class="{
                                          '!rounded-none': site.settings.__theme == 'sand',
                                       }">
                                    </div>
                                 </template>
                                 
                                  <div class="custom-link-preview-content description">
                                      <div class="custom-link-preview-title">
                                          <span class="custom-link-title-overflow">
                                              <p x-text="item.content.fetch.t"></p>
                                          </span>
                                      </div>
              
                                      <div class="custom-link-preview-description line-clamp-2" x-text="item.content.fetch.d"></div>
                                  </div>
                              </a>
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
       Alpine.data('builder__embed_section_view', () => {
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