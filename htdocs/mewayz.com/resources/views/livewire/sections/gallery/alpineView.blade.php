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
    <div class="{{ implode(' ', $classes) }} wire-section" x-intersect="__section_loaded($el)" x-data="builder__gallerySectionView" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
      <div class="inner-content" :class="{
         'w-boxed min-shape': section.section_settings.width == 'fit',
         'parallax': section.section_settings.parallax,
      }" style="--image-height: 300px; --image-height-mobile: 250px;">
        
        
         <div class="gallery-container w-boxed" :class="{
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
            <div class="gallery-header">
               <div class="[text-align:inherit]">
                <template x-if="section.content.label">
                   <span class="gallery-label t-0" x-text="section.content.label"></span>
                </template>
               </div>
               <template x-if="section.content.title">
               <h2 class="t-4 pre-line [text-align:inherit] --text-color" x-text="section.content.title"></h2>
               </template>
               <template x-if="section.content.subtitle">
                  <p class="t-1 pre-line subtitle [text-align:inherit] --text-color" x-text="section.content.subtitle"></p>
               </template>
            </div>
            
            <div class="gallery-container__wrapper">
               <template x-if="section.settings.display == 'carousel'">
                  <div class="carousel-scroller right">
                     <div class="carousel-scroller__left">
                        <i class="ph ph-caret-left"></i>
                     </div>
                     
                     <div class="carousel-scroller__right">
                        <i class="ph ph-caret-right"></i>
                     </div>
                  </div>
               </template>
               <div class="gallery-container__items" :class="{
                  'auto-scroll': section.settings.display == 'carousel' && section.settings.auto_scroll,
                  'carousel': section.settings.display == 'carousel',
                  'grid': section.settings.display == 'grid' || !section.settings.display
               }" :style="{
                  '--scroll-speed': section.settings.speed ? section.settings.speed  + 's' : ''
               }">
                  <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
                     <a class="gallery-container__item yena-section-items" :data-id="item.uuid">
                           
                        <template x-if="item.content.image">
                           <img :src="$store.builder.getMedia(item.content.image)" alt="" class="light-logo">
                        </template>
                        
                        <template x-if="!item.content.image">
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
                  <template x-if="item.content.image">
                     <img :src="$store.builder.getMedia(item.content.image)" alt="">
                  </template>
                  
                  <template x-if="!item.content.image">
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
                  <template x-if="item.content.image">
                     <img :src="$store.builder.getMedia(item.content.image)" alt="">
                  </template>
                  
                  <template x-if="!item.content.image">
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
         Alpine.data('builder__gallerySectionView', () => {
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
               _save(){
                  var $this = this;
                  // var $eventID = 'section::' + this.section.uuid;
                  // $this.$dispatch($eventID, $this.section);
                  clearTimeout($this.autoSaveTimer);

                  $this.autoSaveTimer = setTimeout(function(){
                     $this.$store.builder.savingState = 0;

                     let event = new CustomEvent("builder::save_sections_and_items", {
                           detail: {
                              section: $this.section,
                              js: '$store.builder.savingState = 2',
                           }
                     });

                     window.dispatchEvent(event);
                  }, $this.$store.builder.autoSaveDelay);
               },

               regenerateAi(content = []){
                  let $this = this;
                  let $content = {
                     ...$this.aiContent,
                     ...content,
                  };
                  let ai = new Ai($this.section);
                  ai.setPrompt($content);
                  let section = ['title', 'subtitle'];

                  section.forEach(sec => {
                     if(!$this.section.content[sec]) return;
                        ai.setTake(sec);
                        ai.run(function(e){
                           if(e.includes('--ai-start-')){
                              $this.section.content[sec] = '';
                           }

                           e = e.replace('--ai-start-', '');
                           $this.section.content[sec] += e;
                        });
                  });

                  $this.section.items.forEach((s) => {
                     ai.image(function(e){
                           s.content.image = e;
                     });
                  });
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

                  this.$watch('section' , (value, _v) => {
                     $this._save();
                     $this.sectionClasses();
                  });

                  this.$watch('section.items' , (value, _v) => {
                     // $this.dispatchSections();
                     $this._save();
                  });
               }
            }
         });
     </script>
     @endscript
     
</div>