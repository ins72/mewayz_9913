<div>

    @php
        $classes = collect([
            'pricing-box',
            'section-width-fill',
            'box',
            'section-bg-wrapper', 
            'focus',
            'color'
        ]);

        $classes = $classes->toArray();
    @endphp
    <div class="{{ implode(' ', $classes) }} wire-section" x-intersect="__section_loaded($el)" x-data="builder__pricingSectionView" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
      <div class="w-boxed">
           
         <div class="pricing-container-small !border-none" :class="{
            'space-below': !section.settings.background && !section.settings.border,

            'left-title': section.settings.split_title,

            'left': section.settings.align == 'left',
            'center': section.settings.align == 'center',
            'right': section.settings.align == 'right',
         }">
            <div class="pricing-header">
               <template x-if="section.content.title || section.content.subtitle">
                  <div class="pricing-heading">
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
               </template>

               <template x-if="section.settings.type == 'plans'">
                  <div class="display-options">
                     <div class="display-style">
                        <ul>
                           <li class="monthly-plan-btn" @click="price_selector='month'" :class="{
                              'active': price_selector=='month'
                           }">{{ __('Monthly') }}</li>
                           <li class="yearly-plan-btn" @click="price_selector='year'" :class="{
                              'active': price_selector=='year'
                           }">{{ __('Yearly') }}</li>
                        </ul>
                     </div>
                  </div>
               </template>
            </div>

            <div class="pricing-section mt-2" :class="{   
               'layout-1': section.settings.style == '1',
               'layout-2': section.settings.style == '2',
            }">
               <template x-for="(item, index) in window._.sortBy(items, 'position')" :key="item.uuid">
                  <div class="background not-popular-price tier yena-section-items" :data-id="item.uuid" :class="{
                     'background': section.settings.background,
                     'border': section.settings.border,
                     'not-popular-price': !item.content.popular_price,
                     'popular-price': item.content.popular_price,
                  }">
                     <div class="pricing-column">
                        <div class="price-title t-1" :class="{
                           't-1': section.settings.text == 's' || section.settings.text == 'm',
                           'small-size !font-normal': section.settings.text == 's',
                           '!font-bold': section.settings.text == 'm',
                           't-2 !font-bold': section.settings.text == 'l',
                        }">
                           <span>
                              <p x-text="item.content.title"></p>
                           </span>

                           <template x-if="item.content.popular_price">
                              <p class="t-0">{{ __('Most Popular') }}</p>
                           </template>
                        </div>
                        <template x-if="item.content.text">
                           <p class="price-description t-0" :class="{
                              't-0': section.settings.text == 's' || section.settings.text == 'm',
                              't-1': section.settings.text == 'l',
                           }" x-text="item.content.text"></p>
                        </template>
                        <div class="billing-price">

                           <template x-if="section.settings.type == 'single'">
                              <p class="amount t-4" x-html="generatePrice(item.content.single_price)"></p>
                           </template>
                           
                           <template x-if="price_selector == 'month' && section.settings.type == 'plans'">
                              <p class="amount t-4" x-html="generatePrice(item.content.month_price)"></p>
                           </template>
                           <template x-if="price_selector == 'year' && section.settings.type == 'plans'">
                              <p class="amount t-4" x-html="generatePrice(item.content.year_price)"></p>
                           </template>

                           <template x-if="section.settings.type == 'plans'">
                              <p class="period" x-text="price_selector == 'month' ? '{{ '/month' }}' : '{{ '/year' }}'"></p>
                           </template>
                        </div>
                     </div>
                     <div class="pricing-details">
                        <template x-if="item.content.features">
                           <div class="pricing-benefits">
                              <ul>
                                 <template x-for="(feature, index) in item.content.features" :key="index">
                                    <li>
                                       <i class="fi fi-rr-check flex items-center mr-[10px]" :class="{
                                          'text-[color:var(--accent)]': item.content.popular_price,
                                       }"></i>
                                       <span x-text="feature.name"></span>
                                    </li>
                                 </template>
                              </ul>
                           </div>
                        </template>
                        <template x-if="item.content.button">
                           <a x-outlink="item.content.button_link" :class="{
                              'mb-2': section.settings.style == '2',
                           }">
                              <button class="t-1 btn" :class="{
                                 'popular-price': item.content.popular_price,
                              }" x-text="item.content.button"></button>
                           </a>
                        </template>
                     </div>
                     <div class="screen"></div>
                  </div>
               </template>
            </div>
         </div>
      </div>
   </div>
     @script
     <script>
         Alpine.data('builder__pricingSectionView', () => {
            return {
               items: {},
               ratings: [1,2,3,4,5],
               autoSaveTimer: null,
               price_selector: 'month',
               currencies: {!! collect(\Currency::currency())->toJson() !!},
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
                     s.content.month_price = Math.floor(Math.random() * 500) + 1;
                     s.content.year_price = Math.floor(Math.random() * 500) + 1;
                     s.content.single_price = Math.floor(Math.random() * 500) + 1;
                     
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

                     if(s.content.features){
                           s.content.features.forEach(feature => {
                              ai.setTake('item_feature');
                              ai.run(function(e){
                                 if(e.includes('--ai-start-')){
                                       feature.name = '';
                                 }

                                 e = e.replace('--ai-start-', '');
                                 feature.name += e;
                              });
                           })
                     }
                  });
               },

               sectionClass: function(){
                  var $class = {
                     ...this.$store.builder.generateSectionClass(this.site, this.section),
                     'min-shape': false,
                     'section-bg-wrapper': false,
                  };

                  return $class;
               },
               sectionStyles: function(){
                return this.$store.builder.generateSectionStyles(this.section);
               },

               sectionClasses: function(){
                this.sectionClass();
                this.sectionStyles();
               },

               generatePrice(price){
                  let currency = this.section.settings.currency;


                  if(this.currencies[currency]){
                     currency = this.currencies[currency];
                  }
                  price = !price ? 0 : price;
                  return currency + price;
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