<div wire:ignore>

    <div x-data="_builder__sectionBooking">

        <div x-show="__page == 'add'">
            <div>
                <x-livewire::components.bio.sections.booking.partial.add />
            </div>
        </div>
      
        <div x-cloak x-show="__page == '-'">
          <div class="banner-section !block">
              <div>
          
                  <div class="banner-navbar">
                      <ul >
                          <li class="close-header">
                          <a @click="closePage('pages')">
                              <span>
                                  {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                              </span>
                          </a>
                      </li>
                      <li>{{ __('Bookings') }}</li>
                      <li></li>
                      </ul>
                  </div>
                  <div class="sticky container-small">
                      <div class="tab-link">
                          <ul class="tabs">
                          <li class="tab !w-full" @click="__tab = 'content'" :class="{'active': __tab == 'content'}">{{ __('Content') }}</li>
                          {{-- <li class="tab !w-full" @click="__tab = 'style'" :class="{'active': __tab == 'style'}">{{ __('Style') }}</li> --}}
                          </ul>
                      </div>
                  </div>
                  <div class="container-small tab-content-box">
                      <div class="tab-content">
                          <div x-cloak x-show="__tab == 'content'" data-tab-content>
                              <x-livewire::components.bio.sections.booking.partial.edit.content/>
                          </div>
                          <div x-cloak x-show="__tab == 'style'" data-tab-content>
                              {{-- <x-livewire::components.bio.sections.booking.partial.edit.style /> --}}
                          </div>
                      </div>
                  </div>
              </div>
            </div>
        </div>
    </div>

  @script
  <script>
      Alpine.data('_builder__sectionBooking', () => {
         return {
            autoSaveTimer: null,
            __tab: 'content',
            __page: '-',
            skeleton: {
                label: '',
                title: '',
            },

            itemSkeleton: {

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
                        'title': '{{ __('Add title') }}',
                        'text': '{{ __('Add text here') }}',
                    },
                };
                $this.section.items.push(item);
                var $index = $this.section.items.length-1;

                this.$dispatch('section::create_section_item', {
                    item: item,
                    section_id: this.section.uuid,
                });
            },

            dispatchSections(){
                var $this = this;
                $this.$dispatch('sectionItem::' + this.section.uuid, $this.section.items);
            },
            registerEvents(){
                let $this = this;
                window.addEventListener('section:content:' + this.section.uuid, (event) => {
                    $this.__page = '-';
                    $this.__tab = 'content';
                });
                window.addEventListener('section:style:' + this.section.uuid, (event) => {
                    $this.__page = '-';
                    $this.__tab = 'style';
                });
                window.addEventListener('section:section:' + this.section.uuid, (event) => {
                    $this.__page = 'section';
                });
                window.addEventListener('section:i:' + this.section.uuid, (event) => {
                    $this.__page = 'section::list::' + event.detail;
                });
               window.addEventListener("reaiSection:" + this.section.uuid, (event) => {
                    let detail = event.detail;

                    $this.generateSection = detail.section;
                    $this.aiContent = detail.prompt;

                    $this.regenerateAi();
               });
            },

            init(){
                this.registerEvents();
               //if(!this.section) this.section = this.skeleton;
               var $this = this;
               var $eventID = 'section::' + this.section.uuid;

               //this.section.items = window._.sortBy(this.section.items, 'position');

                if(this.section.items === undefined || this.section.items == null){
                    this.section.items = [];
                }

                if(this.section.section_settings === undefined || this.section.section_settings == null){
                    this.section.section_settings = {
                        color: 'transparent',
                    };
                }
            }
         }
      });
  </script>
  @endscript
</div>