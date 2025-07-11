
<?php

   use App\Models\Section;
   use App\Models\SectionItem;
   use App\Models\Page;
   use function Livewire\Volt\{state, mount, placeholder};

   placeholder('
   <div class="p-5 w-full mt-1">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');


   state([
      'templates' => function(){
         $sections = [];
            foreach (collect(config('yena.pages')) as $key => $value) {
               $view = "includes.sitePages.$key";

               $section = [
                  ...$value,
                  'icon' => view()->exists($view) ? view($view)->render() : '',
               ];

               $sections[$key] = $section;
            }

         return $sections;
      }
   ]);

   state(['site']);

   // Methods

   $createPage = function($_page, $sections = null){
      $this->skipRender();
      $_c = Page::where('site_id', $this->site->id)->count();
      if(__o_feature('consume.pages', iam()) != -1 && $_c >= __o_feature('consume.pages', iam())){
         $this->js('window.runToast("error", "'. __('You have reached your page creation limit. Please upgrade your plan.') .'");');
         return;
      }
      // Create Page
      // $name = "Untitled-" . ao($template, 'name');
      // $slug = slugify(ao($_page, 'name') . str()->random(3), '-');

      $page = new Page;
      $page->fill($_page);
      $page->site_id = $this->site->id;
      $page->uuid = __a($_page, 'uuid');
      $page->save();

      if($sections){
         foreach ($sections as $section) {
            $_section = new Section;
            $_section->fill($section);
            $_section->published = 1;
            $_section->site_id = $this->site->id;
            $_section->page_id = $page->uuid;
            $_section->uuid = __a($section, 'uuid');
            $_section->save();

            if(is_array($items = __a($section, 'items'))){
               foreach ($items as $key => $value) {
                  $_item = new SectionItem;
                  $_item->fill($value);
                  $_item->section_id = $_section->uuid;
                  $_item->uuid = __a($value, 'uuid');
                  $_item->save();
               }
            }
         }
      }

      // $this->js('createPage=false');
      // $this->js('pages.push('. $page .')');
      // $this->dispatch('builder::pageCreated', $page);
   };
?>

<div>
  <div x-data="builder__new_page">

      <template x-for="(template, index) in templates" :key="index">
         <div x-show="_page==index">
            <div class="design-navbar">
               <ul >
                   <li class="close-header !flex">
                     <a @click="_page='-'">
                       <span>
                           {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                       </span>
                     </a>
                  </li>
                  <li class="!pl-0" x-text="template.name"></li>
                  <li></li>
               </ul>
            </div>
                     
            <div class="container-small w-[100%] py-0 px-[var(--s-2)] mt-[var(--s-2)]">
               <div :style="styles()">
                  <div class="page-subpanel-section"  x-data x-intersect="rescaleDiv($root)">
                     <template x-for="(item, index) in generateSectionTemplate(index)" :key="index">
                        <div class="page-type-options">
                           <div class="page-type-item">
                              <div class="container-small edit-board overflow-y-scroll !origin-[0px_0px]">
                                 <div class="card">
                                    <div class="card-body" wire:ignore>
                                       <div>
                                           <x-livewire::sections.header.viewComponent />
                                       </div>
                                       <template x-for="(_section, index) in getSections(item)" :key="index">
                                          <div class="card-body-inner">
                                             <div x-bit="'section-' + _section.section" x-data="{section:_section}"></div>
                                          </div>
                                       </template>
                                       <div>
                                           <x-livewire::sections.footer.viewComponent />
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div  x-data="{ tippyAi: {
                              content: () => $refs.tippy_ai.innerHTML,
                              allowHTML: true,
                              appendTo: $root,
                              maxWidth: 250,
                              interactive: true,
                              trigger: 'click',
                              animation: 'scale',
                              placement: 'bottom-start'
                           } }">
                              <button class="btn" @click="generatePage(template, item)">{{ __('Add Page') }}</button>
                              <template x-if="__o_feature('feature.ai_pages_sections')">
                                 <button class="btn !block" x-tooltip="tippyAi">
                                    <span class="yena-badge-g">{{ __('AI') }}</span>
                                    {{ __('Generate') }}
                                 </button>
                              </template>
                              <template x-if="!__o_feature('feature.ai_pages_sections')">
                                 <button class="btn !block" @click="$dispatch('open-modal', 'upgrade-modal')">
                                    <span class="yena-badge-g">{{ __('AI') }}</span>
                                    {{ __('Generate') }}
                                 </button>
                              </template>
                           </div>
                        </div>
                     </template>
                  </div>
               </div>
            </div>
         </div>
      </template>
   
      <div x-show="_page=='-'">
         <div class="design-navbar">
            <ul >
                <li class="close-header !flex">
                  <a @click="createPage=false">
                    <span>
                        {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                    </span>
                  </a>
               </li>
               <li class="!pl-0">{{ __('Add Page') }}</li>
               <li></li>
            </ul>
         </div>
         <div class="container-small">
          <div class="all-pages-style !mt-4">
             <ul>
                <template x-for="(item, index) in templates" :key="index">
                   <li @click="openPage(index)">
                     <div>
                        <div x-html="item.icon" class="!h-[58px] w-[90px] !flex !items-center"></div>
                        <div class="section-text">
                          <p x-text="item.name"></p>
                          <p x-text="item.description"></p>
                        </div>
                     </div>
                      <span>
                         {!! __i('Arrows, Diagrams', 'Arrow.5') !!}
                      </span>
                   </li>
                </template>
                <li @click="createBlank()">
                   <div>
                      <div class="!h-[58px] w-[90px] !flex !items-center">
                         @include('includes.sitePages.blank')
                      </div>
                      <div class="section-text">
                         <p>{{ __('Blank') }}</p>
                         <p>{{ __('Start with an empty space') }}</p>
                      </div>
                   </div>
                   <span>
                      {!! __i('Arrows, Diagrams', 'Arrow.5') !!}
                   </span>
                </li>
             </ul>
          </div>
         </div>
      </div>
            
      <template x-ref="tippy_ai">
         <div class="yena-menu-list !w-full">
            <div>
               
               <div class="flex items-start justify-between flex-col gap-2 mb-0 md:items-center md:flex-row">
                  <div class="flex items-center flex-row gap-2 max-w-full overflow-x-hidden">
                     <a class="yena-button-o !bg-white !border !border-solid !border-[var(--yena-colors-gray-200)] w-[100%]" type="button" x-tooltip="tippyCategory">
                          <span x-text="aiContent.category"></span>
                          <span class="--icon ml-2 !mr-0">
                              <i class="ph ph-caret-down"></i>
                          </span>
                     </a>
                     <a class="yena-button-o !bg-white !border !border-solid !border-[var(--yena-colors-gray-200)] w-[100%] !px-8" type="button" x-tooltip="tippyTranslate">
                          <span class="--icon">
                              <i class="ph ph-translate"></i>
                          </span>
                          <span x-text="aiContent.textLanguage" class="capitalize"></span>
                          <span class="--icon !mr-0 !ml-2">
                              <i class="ph ph-caret-down"></i>
                          </span>
                     </a>
                  </div>
              </div>
              <div class="flex items-center flex-col gap-4 py-4">
                  <div class="yena-form-group" x-data="{scrollHeight:5}">
                      <textarea type="text" :style="{
                          'height': scrollHeight + 'px'
                      }" @input="scrollHeight-0;scrollHeight=$event.target.scrollHeight" x-model="aiContent.textPrompt" placeholder="{{ __('Tell us more... (e.g., We offer digital marketing services for small businesses)') }}" class="!px-[1rem] !rounded-lg !shadow-lg md:!text-[var(--yena-fontSizes-lg)] focus:!shadow-lg bg-white w-[100%] resize-none min-h-[60px] max-h-[300px]"></textarea>
                  </div>
                  
              </div>
              <a class="btn btn-medium neutral !h-[calc(var(--unit)*_4)] !w-[100%]" type="button" @click="generatePage(template, item, true)"><i class="ph ph-star-four"></i> {{ __('Generate') }}</a>
            </div>
         </div>
      </template>
  </div>
  @script
      <script>
          Alpine.data('builder__new_page', () => {
             return {
                  templates: @entangle('templates'),
                  siteTheme: 'light',
                  // tippyAi: {
                  //    allowHTML: true,
                  //    maxWidth: 360,
                  //    interactive: true,
                  //    trigger: 'click',
                  //    animation: 'scale',
                  // },
                  aiContent: {
                        category: 'Art',
                        textPrompt: '',
                        textContent: 'generate',
                        textAmount: 'brief',
                        textTone: 'casual',
                        textLanguage: 'english',
                        generateImages: 'none',
                        imageQuery: null,
                  },
                  tippy: {
                        allowHTML: true,
                        maxWidth: 360,
                        interactive: true,
                        trigger: 'click',
                        animation: 'scale',
                    },
                  tippyTone: {},
                  tippyCategory: {},
                  tippyTranslate: {},

                  sectionsTemplate: {
                     landing: {!! collect(getSectionPreset('landing'))->toJson() !!},
                     contact: {!! collect(getSectionPreset('contact'))->toJson() !!},
                     links: {!! collect(getSectionPreset('links'))->toJson() !!},
                     pricing: {!! collect(getSectionPreset('pricing'))->toJson() !!},
                     portfolio: {!! collect(getSectionPreset('portfolio'))->toJson() !!},
                     services: {!! collect(getSectionPreset('services'))->toJson() !!},
                     about: {!! collect(getSectionPreset('about'))->toJson() !!},
                     blank: {!! collect(config('sections.blank'))->toJson() !!},
                  },
                  _page: '-',
                  // __section: null,
                  getSections(item){
                     let data = this.$store.builder.generatePageSections(item.sections);
                     return data;
                  },

                  createBlank(){
                     var $this = this;
                     var $siteUUID = $this.$store.builder.generateUUID();
                     var $page = {
                        uuid: $siteUUID,
                        name: '{{ __('Blank') }}',
                        published: 1,
                        default: 0,
                        site_id: this.site.id,
                        slug: 'blank-'+(Math.random() + 1).toString(36).substring(7),
                     };

                     let section = {
                        ...this.sectionsTemplate.blank,
                        uuid: $this.$store.builder.generateUUID(),
                        page_id: $siteUUID,
                        position: 0,
                        section_settings: {
                           height: 'fit',
                           width: 'fill',
                           spacing: 'l',
                           align: 'center',
                           ...this.sectionsTemplate.blank.section_settings
                        }
                     };

                     $this.sections.push(section);
                     $this.pages.push($page);

                     // console.log($page, section)
                     this.$wire.createPage($page, [section]);
                     this._page='-';
                     this.createPage=false;
                  },

                  generatePage(template, section, ai = false){
                     let $this = this;

                     if($this.__o_feature('consume.pages') != -1 && $this.pages.length >= $this.__o_feature('consume.pages')){
                        window.runToast("error", "{{ __('You have reached your page creation limit. Please upgrade your plan.') }}");
                        return;
                     }

                     var $siteUUID = $this.$store.builder.generateUUID();
                     var $page = {
                        uuid: $siteUUID,
                        name: template.name,
                        published: 1,
                        default: 0,
                        site_id: this.site.id,
                        slug: template.name+'-'+(Math.random() + 1).toString(36).substring(7),
                     };

                     section = section.sections;

                     section.forEach((item, i) => {
                        let section_settings = item.section_settings;
                        item.uuid = $this.$store.builder.generateUUID();
                        item.page_id = $siteUUID;
                        item.position = i;
                        item.section_settings = {
                           height: 'fit',
                           width: 'fill',
                           spacing: 'l',
                           align: 'center',
                           ...section_settings
                        };
                        
                        if(item.items){
                           item.items.forEach((__, index) => {
                              __.position = index;
                              __.uuid = $this.$store.builder.generateUUID();
                              __.section_id = item.uuid;
                           });
                        }
                        $this.sections.push(item);
                     });
                     $this.pages.push($page);
                     $this.setPage($page.uuid);
                     $this.getCurrentPage();

                     // console.log($page, section)
                     this.$wire.createPage($page, section).then(r => {
                        if(ai && $this.__o_feature('feature.ai_pages_sections')){
                           var array = ["unsplash", "pexels"];
                           var chosenSource = array[Math.floor(Math.random() * array.length)];

                           let $prompt = {
                              ...$this.aiContent,
                              generateImages: chosenSource
                           };
                           // let $prompt = $this.site.ai_generate_prompt;
                           $this.generateAiPage($prompt);
                        }
                     });
                     this._page='-';
                     this.createPage=false
                  },
                  

                  generateSectionTemplate(item){
                     let $template = this.templates[item];
                     let $data = this.sectionsTemplate[item];

                     return $data;
                  },
                  styles(){
                      var site = this.site;
                      return this.$store.builder.generateSiteDesign(site);
                  },

                  openPage(page){
                     // this.resizeDiv();
                     // this.getSections = function(){
                     //    return this.$store.builder.generatePageSections(this.generateSectionTemplate(page).sections);
                     // };

                     this._page = page;
                  },

                  closePage(){
                     this._page = '-';
                  },

                  getDeviceWidth() {
                     let $this = this;
                     var he = $this.$refs.scale__section;
                     de = he == null ? void 0 : he.querySelector(".page-type-item");
                     let me = de == null ? void 0 : de.clientWidth;

                     let scale = me / window.innerWidth;

                     return scale;
                  },
                  updateAllScale(scale){
                     var he = this.$refs.scale__section;
                     he.querySelectorAll('.edit-board').forEach((el) => {
                        el.style.transform = 'scale(' + scale + ')';
                     })
                  },
                  resizeDiv(){
                     let $this = this;
                     var he = $this.$refs.scale__section;
                     
                     setTimeout(() => {
                        $this.updateAllScale(this.getDeviceWidth());
                     }, 500);
                     
                     window.addEventListener('resize', function(){
                        let scale = $this.getDeviceWidth();
                        $this.updateAllScale(scale);
                     });
                  },




                  

                  rescaleDiv(scale__section){
                     let $this = this;
                     var he = scale__section;

                     var objects = {
                        getDeviceWidth() {
                           let $this = this;
                           let de = he == null ? void 0 : he.querySelector(".page-type-item");
                           let me = de == null ? void 0 : de.clientWidth;

                           let scale = me / window.innerWidth;

                           return scale;
                        },
                        updateAllScale(scale){
                           he.querySelectorAll('.edit-board').forEach((el) => {
                              el.style.transform = 'scale(' + scale + ')';
                           })
                        },
                     };
                     
                     setTimeout(() => {
                        objects.updateAllScale(objects.getDeviceWidth());
                     }, 500);
                     
                     window.addEventListener('resize', function(){
                        let scale = objects.getDeviceWidth();
                        objects.updateAllScale(scale);
                     });
                  },

                  init(){
                     let $this = this;
                     // this.tippyAi.appendTo = this.$root;
                     // this.tippyAi.content = this.$refs.tippy_ai.innerHTML;
                     this.tippy.appendTo = this.$root;

                        this.tippyTone = {
                            ...this.tippy,
                            content: this.$refs.tone_template.innerHTML,
                        }
                        this.tippyTranslate = {
                            ...this.tippy,
                            content: this.$refs.translate_template.innerHTML,
                        }
                        this.tippyCategory = {
                            ...this.tippy,
                            placement: 'bottom',
                            content: this.$refs.category_template.innerHTML,
                        }
                  }
             }
          });
      </script>
  @endscript
</div>