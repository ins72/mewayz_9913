<div>

   <div x-data="builder__linksSectionView">
      <template x-if="sections.length > 0">
          <div :class="`${colClass} grid gap-3`">
              <template x-for="item in sections">
                  <div>
                      <div x-init="
                          let link = '';
                          let class = '';
                          let styles = '';
                          let isSensitive = item.content.sensitive_content;
                          let link = item.content.link ? processLink(item.content.link, item.page_id) : '';
                          let jsFunction = 'sandy_embed_id_' + item.id;
                          let fetch = item.content.fetch;
                          let extraAttr = '';
                          if (item.content.oembed && fetch.h) {
                              extraAttr = 'data-sandy-embed=\"' + jsFunction + '\" data-sandy-embed-open=\"sandy-embed-dialog\"';
                          }
                          let tag = 'a';
                          let isAccordion = item.content.oembed && item.content.fetch.h ? true : false;
                          if (item.content.oembed) {
                              link = '';
                              tag = 'div';
                          }
                          if (item.content.animation && item.content.animation !== '-') {
                              let animateRuns = ' animate__' + item.content.animation_runs;
                              let animate = ' animate__' + item.content.animation;
                              class += animate;
                              class += animateRuns;
                          }
                          styles = false;
                          if (item.content.styles && item.content.styles !== '-') {
                              styles = item.content.styles;
                          }
                      ">
                          <div :class="`link-button builder-block ${styles ? 'custom-link-container-o' : ''}`">
                              <div :class="`-item -item-style animate__animated animate__ animate__delay-2s ${class}`">
                                  <div :class="`--style ${styles ? 'custom-link-container layout-' + styles : ''}`">
                                      <div :class="`${isSensitive ? 'z-menuc' : ''} w-[100%] h-full"` data-max-width="400" data-placement="bottom-start" wire:ignore.self data-handle=".--control">
                                          <template x-if="styles">
                                              <a :class="`custom-link-content-wrapper --control ${isSensitive ? '' : link + ' ' + extraAttr}`">
                                                  <img :src="`${gs('media/blocks', item.thumbnail)}`" alt=" " class="custom-link-preview-image default-transparent-on-hover">
                                                  <template x-if="overlayArray.includes(styles)">
                                                      <span class="custom-link-preview-image-overlay"></span>
                                                  </template>
                                                  <div :class="`custom-link-preview-content ${styles}`">
                                                      <div class="custom-link-preview-title">
                                                          <span class="custom-link-title-overflow">
                                                              <p x-text="item.content.heading"></p>
                                                          </span>
                                                      </div>
                                                      <div class="custom-link-preview-description line-clamp-2" x-text="item.content.desc"></div>
                                                      <template x-if="styles == 'botton' && item.content.button">
                                                          <div class="custom-link-preview-button line-clamp-2"><p x-text="item.content.button"></p></div>
                                                      </template>
                                                  </div>
                                              </a>
                                          </template>
                                          <template x-if="!styles">
                                              <a :class="`--link --control ${isSensitive ? '' : link + ' ' + extraAttr}`">
                                                  <template x-if="item.thumbnail">
                                                      <div class="--link-icon">
                                                          <img :src="`${gs('media/blocks', item.thumbnail)}`" class="">
                                                      </div>
                                                  </template>
                                                  <div class="--link-text-wrap">
                                                      <p x-text="item.content.heading"></p>
                                                      <p class="--link-text" x-text="parse(item.content.link, 'host')"></p>
                                                  </div>
                                                  <div class="--link-status"><span></span></div>
                                              </a>
                                          </template>
                                          {{-- <template x-if="isSensitive">
                                              <div class="z-menuc-content-temp" wire:ignore.self>
                                                  <div class="p-5 w-80">
                                                      <div class="bg-white">
                                                          <div class="dashhead flex-row items-center justify-between mb-3 bg-gray-100 p-2">
                                                              <div class="flex">
                                                                  <div class="-icon bg-white p-2 h-10 w-10 flex-none">
                                                                      {!! ori('interface-essential', 'warning.2') !!}
                                                                  </div>
                                                                  <div class="-content ml-2">
                                                                      <p class="--title my-auto whitespace-nowrap text-sm">{{ __('sensitive content') }}</p>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                          <div class="-is-sensitive">
                                                              <div class="--desc text-sm mb-3"><?= __('this link may contain content that is not appropriate for all audiences.') ?></div>
                                                              <a class="sandy-button bg-black py-2 flex-grow block" :href="link" :data-extra-attr="extraAttr">
                                                                  <div class="--sandy-button-container">
                                                                      <span class="text-xs">{{ __('Continue') }}</span>
                                                                  </div>
                                                              </a>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </template> --}}
                                      </div>
                                  </div>
                                  <div class="--item--bg"></div>
                              </div>
                          </div>
                      </div>
                  </div>
              </template>
          </div>
      </template>
  </div>
  
   

    @php
        $classes = collect([
            'logos-box',
            'section-width-fill',
            'box',
            'section-bg-wrapper', 
            'focus'
        ]);

        $classes = $classes->toArray();
    @endphp
    <div class="{{ implode(' ', $classes) }}" x-data="builder__linksSectionView" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
      <div class="text-left inner-content" :class="{
         'text-left': section.settings.align == 'left' || section.settings.split_title,
         'text-center': !section.settings.split_title && section.settings.align == 'center',
         'text-right': !section.settings.split_title && section.settings.align == 'right'
        }" style="--image-height: 50px; --image-height-mobile: 200px;">
        
        
         <div class="logos-container w-boxed" :class="{
            'show-background': section.settings.background,
            'show-border': section.settings.border,
            'left-title': section.settings.split_title,
         }" :style="{
            '--grid-height': section.settings.desktop_height + 'px',
            '--grid-height-mobile': section.settings.mobile_height + 'px',
            '--grid-width': section.settings.desktop_width + 'px',
            '--grid-width-mobile': section.settings.mobile_width + 'px',
            '--grid-count': section.settings.desktop_grid,
            '--grid-count-mobile': section.settings.mobile_grid,
            }">
            <div class="logos-header">
               <div class="[text-align:inherit]">
                <template x-if="section.content.label">
                   <span class="gallery-label t-0" x-text="section.content.label"></span>
                </template>
               </div>
               <template x-if="section.content.title">
               <h2 class="t-4 pre-line [text-align:inherit]" x-text="section.content.title"></h2>
               </template>
               <template x-if="section.content.subtitle">
                  <p class="t-1 pre-line subtitle [text-align:inherit]" x-text="section.content.subtitle"></p>
               </template>
            </div>
            
            <div class="logos-container__items" :class="{
               'carousel-items-container carousel': section.settings.display == 'carousel',
               'grid': section.settings.display == 'grid' && !section.settings.display
            }">
               <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
                  <div class="logos-container__item">
                     <a class="no-dark" href="javascript:void(0)" target="_self" :style="{
                        '--scale': item.content.desktop_size,
                        '--mobile-scale': item.content.mobile_size,
                     }">
                        
                        <template x-if="item.content.image">
                           <img :src="$store.builder.getMedia(item.content.image)" alt="" class="light-logo">
                        </template>
                        
                        <template x-if="!item.content.image">
                           <div class="default-image">
                              {!! __i('--ie', 'image-picture', 'text-gray-300 w-5 h-5') !!}
                           </div>
                        </template>
                     </a>
                     <div class="screen"></div>
                  </div>
               </template>
            </div>
         </div>
      </div>
   </div>
     @script
     <script>
         Alpine.data('builder__linksSectionView', () => {
            return {
               colClass: 'grid-cols-1',
               sections: <?= json_encode($sections) ?>,
               toggleSensitiveContent: function() {
                  // Add logic to toggle sensitive content
               },
               processLink: function(link, pageId) {
                  // Add logic to process link
               },

               init(){
                  // console.log('lll', this.section, this.section.items)
                  //console.log(this.items)
                  var $this = this;
                  this.items = this.section.items;
                  window.addEventListener('section::' + this.section.uuid, (event) => {
                      $this.section = event.detail;
                     $this.sectionClasses();
                  });

                  window.addEventListener('sectionItem::' + this.section.uuid, (event) => {
                      $this.items = event.detail;
                  });
               }
            }
         });
     </script>
     @endscript
     
</div>