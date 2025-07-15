<div>

    @php
        $classes = collect([
            'card-box',
            'section-width-fill',
            'box',
            'section-bg-wrapper', 
            'focus'
        ]);

        $classes = $classes->toArray();
    @endphp
    <div class="{{ implode(' ', $classes) }} wire-section" x-intersect="__section_loaded($el)" x-data="builder__cardSectionView" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
      <div class="text-left inner-content" :class="{
         'text-left': section.settings.align == 'left' || section.settings.split_title,
         'text-center': !section.settings.split_title && section.settings.align == 'center',
         'text-right': !section.settings.split_title && section.settings.align == 'right',
         'w-boxed': section.section_settings.width == 'fit',
         'parallax': section.section_settings.parallax,
        }" :style="{
         '--image-height': section.settings.desktop_height + 'px',
         '--image-height-mobile': section.settings.mobile_height + 'px',
        }">
        
        
         <div class="card-container !border-none w-boxed" :class="{
            'background': section.settings.background,
            'border': section.settings.border,
            'space-below': !section.settings.background && !section.settings.border,

            'left-title': section.settings.split_title,
            'card-1': section.settings.style == '1',
            'card-2': section.settings.style == '2',

            'left': section.settings.align == 'left',
            'center': section.settings.align == 'center',
            'right': section.settings.align == 'right',
         }">
            <div class="card-header">
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

            <div class="card-container [--scroll-speed:22.5s]" :class="`col-${section.settings.desktop_grid} col-mobile-${section.settings.mobile_grid}`">
               <template x-if="section.items.length > 0">
                  <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="index">
                     <div class="cursor-none card yena-section-items" :data-id="item.uuid" :class="{
                        'min-shape': section.settings.border,
                     }">
                        <a x-outlink="item.content.button_link" name="card-link" class="bottom" :class="{
                           'top': section.settings.layout_align == 'top',
                           'center': section.settings.layout_align == 'center',
                           'bottom': section.settings.layout_align == 'bottom',
                           '!p-0 !bg-transparent': !section.settings.background,
                        }">
                           <template x-if="item.content.button || item.content.title || item.content.text">
                              <div class="card-text" :class="{
                                 'min-shape': section.settings.style == '2',
                                 'glass': section.settings.glass
                              }">
                                 <div class="pre-line t-1" :class="{
                                    't-1': section.settings.text == 's' || section.settings.text == 'm',
                                    'small-size': section.settings.text == 's',
                                    '!font-bold': section.settings.text == 'm',
                                    't-3 !font-bold': section.settings.text == 'l',
                                    '!hidden': !item.content.title
                                 }" x-text="item.content.title"></div>
                                 
                                 <template x-if="section.settings.style == '2'">
                                    <div class="description mt-1 [text-align:inherit]" :class="{
                                       '!hidden': !item.content.text
                                    }">
                                       <p class="pre-line t-0 [text-align:inherit]" :class="{
                                          't-0': section.settings.text == 's' || section.settings.text == 'm',
                                          't-1': section.settings.text == 'l',
                                       }" x-text="item.content.text"></p>
                                    </div>
                                 </template>
                                 <template x-if="section.settings.style == '2' && item.content.button">
                                    <button class="card__button site-btn t-1 shape" onclick="window.open('javascript:void(0)', '_self')" :class="{
                                       'grey': item.content.color == 'default' || !item.content.color,
                                       'accent': item.content.color == 'accent', 
                                    }" x-text="item.content.button"></button>
                                 </template>
                              </div>
                           </template>
   
                           
                           <div class="card-image min-shape !h-[var(--image-height)]" :class="{
                              '!hidden': section.settings.style == '1' && !section.settings.enable_image
                           }">
                              <template x-if="item.content.image">
                                 <img :src="$store.builder.getMedia(item.content.image)" alt="" class="!h-[var(--image-height)]">
                              </template>
                              <template x-if="!item.content.image">
                                 <div class="default-image">
                                    {!! __i('--ie', 'image-picture', 'text-gray-300 w-5 h-5') !!}
                                 </div>
                              </template>
                           </div>
                           
                           <template x-if="section.settings.style == '1'">
                              <div class="card-text" :class="{
                                 '!hidden': !item.content.text
                              }">
                                 <div class="description mt-1 [text-align:inherit]">
                                    <p class="pre-line t-0 [text-align:inherit]" :class="{
                                       't-0': section.settings.text == 's' || section.settings.text == 'm',
                                       't-1': section.settings.text == 'l',
                                    }" x-text="item.content.text"></p>
                                 </div>
                              </div>
                           </template>
                           <template x-if="section.settings.style == '1' && item.content.button">
                              <button class="card__button site-btn t-1 shape" onclick="window.open('javascript:void(0)', '_self')" :class="{
                                 'grey': item.content.color == 'default' || !item.content.color,
                                 'accent': item.content.color == 'accent', 
                              }" x-text="item.content.button"></button>
                           </template>
                        </a>
                        <div class="screen"></div>
                     </div>
                  </template>
               </template>
               
            </div>
         </div>
      </div>
   </div>
     @script
     <script>
         Alpine.data('builder__cardSectionView', () => {
            return {
               items: {},
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
                           s.content.image = e;
                     });
                  });
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
               _save(){
                  var $this = this;
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