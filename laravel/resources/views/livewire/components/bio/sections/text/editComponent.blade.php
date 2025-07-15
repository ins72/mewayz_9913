<div >
    <div x-data="builder__section_Text">
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
                       <li class="!pl-0" x-text="!section.content.title ? '{{ __('Text') }}' : section.content.title"></li>
                       <li class="!flex items-center !justify-center">
                           <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" @click="deleteSection(section)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
                       </li>
                    </ul>
                 </div>
                <div class="container-small sticky ![overflow:initial] lg:!overflow-y-auto">
                    <div class="tab-content-box mt-0">
                        <div class="tab-content">
                            <div x-cloak x-show="__tab == 'content'" data-tab-content>
                                <x-livewire::components.bio.sections.text.partial.edit.content/>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
          </div>
        
    </div>


  @script
  <script>
      Alpine.data('builder__section_Text', () => {
         return {
            autoSaveTimer: null,
            __tab: 'content',
            __page: '-',
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
          
            init(){
               let $this = this;

                this.$watch('section' , (value) => {
                    $this._save();
                });
            }
         }
      });
  </script>
  @endscript
</div>