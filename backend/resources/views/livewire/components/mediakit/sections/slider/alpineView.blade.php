<div>
   <div>

      @php
          $classes = collect([
              'gallery-box',
              'section-width-fill',
              'box',
              'section-bg-wrapper', 
              'focus'
          ]);
  
          $classes = $classes->toArray();
      @endphp
      <div class="{{ implode(' ', $classes) }} wire-section !bg-transparent" x-data="builder__sliderSectionView">
        <div class="inner-content remove-before" style="--image-height: 300px; --image-height-mobile: 250px;">
          
          
           <div class="gallery-container w-boxed xpx-0 !pt-0" :class="{
              'show-border': section.settings.border,
              'left-title': section.settings.split_title,
           }" :style="{
              '--grid-height': section.settings.desktop_height + 'px',
              '--grid-height-mobile': section.settings.desktop_height + 'px',
              '--grid-width': section.settings.desktop_width + 'px',
              '--grid-width-mobile': section.settings.desktop_width + 'px',
              '--grid-count': section.settings.desktop_grid,
              '--grid-count-mobile': section.settings.desktop_grid,
              }">
              
              <div class="gallery-container__wrapper">
                  <div class="carousel-scroller right">
                     <div class="carousel-scroller__left">
                        <i class="ph ph-caret-left"></i>
                     </div>
                     
                     <div class="carousel-scroller__right">
                        <i class="ph ph-caret-right"></i>
                     </div>
                  </div>
                  <div class="gallery-container__items carousel" :class="{
                     'auto-scroll': section.settings.auto_scroll,
                  }" :style="{
                     '--scroll-speed': section.settings.speed ? section.settings.speed  + 's' : ''
                  }">
                     <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
                        <a class="gallery-container__item yena-section-items" :data-id="item.uuid" :class="{
                           '!rounded-none': site.settings.corners == 'straight',
                           '!rounded-xl': site.settings.corners == 'round',
                           '!rounded-3xl': site.settings.corners == 'rounded',
                        }">
                              
                           <template x-if="item.image">
                              <img :src="$store.builder.getMedia(item.image)" alt="" class="light-logo">
                           </template>
                           
                           <template x-if="!item.image">
                              <div class="default-image">
                                 {!! __i('--ie', 'image-picture', 'text-gray-300 w-5 h-5') !!}
                              </div>
                           </template>
                           <div class="screen"></div>
                        </a>
                     </template>
                  </div>
              </div>
           </div>
        </div>
     </div>
     <div class="gallery-viewer">
        <div class="gallery-viewer__controllers">
           <div class="controllers__left"></div>
           <div class="controllers__right"></div>
           <div class="controllers__bottom"></div>
        </div>
        <div class="gallery-viewer__top">
           <div class="top__indexer">
              <div class="indexer__counter">
                 <div class="counter__strip">
                    <template x-for="(item, index) in section.items" :key="item.uuid">
                     <span x-text="(index+1)"></span>
                    </template>
                 </div>
                 <span class="counter__filler" x-text="section.items.length"></span>
              </div>
              / 
              <div class="indexer__total" x-text="section.items.length"></div>
           </div>
           <button class="top__close-btn">
              <i class="ph ph-x text-lg"></i>
           </button>
        </div>
        <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
           <div class="gallery-viewer__image">
              <div class="image__container">
                 <div class="default-image">
                    <template x-if="item.image">
                       <img :src="$store.builder.getMedia(item.image)" alt="">
                    </template>
                    
                    <template x-if="!item.image">
                       <div class="default-image">
                          {!! __i('--ie', 'image-picture', 'text-gray-300 w-5 h-5') !!}
                       </div>
                    </template>
                 </div>
              </div>
           </div>
        </template>
        <div class="gallery-viewer__bottom">
           <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
              <div class="bottom__thumbnail">
                 <div class="default-image">
                    <template x-if="item.image">
                       <img :src="$store.builder.getMedia(item.image)" alt="">
                    </template>
                    
                    <template x-if="!item.image">
                       <div class="default-image">
                          {!! __i('--ie', 'image-picture', 'text-gray-300 w-5 h-5') !!}
                       </div>
                    </template>
                 </div>
              </div>
           </template>
        </div>
     </div>
       @script
       <script>
           Alpine.data('builder__sliderSectionView', () => {
              return {
                 items: {},
                 generateSection: null,
                 aiContent: null,
                 autoSaveTimer: null,
                 sectionClass: function(){
                    var $class = {
                       ...this.$store.builder.generateSectionClass(this.site, this.section),
                       'min-shape': false,
                       'section-bg-wrapper': false,
                    };
  
                    return $class;
                 },
                 sectionStyles: function(){
                  return this.$store.builder.generateSectionStyles(this.section);
                 },
  
                 sectionClasses: function(){
                  this.sectionClass();
                  this.sectionStyles();
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
                 }
              }
           });
       </script>
       @endscript
       
   </div>
</div>