<div class="w-[100%] section-width-fill section-bg-wrapper list-box ![background:transparent]" :class="{
   'section-width-fill': section.section_settings.width == 'fill',
   'lr-padding': section.section_settings.width == 'fit',
}">

   <div x-data="builder__courseSectionView">
      <template x-if="section.items.length == 0">
         <div>
            <div>
               <div class="flex flex-col justify-center items-start px-0 py-[60px]">
                  {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
                  <p class="mt-3 text-base text-left md:!text-2xl font-bold">
                     {!! __t('Your product section is empty. <br> Click here to import a product.') !!}
                  </p>
                  <a class="yena-black-btn gap-2 mt-2 cursor-pointer">{!! __i('Building, Construction', 'store') !!}{{ __('Import Course') }}</a>
               </div>
            </div>
         </div>
      </template>
      <div class="goods-sell-group--container" :class="{
         'goods-sell-group--layout-SMALL_IMAGE': section.settings.style == 'bn-1',
         'goods-sell-group--layout-LIST_RIGHT': section.settings.style == 'bn-2',
         'goods-sell-group--layout-GRID_TWO': section.settings.style == 'bn-3',
         'goods-sell-group--layout-GRID_THREE': section.settings.style == 'bn-4',
         'goods-sell-group--layout-WATERFALL': section.settings.style == 'bn-5',
         'goods-sell-group--layout-ROUND_CARD': section.settings.style == 'bn-6',
         'goods-sell-group--layout-LIST_HORIZONTAL': section.settings.style == 'bn-7',
         'goods-sell-group--layout-LIST': section.settings.style == 'bn-8',
         'goods-sell-group--layout-BIG_IMAGE': section.settings.style == 'bn-9',
         'goods-sell-group--layout-IRREGULAR': section.settings.style == 'bn-10',
      }" :style="{
         '--list-length': section.items.length
      }">
         <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
            <div class="builder-block -booking-block-item g--block-o">
      
               <div class="-item-style" :class="{
                  '!rounded-none': site.settings.__theme == 'sand',
                  '!bg-transparent': site.background.section_color_enable == 'enable',
               }">
                     <div class="--style !p-0">
                        <a class="goods-sell--container goods-sell-group--elem goods-sell-group--item"  x-data="{course: getCourse(item.content.course_id)}" x-outlink="course.route" :class="{
                           ...getClasses,
                           'goods-sell-group--firstGrid': (index + 1) % 3 === 1 && section.settings.style == 'bn-10'
                        }" style="--goods-text-align: LEFT;">
                           <div class="card--container card--background-color" style="height: 100%; --card-background-fill-model: cover; --card-background-opacity: 100;">
                              <div class="card--content goods-sell--layout" :class="{
                                 ...getClasses,
                                 'goods-sell--layout-row': section.settings.style == 'bn-1' || section.settings.style == 'bn-2' || section.settings.style == 'bn-6',
                                 'goods-sell--layout-list': section.settings.style == 'bn-8',
                                 'goods-sell--layout-column': section.settings.style == 'bn-3' || section.settings.style == 'bn-4' || section.settings.style == 'bn-5' || section.settings.style == 'bn-7' || section.settings.style == 'bn-9' || section.settings.style == 'bn-10'
                              }">
                                 <div class="pattern-image" :class="{
                                    'goods-sell--layout-row': section.settings.style == 'bn-1' || section.settings.style == 'bn-2' || section.settings.style == 'bn-6',
                                    'goods-sell--layout-list': section.settings.style == 'bn-8',
                                    'goods-sell--layout-column': section.settings.style == 'bn-3' || section.settings.style == 'bn-4' || section.settings.style == 'bn-5' || section.settings.style == 'bn-7' || section.settings.style == 'bn-9' || section.settings.style == 'bn-10'
                                 }">
                                    <div class="goods-sell--image relative" :class="{
                                       ...getClasses,
                                       '!rounded-none': site.settings.corners == 'straight',
                                       '!rounded-xl': site.settings.corners == 'round',
                                       '!rounded-3xl': site.settings.corners == 'rounded',
                                       '!rounded-full': site.settings.corners == 'rounded' && section.settings.style == 'bn-6',
                                       'goods-sell--order2': section.settings.style == 'bn-2',
                                       'image--ratio': section.settings.style == 'bn-3' || section.settings.style == 'bn-4' || section.settings.style == 'bn-7' || section.settings.style == 'bn-8' || section.settings.style == 'bn-9' || section.settings.style == 'bn-10'
                                    }" :style="{
                                       ...imageStyle,
                                    }" style="--image-align: center;">
                                       <template x-if="item.content.sticker !=='-' && section.settings.style !== 'bn-6'">
                                          <span class="goods-sell--icon-badge" style="top-[5px] left-0" :class="{
                                             '!left-7': item.content.sticker == 'sale',
                                             '!top-4 !left-4': item.content.sticker == 'badge' || item.content.sticker == 'star',
                                          }">
                                             <x-livewire::components.bio.sections.course.partial.badges />
                                          </span>
                                       </template>
                                       <img class="image--content" :class="{
                                       '!rounded-none': site.settings.corners == 'straight',
                                       '!rounded-xl': site.settings.corners == 'round',
                                       '!rounded-3xl': site.settings.corners == 'rounded',
                                       '!rounded-full': site.settings.corners == 'rounded' && section.settings.style == 'bn-6',
                                       }" :src="course.featured_image" alt=" ">
                                    </div>
            
            
            
                                    <div class="goods-sell--footerWrap relative base-text-o" :class="{
                                       ...getClasses,
                                       '!h-[190px]': section.settings.style == 'bn-1' || section.settings.style == 'bn-2',
                                       '!h-[111px]': section.settings.style == 'bn-6',
                                    }">
                                       <template x-if="item.content.sticker !=='-' && section.settings.style == 'bn-6'">
                                          <span class="goods-sell--icon-badge top-0 right-0" :class="{
                                                '!right-7': item.content.sticker == 'sale',
                                             }">
                                             <x-livewire::components.bio.sections.course.partial.badges />
                                          </span>
                                       </template>
                                       <template x-if="item.content.enable_product_name">
                                          <div class="goods-sell--title-wrap">
                                             <div class="max-h-[34px]">
                                                <div class="text--container goods-sell--text goods-sell--title --tx-" :class="{
                                                   ...getClasses,
                                                   '--b-txt': site.background.section_color_enable !== 'enable',
                                                }" style="font-weight: 500; font-size: 14px; line-height: 20px;" x-text="course.name"></div>
                                             </div>
                                             <div class="goods-sell--sell-point --tx-" :class="{
                                                '--b-txt': site.background.section_color_enable !== 'enable'
                                             }" x-text="'(⭐' + course.avg_rating +' '+ '{{__('Rating')}}' + ')'">
                                             </div>
                                          </div>
                                       </template>
                                       <div class="goods-sell--price-wrap">
                                          <template x-if="item.content.enable_product_price">
                                             <div class="text--container goods-sell--text goods-sell--price-small-image font-semibold text-base leading-[22px] h-[22px] --tx-" :class="{
                                                '--b-txt': site.background.section_color_enable !== 'enable'
                                             }">
                                                <span class="mr-[4px]" x-html="course.price_html"></span>
                                                <span class="goods-sell--pre-price"> </span>
                                             </div>
                                          </template>
            
                                          <template x-if="item.content.style == 'download' || item.content.style == 'cart'">
                                             <div class="goods-sell--iconBtn !flex items-center justify-center">
                                                <template x-if="item.content.style == 'cart'">
                                                   {!! __i('Content Edit', 'Book', 'w-4 h-4') !!}
                                                </template>
                                                <template x-if="item.content.style == 'download'">
                                                   {!! __i('--ie', 'download-arrow', 'w-4 h-4') !!}
                                                </template>
                                             </div>
                                          </template>
                                       </div>
                                          
                                       <ul class="-yena-course-meta">
                                           <li class="--tx-" :class="{
                                             '--b-txt': site.background.section_color_enable !== 'enable'
                                          }"><i class="ph ph-book"></i> <span x-text="course.lessons"></span> {{ __('Lessons') }}</li>
                                           <template x-if="section.settings.style == 'bn-3' || section.settings.style == 'bn-7' || section.settings.style == 'bn-9' || section.settings.style == 'bn-10'">
                                             <li class="--tx-" :class="{
                                                '--b-txt': site.background.section_color_enable !== 'enable'
                                             }"><i class="ph ph-users"></i> <span x-text="course.students"></span> {{ __('Students') }}</li>
                                           </template>
                                       </ul>
                                       <template x-if="item.content.style == 'button'">
                                          <div class="goods-sell--btn-wrap !h-full">
                                             <template x-if="section.settings.style !== 'bn-6'">
                                                <div type="submit" class="goods-sell--btn flex items-center justify-center" :class="{
                                                   '!rounded-none': site.settings.corners == 'straight',
                                                   '!rounded-xl': site.settings.corners == 'round',
                                                   '!rounded-full': site.settings.corners == 'rounded',
                                                }" :style="{
                                                   'background-color': $store.builder.getContrastColor(site.settings.color),
                                                   'color': $store.builder.getContrastColor($store.builder.getContrastColor(site.settings.color))
                                                }">
                                                   <span>{{ __('Learn more') }}</span>
                                                </div>
                                             </template>
                                          </div>
                                       </template>
                                    </div>
                                 </div>
            
                                 <template x-if="section.settings.style == 'bn-6' && item.content.style == 'button'">
                                    <div class="goods-sell--btn-ROUND_CARD">
                                       <div type="submit" class="goods-sell--btn goods-sell--button flex items-center justify-center" :class="{
                                          '!rounded-none': site.settings.corners == 'straight',
                                          '!rounded-xl': site.settings.corners == 'round',
                                          '!rounded-full': site.settings.corners == 'rounded',
                                       }" :style="{
                                                   'background-color': $store.builder.getContrastColor(site.settings.color),
                                                   'color': $store.builder.getContrastColor($store.builder.getContrastColor(site.settings.color))
                                                }">
                                          <span>{{ __('Learn more') }}</span>
                                       </div>
                                    </div>
                                 </template>
                              </div>
                           </div>
                        </a>
                     </div>
                     <div class="--item--bg"></div>
               </div>
            </div>
         </template>
      </div>
   </div>
   @script
     <script>
         Alpine.data('builder__courseSectionView', () => {
            return {
               items: {},
               ratings: [1,2,3,4,5],
               autoSaveTimer: null,
               generateSection: null,
               aiContent: null,
               get listLength(){
                  let ln = 1;
                  let style = this.section.settings.style;

                  if(style == 'bn-7'){
                     ln = 2;
                  }
                  if(style == 'bn-8' || style == 'bn-10'){
                     ln = 3;
                  }

                  return {
                     '--list-length': ln
                  };
               },
               get imageStyle(){
                  let style = this.section.settings.style;
                  let width = '152px';
                  let height = '190px';
                  let ratio = '1';


                  if(style == 'bn-3' || style == 'bn-4' || style == 'bn-5' || style == 'bn-9' || style == 'bn-10'){
                     width = '100%';
                     height = '100%';
                  }
                  if(style == 'bn-6'){
                     width = '80px';
                     height = '80px';
                  }
                  if(style == 'bn-7'){
                     width = '280%';
                     height = '280%';
                  }
                  if(style == 'bn-8'){
                     width = '136px';
                     height = '136px';
                  }

                  
                  if(style == 'bn-5'){
                     ratio = '0';
                  }

                  return {
                     '--image-width': width,
                     '--image-height': height,
                     '--image-ratio': ratio,
                  };
               },
               get getClasses() {
                  return {
                     'goods-sell--SMALL_IMAGE': this.section.settings.style == 'bn-1',
                     'goods-sell--LIST_RIGHT': this.section.settings.style == 'bn-2',
                     'goods-sell--GRID_TWO': this.section.settings.style == 'bn-3',
                     'goods-sell--GRID_THREE': this.section.settings.style == 'bn-4',
                     'goods-sell--WATERFALL': this.section.settings.style == 'bn-5',
                     'goods-sell--ROUND_CARD': this.section.settings.style == 'bn-6',
                     'goods-sell--LIST_HORIZONTAL': this.section.settings.style == 'bn-7',
                     'goods-sell--LIST': this.section.settings.style == 'bn-8',
                     'goods-sell--BIG_IMAGE': this.section.settings.style == 'bn-9',
                     'goods-sell--IRREGULAR': this.section.settings.style == 'bn-10',
                  };
               },
               limitedText(text, wordLimit) {
                  if(!text) return;
                  text = text.replace(/<\/?[^>]+(>|$)/g, "");
                  const words = text.split(' ');
                  if (words.length <= wordLimit) {
                     return text;
                  }
                  let newText = words.slice(0, wordLimit).join(' ');
                  return newText + '...';
               },

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