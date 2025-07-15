<div class="w-[100%] section-width-fill section-bg-wrapper list-box" :class="{
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
      <section class="list-content section-content wire-section" x-intersect="__section_loaded($el)" x-data="builder__courseSectionView" :class="{
         'w-boxed min-shape': section.section_settings.width == 'fit'
      }">
         <div class="list-box section-bg-wrapper" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
      
            <div class="inner-content" :class="{
               'w-boxed min-shape': section.section_settings.width == 'fit',
               'text-left': section.settings.align == 'left' || section.settings.split_title,
               'text-center': !section.settings.split_title && section.settings.align == 'center',
               'text-right': !section.settings.split_title && section.settings.align == 'right',
               'align-items-start': section.section_settings.align == 'top',
               'align-items-center': section.section_settings.align == 'center',
               'align-items-end': section.section_settings.align == 'bottom',
               'parallax': section.section_settings.parallax,
            }">

            <div class="list-section w-boxed !border-none section-component" :class="{
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
               <div class="list-header" :class="{
                  '!hidden': section.items.length == 0
               }">
                  <div class="[text-align:inherit]">
                  <template x-if="section.content.label">
                     <span class="text-[var(--foreground)] bg-[var(--c-mix-1)] px-[9px] py-[3px] mb-1 rounded-[var(--shape)] t-0" x-text="section.content.label"></span>
                  </template>
                  </div>
                  <template x-if="section.content.title">
                  <h2 class="t-4 pre-line [text-align:inherit] --text-color" x-text="section.content.title"></h2>
                  </template>
                  <template x-if="section.content.subtitle">
                     <p class="t-1 pre-line subtitle [text-align:inherit] --text-color" x-text="section.content.subtitle"></p>
                  </template>
               </div>

               <template x-if="section.items.length == 0">
                  <div>
                     <div>
                         <div class="flex flex-col justify-center items-start px-0 py-[60px]">
                           {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
                           <p class="mt-3 text-base text-left md:!text-2xl font-bold">
                              {!! __t('Your course section is empty. <br> Click here to import a course.') !!}
                           </p>
                           <a class="yena-black-btn gap-2 mt-2 cursor-pointer">{!! __i('Building, Construction', 'store') !!}{{ __('Import Course') }}</a>
                         </div>
                     </div>
                  </div>
               </template>

               <div class="section-container grid [--scroll-speed:22.5s] yena-course-wrapper" x-data x-masonry.poll.1000 :class="{
                  'md:!grid-cols-1': section.settings.desktop_grid == '1',
                  'md:!grid-cols-2': section.settings.desktop_grid == '2',
                  'md:!grid-cols-3': section.settings.desktop_grid == '3',
                  'grid-cols-1': section.settings.mobile_grid == '1',
                  'grid-cols-2': section.settings.mobile_grid == '2',

                  'grid gap-3 items-start': section.settings.display == 'grid',
                  'carousel carousel-items-container !gap-4': section.settings.display == 'carousel',
               }">
                  <template x-for="(item, index) in window._.sortBy(section.items, 'position')" :key="item.uuid">
                     <div>
                        <div class="yena-course-card yena-course-hover" x-data="{course: getCourse(item.content.course_id)}" :class="{
                           'is-slider': section.settings.display == 'carousel',
                           '!rounded-3xl': site.settings.corner == 'rounded',
                           '!rounded-none': site.settings.corner == 'straight',
                           '!rounded-xl': site.settings.corner == 'round',
                        }" :style="{
                           '--width': section.settings.desktop_width + 'px',
                           '--mobile-width': section.settings.mobile_width + 'px',
                        }">
                           <div class="yena-course-card-img">
                               <a :href="course.route">
                                   <img :src="course.featured_image" :style="{
                                    'height': section.settings.desktop_height + 'px',
                                 }" alt=" ">
                                   {{-- <div class="yena-course-badge-3 bg-white">
                                       <span>-40%</span>
                                       <span>Off</span>
                                   </div> --}}
                               </a>
                           </div>
                           <div class="yena-course-card-body">
                               <div class="yena-course-card-top">
                                   <div class="yena-course-review">
                                       <div class="rating">
                                           <i class="ph-fill ph-star"></i>
                                       </div>
                                       <span class="rating-count"> (<span x-text="course.avg_rating"></span> {{__('Rating')}})</span>
                                   </div>
                                   {{-- <div class="yena-course-bookmark-btn">
                                       <a class="yena-course-round-btn" title="Bookmark" href="#"><i class="feather-bookmark"></i></a>
                                   </div> --}}
                               </div>
      
                               <h4 class="yena-course-card-title">
                                    <a :href="course.route" x-text="course.name"></a>
                               </h4>
      
                               <ul class="yena-course-meta">
                                   <li><i class="ph ph-book"></i> <span x-text="course.lessons"></span> {{ __('Lessons') }}</li>
                                   <li><i class="ph ph-users"></i> <span x-text="course.students"></span> {{ __('Students') }}</li>
                               </ul>
      
                               <p class="yena-course-card-text" :class="{
                                 'truncate': section.settings.display == 'carousel'
                               }" x-text="limitedText(course.description, 10)"></p>
                               <div class="yena-course-author-meta mb--10">
                                   <div class="yena-course-avater">
                                       <a>
                                           <img :src="course.userAvatar" alt=" ">
                                       </a>
                                   </div>
                                   <div class="yena-course-author-info">
                                       {{ __('By') }} <span x-text="course.userName"></span>
                                   </div>
                               </div>
                               <div class="yena-course-card-bottom">
                                   <div class="yena-course-price">
                                       <span class="current-price" x-html="course.price_html"></span>
                                       {{-- <span class="off-price">$120</span> --}}
                                   </div>
                                   <a class="yena-course-btn-link" :href="course.route">{{ __('Learn More') }} <i class="ph ph-caret-right"></i>
                                    </a>
                               </div>
                           </div>
                       </div>
                     </div>
                     {{-- <div x-data="{course: getCourse(item.content.course_id)}">
                        <a class="product-card-container" x-outlink:navigate="course.route" :class="{
                           'is-slider': section.settings.display == 'carousel'
                        }" :style="{
                           'height': section.settings.desktop_height + 'px',
                           '--width': section.settings.desktop_width + 'px',
                           '--mobile-width': section.settings.mobile_width + 'px',
                           '--background': 'url('+course.featured_image+')',
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
                     </div> --}}
                  </template>
               </div>
            </div>
            </div>
         </div>
      </section>
     @script
     <script>
         Alpine.data('builder__courseSectionView', () => {
            return {
               items: {},
               ratings: [1,2,3,4,5],
               autoSaveTimer: null,
               generateSection: null,
               aiContent: null,
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