<div >
    <div x-data="builder__sectionSkillbar">
        
          <div x-cloak x-show="__page == '-'">
            <div class="website-section !block">
                <div class="design-navbar">
                    <ul >
                        <li class="close-header !flex">
                          <a @click="closePage('pages')">
                            <span>
                                {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                            </span>
                          </a>
                       </li>
                       <li class="!pl-0" x-text="!section.content.title ? '{{ __('Skillbar') }}' : section.content.title"></li>
                       <li class="!flex items-center !justify-center">
                           <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" @click="deleteSection(section)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
                       </li>
                    </ul>
                 </div>
                <div>
                    <div class="container-small tab-content-box mt-0 ![overflow:initial] lg:!overflow-y-auto">
                        <div class="tab-content">
                            <div x-cloak x-show="__tab == 'content'" data-tab-content>
                                <x-livewire::components.bio.sections.skillbar.partial.edit.content/>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
          </div>
        
    </div>


  @script
  <script>
      Alpine.data('builder__sectionSkillbar', () => {
         return {
            autoSaveTimer: null,
            __tab: 'content',
            __page: '-',

            tippyOptions: {
                content: () => $refs.template.innerHTML,
                allowHTML: true,
                appendTo: $root,
                maxWidth: 360,
                interactive: true,
                trigger: 'click',
                animation: 'scale',
            },

            __delete_item(item_id){
                var index = 0;

                this.section.items.forEach(element => {
                    if(item_id == element.uuid){
                        this.section.items.splice(index, 1);
                    }

                    index++;
                });

                // this.__page = '-';

                this.$dispatch('section::deleteItem', {
                   item: item_id
                });
            },

            createItem(){
                let $this = this;

                let count = $this.section.items.length + 1;

                let item = {
                    uuid: this.$store.builder.generateUUID(),
                    content: {
                        'skillbar': 32,
                        'title': 'Skillbar ' + count,
                    },
                    settings: {
                        animation: '-',
                    },
                };
                $this.section.items.push(item);
                let $index = $this.section.items.length-1;

                this.$dispatch('section::create_section_item', {
                    item: item,
                    section_id: this.section.uuid,
                });
            },

            _save(){
                let $this = this;
                let $eventID = 'section::' + this.section.uuid;


                $this.$dispatch($eventID, $this.section);
                clearTimeout($this.autoSaveTimer);

                $this.autoSaveTimer = setTimeout(function(){
                    $this.$store.builder.savingState = 0;

                    event = new CustomEvent("builder::save_sections_and_items", {
                        detail: {
                            section: $this.section,
                            js: '$store.builder.savingState = 2',
                        }
                    });

                    window.dispatchEvent(event);
                }, $this.$store.builder.autoSaveDelay);
            },

            dispatchSections(){
                let $this = this;
                $this.$dispatch('sectionItem::' + this.section.uuid, $this.section.items);
            },
            initSort(){
               var $this = this;
               if(this.$refs.sortable_wrapper){
                  this.$store.builder.generalSortable(this.$refs.sortable_wrapper, {
                     handle: '.handle'
                  }, this.$refs.sortable_template, this.section.items, function(){
                    
                  });
               }
            },
          
            init(){
               let $this = this;

               $this.initSort();
                if(this.section.items === undefined || this.section.items == null){
                    this.section.items = [];
                }

                this.$watch('section' , (value) => {
                    $this._save();
                });

               this.$watch('section.items' , (value, _v) => {
                    $this.dispatchSections();
                    $this._save();
               });
            }
         }
      });
  </script>
  @endscript
</div>