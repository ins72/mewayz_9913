<div class="w-[100%] section-width-fill section-bg-wrapper list-box ![background:transparent]" :class="{
   'section-width-fill': section.section_settings.width == 'fill',
   'lr-padding': section.section_settings.width == 'fit',
}">

    @php
        $classes = collect([
            'list-box',
            'section-width-fill',
            'box',
            'section-bg-wrapper', 
            'focus'
        ]);

        $classes = $classes->toArray();
    @endphp
      <section class="list-content section-content wire-section ![background:transparent]" x-data="builder__productSectionView" :class="{
         'w-boxed min-shape': section.section_settings.width == 'fit'
      }">
         <div class="list-box section-bg-wrapper ![background:transparent]" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
      
            <div class="inner-content remove-before" :class="{
               'w-boxed min-shape': section.section_settings.width == 'fit',
               'text-left': section.settings.align == 'left' || section.settings.split_title,
               'text-center': !section.settings.split_title && section.settings.align == 'center',
               'text-right': !section.settings.split_title && section.settings.align == 'right',
               'align-items-start': section.section_settings.align == 'top',
               'align-items-center': section.section_settings.align == 'center',
               'align-items-end': section.section_settings.align == 'bottom',
               'parallax': section.section_settings.parallax,
            }">

            <div class="list-section w-boxed !border-none" :class="{
               'background': section.settings.background,
               'no-background': section.settings.border,
               'space-below': !section.settings.background && !section.settings.border,

               'left-title': section.settings.split_title,
               'layout-1': section.settings.style == '1',
               'layout-2': section.settings.style == '2',

               'left': section.settings.align == 'left',
               'center': section.settings.align == 'center',
               'right': section.settings.align == 'right',
            }">
               <template x-if="section.items.length == 0">
                  <div>
                     <div>
                         <div class="flex flex-col justify-center items-start px-0 py-[60px]">
                           {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
                           <p class="mt-3 text-base text-left md:!text-2xl font-bold">
                              {!! __t('Your product section is empty. <br> Click here to import a product.') !!}
                           </p>
                           <a class="yena-black-btn gap-2 mt-2 cursor-pointer">{!! __i('Building, Construction', 'store') !!}{{ __('Import Product') }}</a>
                         </div>
                     </div>
                  </div>
               </template>

               <div class="section-container grid [--scroll-speed:22.5s]" :class="{
                  'md:!grid-cols-1': section.settings.desktop_grid == '1',
                  'md:!grid-cols-2': section.settings.desktop_grid == '2',
                  'md:!grid-cols-3': section.settings.desktop_grid == '3',
                  'grid-cols-1': section.settings.mobile_grid == '1',
                  'grid-cols-2': section.settings.mobile_grid == '2',

                  'grid gap-3': section.settings.display == 'grid',
                  'carousel carousel-items-container gap-3': section.settings.display == 'carousel'
               }">
                  <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
                     <div x-data="{product: getProduct(item.content.product_id)}">
                        <a class="product-card-container !border-0" x-outlink="product.route" :class="{
                           'is-slider': section.settings.display == 'carousel',
                           '!rounded-3xl': site.settings.corners == 'rounded',
                           '!rounded-none': site.settings.corners == 'straight',
                           '!rounded-xl': site.settings.corners == 'round',
                        }" :style="{
                           'height': section.settings.desktop_height + 'px',
                           '--width': section.settings.desktop_width + 'px',
                           '--mobile-width': section.settings.mobile_width + 'px',
                           '--background': 'url('+product.featured_image+')',
                        }">
                           <div class="product-card-overlay">
                              <div class="product-card-wrapper">
                                 <div class="subheading-small product-card-tag capitalize" :class="{
                                    '!hidden': !product.tag,
                                    '!bg-[#dc2626] !text-white': product.tag == 'hot',
                                    '!bg-[#16a34a] !text-white': product.tag == 'new',
                                 }" x-text="product.tag"></div>

                                 <div class="paragraph-small product-card-ratings" x-text="'⭐' + product.avg_rating"></div>
                              </div>
                              <div class="product-card-wrapper">
                                 <div class="product-card-heading">
                                    <div class="paragraph-small text-white">{{ __('Product') }}</div>
                                    <div class="subheading-medium product-card-title" x-text="product.name" :class="{
                                       't-1': section.settings.text == 's' || section.settings.text == 'm',
                                       'small-size': section.settings.text == 's',
                                       '!font-bold': section.settings.text == 'm',
                                       '!text-[calc(1rem*1.25)] !font-bold': section.settings.text == 'l',
                                    }"></div>

                                    <div class="flex items-center gap-2 overflow-hidden">
                                       <div class="product-card-color"></div>
                                       
                                       <template x-for="(variant, index) in product.variants">
                                          <div class="product-card-color" :style="{
                                             'background-color': variant.variation_value
                                          }"></div>
                                       </template>
                                    </div>
                                 </div>
                                 <div class="paragraph-medium text-white" x-html="product.price_html"></div>
                              </div>
                           </div>
                           <div class="product-card-button">
                              <i class="ph ph-eye text-base"></i>
                           </div>
                        </a>                        
                     </div>
                  </template>
               </div>
            </div>
            </div>
         </div>
      </section>
     @script
     <script>
         Alpine.data('builder__productSectionView', () => {
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
                     let section = ['item_title', 'item_text'];
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
                           s.content.icon_type = 'image';
                           s.content.image = e;
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
                  // var $eventID = 'section::' + this.section.uuid;


                  // $this.$dispatch($eventID, $this.section);
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