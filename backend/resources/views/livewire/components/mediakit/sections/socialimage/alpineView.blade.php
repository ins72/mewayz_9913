<div>
    <div x-data="builder__image_section_view">
       <div>
          <div class="multi-image-container builder-image-block !grid grid-cols-2 gap-4">
            <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
                <div class="builder-block -image-block-item inner-image-container w-[100%] h-full" :class="{
                    'col-span-2': (index + 1) % 2 !== 0 && (section.items.length == (index+1))
                }">
                   <div class="-item-style overflow-hidden" :class="{
                     '!rounded-none': site.settings.corners == 'straight',
                     '!rounded-xl': site.settings.corners == 'round',
                     '!rounded-3xl': site.settings.corners == 'rounded',
                  }">
                      <div class="--style" x-data="{textColor: $store.builder.getContrastColor(item.content.color)}">
                        <a class="awesome-card !rounded-none block" :style="{
                           'background': item.content.color
                        }" x-outlink="item.content.link">
                
                            <div class="--social-icon right-8" :style="{
                              'color': textColor
                             }">
                              <template x-if="!item.content.icon">
                                 <i class="ph ph-instagram text-xl lg:!text-3xl"></i>
                              </template>
                              <template x-if="item.content.icon">
                                 <i :class="item.content.icon" class="ph text-xl lg:!text-3xl"></i>
                              </template>
                            </div>
    
                            <div class="-thumbnail overflow-hidden" :class="{
                              '!rounded-none': site.settings.corners == 'straight',
                              '!rounded-xl': site.settings.corners == 'round',
                              '!rounded-full': site.settings.corners == 'rounded',
                           }">
                              <template x-if="!item.image">
                                  <div class="default-image p-4 !flex bg-gray-200 items-center justify-center w-[100%] h-full">
                                     {!! __i('--ie', 'image-picture', 'text-gray-400 !w-7 !h-7') !!}
                                  </div>
                               </template>
                               <template x-if="item.image">
                                  <img :src="$store.builder.getMedia(item.image)" alt="">
                               </template>
                            </div>
    
                            <div class="-content">
                              <template x-if="item.content.title">
                                  <div class="--social-title" :style="{
                                   'color': textColor
                                  }" x-text="item.content.title"></div>
                              </template>
                              <template x-if="item.content.username">
                                 <div class="--social-username truncate" :style="{
                                  'color': textColor
                                 }" x-text="'@' + item.content.username"></div>
                              </template>
                            </div>
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