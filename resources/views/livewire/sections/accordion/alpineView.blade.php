

<div class="w-[100%] section-width-fill box section-bg-wrapper focus accordion-box" :class="{
      'section-width-fill': section.section_settings.width == 'fill',
      'lr-padding': section.section_settings.width == 'fit',
   }">
   <div class="wire-section" x-intersect="__section_loaded($el)" x-data="builder__accordionSectionView">
       <section class="section-content" :class="{
        'w-boxed min-shape': section.section_settings.width == 'fit'
    }">
          <div class="accordion-box section-bg-wrapper transparent color" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
             <div class="inner-content" :class="{
               'align-items-start': section.section_settings.align == 'top',
               'align-items-center': section.section_settings.align == 'center',
               'align-items-end': section.section_settings.align == 'bottom',
               'min-shape': section.section_settings.width == 'fit',
               'parallax': section.section_settings.parallax,
             }">
                <div class="p-0 w-boxed">
                   <div class="accordion-container section-component w-boxed background no-border left" :class="{
                    'left': section.settings.align == 'left' || section.settings.split_title,
                    'center': !section.settings.split_title && section.settings.align == 'center',
                    'right': !section.settings.split_title && section.settings.align == 'right',

                    'background': section.settings.background,
                    'no-border': section.settings.background,
                    
                    'no-background': !section.settings.background,
                    'left-title': section.settings.split_title
                    }">
                      <div class="accordion-title content-heading">
                         <div class="accordion-label" :class="{
                          'text-left': section.settings.align == 'left' || section.settings.split_title,
                          'text-center': !section.settings.split_title && section.settings.align == 'center',
                          'text-right': !section.settings.split_title && section.settings.align == 'right'
                         }">
                          <template x-if="section.content.label">
                             <span class="card-label content-label t-0" x-text="section.content.label"></span>
                          </template>
                         </div>
                         <h2 class="t-4 pre-line --text-color" :class="{
                          'text-left': section.settings.align == 'left' || section.settings.split_title,
                          'text-center': !section.settings.split_title && section.settings.align == 'center',
                          'text-right': !section.settings.split_title && section.settings.align == 'right'
                         }" x-text="section.content.title"></h2>
                          <template x-if="section.content.subtitle">
                             <p class="t-1 pre-line subtitle --text-color" x-text="section.content.subtitle" :class="{
                                'text-left': section.settings.align == 'left' || section.settings.split_title,
                                'text-center': !section.settings.split_title && section.settings.align == 'center',
                                'text-right': !section.settings.split_title && section.settings.align == 'right'
                               }"></p>
                          </template>
                      </div>
                      <div class="all-accordions section-container">
                         <div class="accordion active">
                            
                          <template x-for="(item, index) in window._.sortBy(items, 'position')" :key="index">
                             <div class="accordion-item section-item no-link bg yena-section-items" :data-id="item.uuid" x-data="{open:false}" :class="{
                                'active': open,
                                'bg': section.settings.background,
                                'border': !section.settings.background
                             }">
                                <button class="accordion-header" :class="{'active': open}" @click="open = ! open">
                                   <p class="pre-line">
                                      <span class="t-1 pre-line" x-text="item.content.title"></span>
                                   </p>
                                   <span>
                                      <template x-if="section.settings.icon == 'arrow'">
                                         <div>
                                            <template x-if="!open">
                                               <div>
                                                  {!! __i('Arrows, Diagrams', 'Arrow.5', '!w-6 !h-6') !!}
                                               </div>
                                            </template>
                                            <template x-if="open">
                                               <div>
                                                  {!! __i('Arrows, Diagrams', 'Arrow.2', '!w-6 !h-6') !!}
                                               </div>
                                            </template>
                                         </div>
                                      </template>
                                      <template x-if="section.settings.icon == 'plus'">
                                         <div>
                                            <template x-if="!open">
                                               <div>
                                                  {!! __i('custom', 'plus', '!w-5 !h-5') !!}
                                               </div>
                                            </template>
                                            <template x-if="open">
                                               <div>
                                                  {!! __i('custom', 'minus', '!w-4 !h-4') !!}
                                               </div>
                                            </template>
                                         </div>
                                      </template>
                                   </span>
                                </button>
                                <div class="accordion-body" :class="{'active': open}">
                                   <p class="t-1 pre-line" x-text="item.content.text"></p>
                                </div>
                             </div>
                          </template>
                         </div>
                      </div>
                   </div>
                </div>
             </div>
          </div>
       </section>
    </div>
    @script
    <script>
        Alpine.data('builder__accordionSectionView', () => {
           return {
            items: {},
            generateSection: null,
            aiContent: null,
            autoSaveTimer: null,
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
                this.$watch('section' , (value, _v) => {
                    $this._save();
                    $this.sectionClasses();
                });

               this.$watch('section.items' , (value, _v) => {
                  //   $this.$dispatch($eventIDItem, $this.section.items);
                    $this._save();
               });
              }
           }
        });
    </script>
    @endscript
</div>