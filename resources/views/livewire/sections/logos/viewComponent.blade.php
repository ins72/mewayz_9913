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
            'logos-box',
            'section-width-fill',
            'box',
            'section-bg-wrapper', 
            'focus'
        ]);

        $classes = $classes->toArray();
    @endphp
    <div class="{{ implode(' ', $classes) }} {{-- logos-box box focus transparent color section-height-fit section-width-fill align-items-start --}}" x-data="builder__logosSectionView" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
      <div class="text-left inner-content" :class="{
         'text-left': section.settings.align == 'left' || section.settings.split_title,
         'text-center': !section.settings.split_title && section.settings.align == 'center',
         'text-right': !section.settings.split_title && section.settings.align == 'right'
        }" style="--image-height: 50px; --image-height-mobile: 200px;">
        
        
         <div class="logos-container w-boxed" :class="{
            'show-background': section.settings.background,
            'show-border': section.settings.border,
            'left-title': section.settings.split_title,
         }" :style="{
            '--grid-height': section.settings.desktop_height + 'px',
            '--grid-height-mobile': section.settings.mobile_height + 'px',
            '--grid-width': section.settings.desktop_width + 'px',
            '--grid-width-mobile': section.settings.mobile_width + 'px',
            '--grid-count': section.settings.desktop_grid,
            '--grid-count-mobile': section.settings.mobile_grid,
            }">
            <div class="logos-header">
               <div class="[text-align:inherit]">
                <template x-if="section.content.label">
                   <span class="gallery-label t-0" x-text="section.content.label"></span>
                </template>
               </div>
               <template x-if="section.content.title">
               <h2 class="t-4 pre-line [text-align:inherit]" x-text="section.content.title"></h2>
               </template>
               <template x-if="section.content.subtitle">
                  <p class="t-1 pre-line subtitle [text-align:inherit]" x-text="section.content.subtitle"></p>
               </template>
            </div>
            
            <div class="logos-container__items" :class="{
               'carousel-items-container carousel': section.settings.display == 'carousel',
               'grid': section.settings.display == 'grid' && !section.settings.display
            }">
               <template x-for="(item, index) in window._.sortBy(items, 'position')" :key="item.uuid">
                  <div class="logos-container__item">
                     <a class="no-dark" href="javascript:void(0)" target="_self" :style="{
                        '--scale': item.content.desktop_size,
                        '--mobile-scale': item.content.mobile_size,
                     }">
                        
                        <template x-if="item.content.image">
                           <img :src="$store.builder.getMedia(item.content.image)" alt="" class="light-logo">
                        </template>
                        
                        <template x-if="!item.content.image">
                           <div class="default-image">
                              {!! __i('--ie', 'image-picture', 'text-gray-300 w-5 h-5') !!}
                           </div>
                        </template>
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
         Alpine.data('builder__logosSectionView', () => {
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
                  window.addEventListener('section::' + this.section.uuid, (event) => {
                      $this.section = event.detail;
                     $this.sectionClasses();
                  });

                  window.addEventListener('sectionItem::' + this.section.uuid, (event) => {
                      $this.items = event.detail;
                  });
               }
            }
         });
     </script>
     @endscript
</div>