<?php
    use App\Models\SectionItem;
    use App\Livewire\Actions\BroadcastBuilder;
    use function Livewire\Volt\{state, mount, rules, on};
?>

@php
    $animation = [
        '-',
        'bounce',
        'tada',
        'wobble',
        'swing',
        'shakeX',
        'shakeY',
        'rubberBand',
        'pulse',
        'flash',
        'jello',
        'bounceIn',
        'fadeIn',
        'flipInX',
        'flipInY'
    ];

    $animation_runs = [
        'repeat-1',
        'repeat-2',
        'repeat-3',
        'infinite',
    ];

    $link_style = [
        '-',
        'description',
        'feature',
        'overlay',
        'banner',
        'botton',
        'display',
        'hover',
    ];
    $styles = [];
    foreach ($link_style as $key => $value) {
        $item = [
            'style' => $value,
            'link' => gs('assets/image/others/link-styles', $value . '.png')
        ];
        $styles[] = $item;
    }
@endphp


<div >
    <div x-data="builder__sectionLinks">
        <template x-for="item in section.items" :key="item.uuid">
            <div x-show="__page == 'section::links::' + item.uuid">
                <div>
                    <x-livewire::components.bio.sections.links.partial.edit.item />
                </div>
            </div>
          </template>
        
          {{-- <div x-show="__page == 'section'">
              <div>
                 <x-livewire::components.builder.parts.section />
              </div>
          </div> --}}
        
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
                       <li class="!pl-0" x-text="!section.content.title ? '{{ __('Links') }}' : section.content.title"></li>
                       <li class="!flex items-center !justify-center">
                           <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" @click="deleteSection(section)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
                       </li>
                    </ul>
                 </div>
                <div class="container-small sticky">
                    <div>
                        <div class="tab-link">
                            <ul class="tabs">
                            <li class="tab !w-[100%]" @click="__tab = 'content'" :class="{'active': __tab == 'content'}">{{ __('Content') }}</li>
                            <li class="tab !w-[100%]" @click="__tab = 'style'" :class="{'active': __tab == 'style'}">{{ __('Style') }}</li>
                            {{-- <li class="tab !w-[100%]" @click="__tab = 'settings'" :class="{'active': __tab == 'settings'}">{{ __('Settings') }}</li> --}}
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content-box">
                        <div class="tab-content">
                            <div x-cloak x-show="__tab == 'content'" data-tab-content>
                                <x-livewire::components.bio.sections.links.partial.edit.content/>
                            </div>
                            <div x-cloak x-show="__tab == 'style'" data-tab-content>
                                <x-livewire::components.bio.sections.links.partial.edit.style/>
                            </div>
                            {{-- <div x-cloak x-show="__tab == 'settings'" data-tab-content></div> --}}
                        </div>
                    </div>
                </div>
              </div>
          </div>
        
    </div>


  @script
  <script>
      Alpine.data('builder__sectionLinks', () => {
         return {
            autoSaveTimer: null,
            __tab: 'content',
            __page: '-',
            animations: {!! collect($animation)->toJson() !!},
            animation_runs: {!! collect($animation_runs)->toJson() !!},
            styles: {!! collect($styles)->toJson() !!},
            studio: {!! collect(config('yena.studio'))->toJson() !!},

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

                this.__page = '-';

                this.$dispatch('section::deleteItem', {
                   item: item_id
                });
            },

            createItem(){
                var $this = this;

                let item = {
                    uuid: this.$store.builder.generateUUID(),
                    content: {
                        'image': null,
                        'link': '',
                        'title': 'Link ' + (this.section.items.length + 1),
                    },
                    settings: {
                        animation: '-',
                    },
                };
                $this.section.items.push(item);
                var $index = $this.section.items.length-1;

                this.$dispatch('section::create_section_item', {
                    item: item,
                    section_id: this.section.uuid,
                });
            },

            _save(){
                var $this = this;
                var $eventID = 'section::' + this.section.uuid;


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
                var $this = this;
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
               //if(!this.section) this.section = this.skeleton;
               var $this = this;
               $this.initSort();
               var $eventID = 'section::' + this.section.uuid;
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