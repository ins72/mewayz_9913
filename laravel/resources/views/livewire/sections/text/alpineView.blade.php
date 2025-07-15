

<div>
   <div x-data="builder__textSectionView" x-intersect="__section_loaded($el)" class="wire-section">
      <div class="text-box box !flex" :class="{
         'left': section.settings.align == 'left' || section.settings.split_title,
         'center': !section.settings.split_title && section.settings.align == 'center',
         'right': !section.settings.split_title && section.settings.align == 'right'
      }">
         <div class="text w-boxed" :class="{
            'left-title': section.settings.split_title
         }">
            
            <div class="text-header" :class="{
               'text-left': section.settings.align == 'left' || section.settings.split_title,
               'text-center': !section.settings.split_title && section.settings.align == 'center',
               'text-right': !section.settings.split_title && section.settings.align == 'right'
              }">
               <template x-if="section.content.label">
                  <div class="text-label">
                  <span class="card-label content-label t-0" x-text="section.content.label"></span>
                  </div>
               </template>


               <h2 class="t-4 pre-line" :class="{
                'text-left': section.settings.align == 'left' || section.settings.split_title,
                'text-center': !section.settings.split_title && section.settings.align == 'center',
                'text-right': !section.settings.split_title && section.settings.align == 'right'
               }" x-text="section.content.title"></h2>
            </div>

               
            <div>
               <template x-if="section.content.subtitle">
                  <div class="tiny-content-init" :class="{
                     'text-column': section.settings.split == '2',
                     'text-left': section.settings.align == 'left' || section.settings.split_title,
                     'text-center': !section.settings.split_title && section.settings.align == 'center',
                     'text-right': !section.settings.split_title && section.settings.align == 'right'
                  }">
                   <p class="t-1 pre-line subtitle" x-html="window.marked(section.content.subtitle)" :class="{
                      'text-left': section.settings.align == 'left' || section.settings.split_title,
                      'text-center': !section.settings.split_title && section.settings.align == 'center',
                      'text-right': !section.settings.split_title && section.settings.align == 'right'
                     }"></p>
                  </div>
               </template>
            </div>
         </div>
       </div>
    </div>
    @script
    <script>
        Alpine.data('builder__textSectionView', () => {
           return {
               autoSaveTimer: null,
               generateSection: null,
               aiContent: null,
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

               regenerateAi(content = []){
                  let $this = this;
                  let $content = {
                     ...$this.aiContent,
                     ...content,
                  };
                  let ai = new Ai($this.section);
                  ai.setPrompt($content);
                  let section = ['subtitle'];

                  section.forEach(sec => {
                     ai.setTake('text');
                     ai.run(function(e){
                        if(e.includes('--ai-start-')){
                           $this.section.content[sec] = '';
                        }

                        e = e.replace('--ai-start-', '');
                        $this.section.content[sec] += e;
                        // $this.editor.value($this.section.content[sec]);
                     });
                  });


                  // object.subtitle();
               },
               init(){
                  var $this = this;

                  this.$watch('section' , (value, _v) => {
                     $this._save();
                     $this.sectionClasses();
                  });
               }
           }
        });
    </script>
    @endscript
</div>