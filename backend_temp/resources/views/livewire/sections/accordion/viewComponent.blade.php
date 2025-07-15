<?php
    use function Livewire\Volt\{state, mount, on, placeholder};

   placeholder('
      <div class="p-5 mt-1 w-boxed">
         <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
         <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
         <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      </div>
   ');
    state([
        'section',
        '__section' => [],
        '__items' => [],
    ]);

    on([
        /*'sectionMediaEvent:{section.uuid}' => function($public, $image){
            $this->media = $public;
        },*/
    ]);

    mount(function(){
        $this->__section = $this->section->toArray();
        $this->__items = $this->section->getItems()->orderBy('id', 'ASC')->orderBy('position', 'DESC')->get()->toArray();
    });

    // Methods
?>


<div>

    @php
        $classes = collect([
            'accordion-box',
            'section-width-fill',
            'box',
            'section-bg-wrapper', 
            'focus'
        ]);

        $classes = $classes->toArray();
    @endphp
    <div class="{{ implode(' ', $classes) }}" :class="{
      'section-width-fill': section.section_settings.width == 'fill',
      'lr-padding': section.section_settings.width == 'fit',
      }" x-data="builder__accordionSectionView">
        <section class="section-content" :class="{
         'w-boxed min-shape': section.section_settings.width == 'fit'
     }">
           <div class="accordion-box section-bg-wrapper transparent color" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
              <div class="inner-content">
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
                          <h2 class="t-4 pre-line" :class="{
                           'text-left': section.settings.align == 'left' || section.settings.split_title,
                           'text-center': !section.settings.split_title && section.settings.align == 'center',
                           'text-right': !section.settings.split_title && section.settings.align == 'right'
                          }" x-text="section.content.title"></h2>
                           <template x-if="section.content.subtitle">
                              <p class="t-1 pre-line subtitle" x-text="section.content.subtitle" :class="{
                                 'text-left': section.settings.align == 'left' || section.settings.split_title,
                                 'text-center': !section.settings.split_title && section.settings.align == 'center',
                                 'text-right': !section.settings.split_title && section.settings.align == 'right'
                                }"></p>
                           </template>
                       </div>
                       <div class="all-accordions section-container">
                          <div class="accordion active">
                             
                           <template x-for="(item, index) in window._.sortBy(items, 'position')" :key="index">
                              <div class="accordion-item section-item no-link bg yena-section-items" x-data="{open:false}" :class="{
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
               section: @entangle('__section'),
               items: @entangle('__items'),
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

               init(){
                  //console.log(this.items)
                  var $this = this;
                  var $eventID = 'section::' + this.section.uuid;
                  var $eventIDItem = 'sectionItem::' + this.section.uuid;
                  window.addEventListener($eventID, (event) => {
                      $this.section = event.detail;
                     $this.sectionClasses();
                  });

                  window.addEventListener($eventIDItem, (event) => {
                      $this.items = event.detail;
                  });
               }
            }
         });
     </script>
     @endscript
</div>