<div>

    @php
        $classes = collect([
            'review-box',
            'section-width-fill',
            'box',
            'section-bg-wrapper', 
            'focus'
        ]);

        $classes = $classes->toArray();
    @endphp
    <div class="{{ implode(' ', $classes) }} wire-section" x-intersect="__section_loaded($el)" x-data="builder__reviewSectionView" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
      <div class="text-left inner-content" :class="{
         'text-left': section.settings.align == 'left' || section.settings.split_title,
         'text-center': !section.settings.split_title && section.settings.align == 'center',
         'text-right': !section.settings.split_title && section.settings.align == 'right',
         'w-boxed min-shape': section.section_settings.width == 'fit',

         'parallax': section.section_settings.parallax,
        }">
        
        
         <div class="review-section w-boxed !border-none" :class="{
            'background': section.settings.background,
            'border': section.settings.border,
            'space-below': !section.settings.background && !section.settings.border,

            'left-title': section.settings.split_title,
            'layout-1': section.settings.style == '1',
            'layout-2': section.settings.style == '2',

            'left': section.settings.align == 'left',
            'center': section.settings.align == 'center',
            'right': section.settings.align == 'right',
         }">
            <div class="review-header">
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

            <div class="review-container" :style="{
               '--columns': section.settings.desktop_grid,
               '--mobile-columns': section.settings.mobile_grid,
               '--width': (!section.settings.desktop_width ? 250 : section.settings.desktop_width) + 'px',
               '--width-mobile': (!section.settings.mobile_width ? 250 : section.settings.mobile_width) + 'px',
               '--scroll-speed': section.settings.speed ? section.settings.speed  + 's' : '',
            }" :class="{
               'grid': section.settings.display == 'grid' || !section.settings.display,
               'auto-scroll': section.settings.display == 'carousel' && section.settings.auto_scroll,
               'carousel carousel-items-container': section.settings.display == 'carousel',
            }">
               <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
                  <div class="review yena-section-items" :data-id="item.uuid">
                     <a x-outlink="item.content.button_link" name="review-link" class="review-link min-shape row-reverse">
                        <div class="review-item">
                           <template x-if="section.settings.rating">
                              <div class="review-icon">
                                 <template x-if="section.settings.type == 'quote'">
                                    <span ><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m18.187 18.893c2.6255 0 4.8133-2.1878 4.8133-4.8133 0-2.6254-2.1878-4.8132-4.8133-4.8132h-0.6563c0.2188-1.3127 0.4376-1.7503 2.2972-3.6099-2.7348 0.21878-7.548 3.5006-6.5635 8.7514 0.547 2.8442 2.2972 4.4851 4.9226 4.4851z" fill="var(--accent)"></path><path d="m1.1307 14.626c0.43757 2.6254 2.2972 4.2663 4.9226 4.2663s4.8133-2.1878 4.8133-4.8133c0-2.6254-2.1879-4.8132-4.8133-4.8132h-0.65635c0.21878-1.3127 0.43757-1.7503 2.2972-3.6099-2.7348 0.32818-7.5481 3.6099-6.5635 8.9702z" fill="var(--accent)"></path></svg></span>
                                 </template>
                                 <template x-if="section.settings.type == 'stars'">
                                    <template x-for="(star, index) in ratings" :key="index">
                                       <span>
                                          <template x-if="item.content.rating>=star">
                                             <svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--accent)" fill-rule="evenodd"></path>
                                             </svg>
                                          </template>
                                          <template x-if="item.content.rating<star">
                                             <svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--c-mix-10)" fill-rule="evenodd"></path>
                                             </svg>
                                          </template>
                                       </span>
                                    </template>
                                 </template>
   
                              </div>
                           </template>
                           <div class="review-text">
                              <h3 class="pre-line t-1" :class="{
                                 't-1': section.settings.text == 's' || section.settings.text == 'm',
                                 'small-size': section.settings.text == 's',
                                 '!font-bold': section.settings.text == 'm',
                                 't-2 !font-bold': section.settings.text == 'l',
                              }" x-text="item.content.text"></h3>
                           </div>
                        </div>
                        <div class="reviewer-details">

                           <template x-if="section.settings.avatar">
                              <div class="reviewer-image min-shape square" :class="{
                                 'square': section.settings.shape == 'square',
                                 'avatar-image': section.settings.shape == 'circle',
                              }">
                                 <template x-if="item.content.image">
                                    <img :src="$store.builder.getMedia(item.content.image)" alt="">
                                 </template>
                                 <template x-if="!item.content.image">
                                    <div class="default-image">
                                       {!! __i('--ie', 'image-picture', 'text-gray-300 w-5 h-5') !!}
                                    </div>
                                 </template>
                              </div>
                           </template>
                           <div class="reviewer-description">
                              <p class="pre-line name t-0" x-text="item.content.title"></p>
                              <p class="pre-line about t-0" x-text="item.content.bio"></p>
                           </div>
                        </div>
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
         Alpine.data('builder__reviewSectionView', () => {
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
                     let section = ['item_title', 'item_text', 'item_bio'];
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
                           $this.section.settings.enable_image = true;
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
                  var $eventID = 'section::' + this.section.uuid;


                  $this.$dispatch($eventID, $this.section);
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
                     //   $this.dispatchSections();
                     $this._save();
                  });
               }
            }
         });
     </script>
     @endscript
     
</div>