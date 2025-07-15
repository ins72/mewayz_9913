<div>

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
    <div class="{{ implode(' ', $classes) }} wire-section" x-intersect="__section_loaded($el)" x-data="builder__logosSectionView" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
      <div class="text-left inner-content" :class="{
         'text-left': section.settings.align == 'left' || section.settings.split_title,
         'text-center': !section.settings.split_title && section.settings.align == 'center',
         'text-right': !section.settings.split_title && section.settings.align == 'right',
         'w-boxed min-shape': section.section_settings.width == 'fit',
         'parallax': section.section_settings.parallax,
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
               <h2 class="t-4 pre-line [text-align:inherit] --text-color" x-text="section.content.title"></h2>
               </template>
               <template x-if="section.content.subtitle">
                  <p class="t-1 pre-line subtitle [text-align:inherit] --text-color" x-text="section.content.subtitle"></p>
               </template>
            </div>
            
            <div class="logos-container__items" :class="{
               'carousel-items-container carousel': section.settings.display == 'carousel',
               'auto-scroll': section.settings.display == 'carousel' && section.settings.auto_scroll,
               'grid': section.settings.display == 'grid' && !section.settings.display
            }" :style="{
               '--scroll-speed': section.settings.speed ? section.settings.speed  + 's' : '',
            }">
               <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
                  <div class="logos-container__item yena-section-items" :data-id="item.uuid">
                     <a class="no-dark" x-outlink="item.content.link" :style="{
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
         Alpine.data('builder__logosSectionView', () => {
            return {
               autoSaveTimer: null,
               generateSection: null,
               aiContent: null,


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

                  // object.subtitle();
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