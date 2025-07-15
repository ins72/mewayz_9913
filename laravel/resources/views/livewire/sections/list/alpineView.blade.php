<div class="w-[100%] section-width-fill section-bg-wrapper list-box" :class="{
   'section-width-fill': section.section_settings.width == 'fill',
   'lr-padding': section.section_settings.width == 'fit',
}">

    @php
        $classes = collect([
            'list-box',
            'section-width-fill',
            'box',
            'section-bg-wrapper', 
            'focus'
        ]);

        $classes = $classes->toArray();
    @endphp
      <section class="list-content section-content wire-section" x-intersect="__section_loaded($el)" x-data="builder__listSectionView" :class="{
         'w-boxed min-shape': section.section_settings.width == 'fit'
      }">
         <div class="list-box section-bg-wrapper" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
      
            <div class="inner-content" :class="{
               'w-boxed min-shape': section.section_settings.width == 'fit',
               'text-left': section.settings.align == 'left' || section.settings.split_title,
               'text-center': !section.settings.split_title && section.settings.align == 'center',
               'text-right': !section.settings.split_title && section.settings.align == 'right',
               'align-items-start': section.section_settings.align == 'top',
               'align-items-center': section.section_settings.align == 'center',
               'align-items-end': section.section_settings.align == 'bottom',
               'parallax': section.section_settings.parallax,
            }">

            <div class="list-section w-boxed !border-none section-component" :class="{
               'background': section.settings.background,
               'no-background': section.settings.border,
               'space-below': !section.settings.background && !section.settings.border,

               'left-title': section.settings.split_title,
               'layout-1': section.settings.style == '1',
               'layout-2': section.settings.style == '2',

               'left': section.settings.align == 'left',
               'center': section.settings.align == 'center',
               'right': section.settings.align == 'right',
            }">
               <div class="list-header">
                  <div class="[text-align:inherit]">
                  <template x-if="section.content.label">
                     <span class="text-[var(--foreground)] bg-[var(--c-mix-1)] px-[9px] py-[3px] mb-1 rounded-[var(--shape)] t-0" x-text="section.content.label"></span>
                  </template>
                  </div>
                  <template x-if="section.content.title">
                  <h2 class="t-4 pre-line [text-align:inherit] --text-color" x-text="section.content.title"></h2>
                  </template>
                  <template x-if="section.content.subtitle">
                     <p class="t-1 pre-line subtitle [text-align:inherit] --text-color" x-text="section.content.subtitle"></p>
                  </template>
               </div>

               <div class="list-container section-container grid [--scroll-speed:22.5s]" :class="{
                  'col-1': section.settings.desktop_grid == '1',
                  'col-2': section.settings.desktop_grid == '2',
                  'col-3': section.settings.desktop_grid == '3',
                  'col-mobile-1': section.settings.mobile_grid == '1',
                  'col-mobile-2': section.settings.mobile_grid == '2',

                  'grid': section.settings.display == 'grid',
                  'carousel carousel-items-container': section.settings.display == 'carousel'
               }" :style="{
                  '--columns': section.settings.desktop_grid,
                  '--mobile-columns': section.settings.mobile_grid
               }">
                  <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
                     <div class="list section-item yena-section-items" :data-id="item.uuid" :style="{
                        '--width': section.settings.desktop_width + 'px',
                        '--mobile-width': section.settings.mobile_width + 'px',
                     }">
                        <a x-outlink="item.content.button_link" name="item-link" class="item-link min-shape" :class="{
                           'row-reverse': section.settings.style == '2',
                           'display-block': section.settings.style == '2' && section.settings.layout == 'right',

                           'column': section.settings.style == '1' && section.settings.layout == 'right',
                           '!bg-transparent': section.settings.border,
                        }">
                        <template x-if="section.settings.icon">
                           <div class="list-image section-item-image min-shape" :style="{
                              'height': section.settings.desktop_height + 'px',
                              'width': section.settings.desktop_height + 'px',
                           }" :class="{
                              'avatar-image': section.settings.shape == 'circle',
                           }">
                              <div class="list-icon overflow-hidden" :class="{
                                 'grey': section.settings.border
                              }">
                                 <template x-if="item.content.icon_type == 'image' || !item.content.icon_type">
                                    <div class="w-[100%] h-full flex items-center justify-center">
                                       <template x-if="item.content.image">
                                          <img :src="$store.builder.getMedia(item.content.image)" class="w-[100%] h-[100%] !rounded-none object-cover" alt="">
                                       </template>
                                       <template x-if="!item.content.image">
                                          <div class="default-image w-[100%] h-full">
                                             {!! __i('--ie', 'image-picture', 'text-gray-300 w-5 h-5') !!}
                                          </div>
                                       </template>
                                    </div>
                                 </template>
                                 <template x-if="item.content.icon_type == 'icon'">
                                    <div class="flex">
                                       <template x-if="item.content.icon">
                                          <i :class="item.content.icon" class="ph text-xl"></i>
                                       </template>
                                       <template x-if="!item.content.icon">
                                          <div class="default-image">
                                             {!! __i('--ie', 'image-picture', 'text-gray-300 w-5 h-5') !!}
                                          </div>
                                       </template>
                                    </div>
                                 </template>
                              </div>
                           </div>
                        </template>
                           <div class="list-text">
                              <h3 class="pre-line t-1 small-size" x-text="item.content.title" :class="{
                                 
                                 't-1': section.settings.text == 's' || section.settings.text == 'm',
                                 'small-size': section.settings.text == 's',
                                 '!font-bold': section.settings.text == 'm',
                                 't-2 !font-bold': section.settings.text == 'l',
                              }"></h3>
                              <template x-if="item.content.text">
                                 <p class="pre-line t-0" :class="{
                                    't-0': section.settings.text == 's' || section.settings.text == 'm',
                                    't-2': section.settings.text == 'l',
                                 }" x-text="item.content.text"></p>
                              </template>
                           </div>
                        </a>
                        <div class="screen"></div>
                     </div>
                  </template>
               </div>
            </div>
            </div>
         </div>
      </section>
     @script
     <script>
         Alpine.data('builder__listSectionView', () => {
            return {
               items: {},
               ratings: [1,2,3,4,5],
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
                     let section = ['item_title', 'item_text'];
                     s.content.rating = Math.floor(Math.random() * 5) + 1;
                     section.forEach(sec => {
                           ai.setTake(sec);

                           sec = sec.replace('item_', '');
                           ai.run(function(e){
                              if(e.includes('--ai-start-')){
                                 s.content[sec] = '';
                              }

                              e = e.replace('--ai-start-', '');
                              s.content[sec] += e;
                           });
                     });

                     ai.image(function(e){
                           s.content.icon_type = 'image';
                           s.content.image = e;
                     });
                  });
               },
               sectionClass: function(){
                return this.$store.builder.generateSectionClass(this.site, this.section);
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