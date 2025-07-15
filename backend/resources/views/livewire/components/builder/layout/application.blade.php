
<?php

   use App\Models\SiteSocial;
   use App\Models\Page;
   use App\Models\ProductOrder;
   use App\Models\ProductReview;
   use App\Models\Product;
   use App\Models\CoursesEnrollment;
   use App\Models\CoursesLesson;
   use App\Models\CoursesReview;
   use App\Models\Course;
   use App\Models\SiteHeaderLink;
   use App\Models\Section;
   use App\Models\SectionItem;
   use App\Models\SitesStaticThumbnail;
   use App\Livewire\Actions\ToastUp;
   use function Livewire\Volt\{state, mount, on, updated, uses};

   uses([ToastUp::class]);

   state([
      'site',
      'sections' => [],
      'section' => [],
      'pages' => [],
      'posts' => [],
      'products' => [],
      'courses' => [],

      'siteArray' => [],
      'headerLinks' => [],
      'footerGroups' => [],

      'planFeatures' => fn() => $this->site->user()->first()->planJsFeatures(),
   ]);
   state([
      'aiTone' => function(){
         $tones = [];

         foreach (config('yena.aiTone') as $key => $value) {
               $tone = [
                  ...$value,
                  'icon' => __dashed_svg(ao($value, 'icon'), 'w-4 h-4')
               ];

               $tones[] = $tone;
         }

         return $tones;
      },
      'aiLanguage' => fn() => config('yena.aiLanguage'),
   ]);

   state([
      'generatePrompt' => fn () => collect(config('yena.generatePrompt')),
   ]);
   mount(function(){

      // dd($this->site->canA());
      $this->getSections();
      $this->getPages();
      $this->getPosts();

      $this->getHeaderLinks();
      $this->getFooterGroups();

      $this->getProducts();
      $this->getCourses();

      $this->siteArray = $this->site->toArray();

      // dd(iam()->planJsFeatures());
   });

   on([
      'builder::refreshSections' => function($section){
         $this->getSections();
      },

      'builder::processSectionToPages' => function($data){
         $sections = [];
         foreach ($data as $key => $value) {
            $items = [];

            if(is_array($dataItems = ao($value, 'items'))){
               foreach ($dataItems as $k => $v) {
                  $items[] = [
                     'content' => ao($v, 'content'),
                     'settings' => ao($v, 'settings'),
                  ];
               }
            }

            $section = [
               // 'image' => ao($value, 'image'),
               'section' => ao($value, 'section'),
               'content' => ao($value, 'content'),
               'settings' => ao($value, 'settings'),
               'section_settings' => ao($value, 'section_settings'),
               'form' => ao($value, 'form'),
               'items' => $items,
            ];

            $sections[] = $section;
         }

         // config(['sections.rand' => $sections]);
         // $fp = fopen(base_path() .'/config/sections/rand.php' , 'w');
         // fwrite($fp, '<?php return ' . var_export(config('sections'), true) . ';');
         // fclose($fp);

         dd(json_encode($sections));
      },

      'section::create' => function($section){
         $this->skipRender();

         // dd($this->site->getEditingPage(), $this->site->pages()->get());

         $_section = new Section;
         $_section->fill($section);
         $_section->site_id = $this->site->id;
         $_section->page_id = $this->site->getEditingPage();
         $_section->published = 1;
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

         $this->js('$store.builder.savingState = 2');
      },

      'section::delete' => function($id){
         $this->skipRender();

         $this->deleteSection($id);
      },

      'section::deleteItem' => function ($item) {
         if(!$_item = SectionItem::where('uuid', $item)) return;

         $_item->delete();

         // Dispatch another event with status
      },

      'section::create_section_item' => function($item, $section_id){

         // Check if i own this section;
         $_item = new SectionItem;
         $_item->fill($item);
         $_item->section_id = $section_id;
         $_item->save();

         // Dispatch another event with status
         $this->dispatch('section::created_section', $_item);
      },

      'builder::saveHeaderLinks' => function($links, $js = null){

         foreach ($links as $key => $value) {
            if(!$_item = SiteHeaderLink::where('uuid', __a($value, 'uuid'))->first()) continue;
            $_item->uuid = __a($value, 'uuid');
            $_item->fill($value);
            $_item->save();


            // Children

            if(is_array($children = __a($value, 'children'))){
               foreach ($children as $v) {
                  if(!$_i = SiteHeaderLink::where('uuid', __a($v, 'uuid'))->first()) continue;
                  $_i->uuid = __a($v, 'uuid');
                  $_i->fill($v);
                  $_i->save();
               }
            }
         }

         if(!$js){
            $this->js('$store.builder.savingState = 2');
         }else{
            $this->js($js);
         }
      },



      'builder::saveSection' => function ($section, $js = null){
         if(!$_section = $this->site->sections()->where('uuid', __a($section, 'uuid'))->first()) return;
         $_section->fill($section);

         $_section->save();

         if(!$js){
            $this->js('$store.builder.savingState = 2');
         }else{
            $this->js($js);
         }
      },

      'builder::save_sections_and_items' => function ($section, $js = null){
         if(!$_section = $this->site->sections()->where('uuid', __a($section, 'uuid'))->first()) return;
         $_section->fill($section);

         $_section->save();
         
         if(is_array($items = __a($section, 'items'))){
            foreach ($items as $key => $value) {
               if(!$_item = SectionItem::where('uuid', __a($value, 'uuid'))->first()) continue;
               $_item->fill($value);

               $_item->uuid = __a($value, 'uuid');
               $_item->save();
            }
         }

         // $this->js('console.log('. json_encode($section) .')');
         if(!$js){
            $this->js('$store.builder.savingState = 2');
         }else{
            $this->js($js);
         }
      },

      // Create Section

      // Site
      'builder::saveSite' => function(){
         // $this->skipRender();
         // Update database site with javascript site data.
         $this->site->fill($this->siteArray);
         $this->site->save();


         if(is_array($socials = __a($this->siteArray, 'socials'))){
            foreach ($socials as $key => $value) {
               if(!$_social = SiteSocial::where('uuid', __a($value, 'uuid'))->first()) continue;
               $_social->fill($value);
               $_social->site_id = $this->site->id;
               $_social->save();
            }
         }

         $this->js('$store.builder.savingState = 2');
      },

      // Page
      'builder::savePage' => function($page, $js = null){
         if(!$_page = $this->site->pages()->where('uuid', __a($page, 'uuid'))->first()) return;
         // Update database site with javascript site data.

         $_page->fill($page);
         $_page->save();

         if(!$js){
            $this->js('$store.builder.savingState = 2');
         }else{
            $this->js($js);
         }
      },
      'builder::sort_sections' => function($sections){
         $this->skipRender();

         
         foreach ($sections as $key => $value) {
            if(!$_section = $this->site->sections()->where('uuid', __a($value, 'uuid'))->first()) continue;
            
            $_section->position = __a($value, 'position');
            $_section->save();
         }
      },
   ]);

   updated([
      'site.name' => function(){

         dd('sdfsd');
         $this->site->save();
      }
   ]);

   $deleteSection = function($id){
      $this->skipRender();

      if(!$section = Section::where('uuid', $id)->where('site_id', $this->site->id)->first()) return;

      SectionItem::where('section_id', $section->uuid)->delete();

      $section->delete();

      // $this->_f('success', __('Section deleted successfully'));
   };

   // Methods
   $getSections = function(){
      $this->sections = $this->site->sections()->get()->map(function($item){
      $config = $item->getConfig();


      $item->get_media = $item->getMedia();
      $item->editComponent = ao($config, 'components.editComponent');
      //$page_id = "sectionComponent:$item->id";

      return $item;
    })->toArray();
   };

   $getPages = function(){
      $this->pages = $this->site->pages()->orderBy('id', 'asc')->get()->toArray();
   };

   $getPosts = function(){
      $this->posts = $this->site->posts()->orderBy('id', 'asc')->get()->toArray();
   };

   $getHeaderLinks = function(){
      $this->headerLinks = $this->site->header_links()->where('parent_id', '=', null)->get()->toArray();
   };

   $getFooterGroups = function(){
      $this->footerGroups = $this->site->footer_groups()->get()->toArray();
   };

   $getProducts = function(){
      $this->products = Product::where('user_id', iam()->id)->get()->map(function($item) {
         $avgRating = ProductReview::where('product_id', $item->id)->avg('rating');

         // $item->route = route('out-products-single-page', ['slug' => $item->slug]);

         $item->variants = $item->variant()->where('type', 'color')->get()->toArray();
         $item->route = null;
         $item->avg_rating = number_format($avgRating, 1);
         $item->featured_image = $item->getFeaturedImage();
         $item->price_html = $item->getPrice();

         // Add tag "new" if the product was recently created
         if ($item->created_at >= now()->subDays(5)) {
            $item->tag = 'new';
         }

         // Add tag "hot" if the product has the highest rating
         $highestRatedProduct = Product::withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating', 'DESC')
            ->first();

         if ($highestRatedProduct && $item->id == $highestRatedProduct->id) {
            $item->tag = 'hot';
         }


         return $item;
      })->toArray();
   };

   $getCourses = function(){
      $this->courses = Course::where('user_id', iam()->id)->get()->map(function($item) {
         $avgRating = CoursesReview::where('course_id', $item->id)->avg('rating');

         // $item->route = route('out-products-single-page', ['slug' => $item->slug]);

         // $item->variants = $item->variant()->where('type', 'color')->get()->toArray();
         $item->route = null;
         $item->avg_rating = number_format($avgRating, 1);
         $item->featured_image = $item->_get_featured_image();
         $item->price_html = $item->getPrice();

         $item->students = CoursesEnrollment::where('course_id', $item->id)->count();
         $item->lessons = CoursesLesson::where('course_id', $item->id)->count();

         $user = $item->user()->first();
         $item->userName = $user->name;
         $item->userAvatar = $user->getAvatar();


         return $item;
      })->toArray();
   };
   
   $saveStaticImage = function($image){
      // Delete previous images
      SitesStaticThumbnail::where('site_id', $this->site->id)->delete();


      $new = new SitesStaticThumbnail;
      $new->site_id = $this->site->id;
      $new->thumbnail = $image;
      $new->save();
   };
?>
<div>
   <div class="editor-panel" x-data="yenaBuilder" :class="{'maximize': renderView == 'max'}">
      
   <template x-if="exportingSite">
      <div class="loader-card">
         <div class="preloader mb-4">
            <div class="loader-animation-container">
                 <div class="inner-circles-loader"></div>
            </div>
         </div>
         <p class="loader-text fade text-center pre-line">{{ __("Exporting site...") }}</p>
     </div>
   </template>
   <style>
      :root {
         --accent: #004b63;
         --shape: var(--r-full);
         --min-shape: var(--min-r-full);
         --site-width: 800px;
         --logo-height: 50px;
         --logo-height-mobile: 20px;
         --sublinks-shape: calc(var(--min-shape) / 2);
         --design-headFont: 'Arima Madurai Black';
         --design-headWeight: 100;
         --design-bodyFont: 'Arima Madurai regular';
      }
   </style>
   

   <livewire:components.builder.layout.toolbar :$site lazy :key="uukey('builder', 'toolbar')" />
   <livewire:components.builder.parts.openai :key="uukey('builder', 'openai-backend')" />
   <livewire:components.builder.parts.ai :key="uukey('builder', 'ai-text-backend')" />
   
   <div class="container !max-w-[initial]">
      <div class="projects">
         @if ($site->canEdit())
            <livewire:components.builder.layout.sidebar :$site lazy :key="uukey('builder', 'sidebar')" />
         @endif
         <div class="container-small !max-w-[initial] edit-board {{ !$site->canEdit() ? '!w-full' : '' }}">
            <div class="edit-blocks !zzflex-row">
               <div class="edit-blocks-container flex-1">
                  {{-- <div class="p-5 !hidden">
                     <div class="generate-card">
                        <div class="absolute z-0 pointer-events-none top-0 w-[100%] h-full [background:var(--bg-img)_center_center_repeat] animate-[180s_linear_0s_infinite_normal_none_running_animation-1w9onv1] [mask-image:linear-gradient(to_left,_rgba(0,_0,_0,_0.75),_transparent,_rgba(0,_0,_0,_0.75))] [mask-repeat:repeat] [mask-size:140px]" style="--bg-img:url({{ gs('assets/image/others/Stars-2.svg') }});"></div>
                        <div class="--background"></div>
   
                        <div class="-body">
                           <div class="flex flex-col gap-4 relative z-[1]">
                              <div class="flex items-center flex-row gap-2">
                                 <p class="font-bold text-2xl text-[var(--yena-colors-gray-800)]">Generate card</p>

                                 <div class="flex-1 place-self-stretch"></div>
                                 
                                 <div>
                                    <button class="yena-button-o !bg-[var(--yena-colors-gradient-light)] !border !border-solid !border-[var(--yena-colors-gray-200)] w-[100%] ![box-shadow:var(--yena-shadows-sm)]" type="button">
                                       <span class="--icon">
                                           <i class="ph ph-translate"></i>
                                       </span>
                                       <span class="capitalize">{{ __('English') }}</span>
                                       <span class="--icon ml-auto !mr-0">
                                           <i class="ph ph-caret-down"></i>
                                       </span>
                                   </button>
                                 </div>
                              </div>
                              <div class="yena-form-group">
                                    <input type="text" class="!px-[1rem] !rounded-lg !shadow-lg !h-[var(--yena-sizes-14)] md:!text-[var(--yena-fontSizes-lg)] md:!h-[var(--yena-sizes-16)] !bg-white" placeholder="Describe what you'd like to make">
                              </div>

                              <p class="text-[var(--body-color-muted)] text-sm font-normal">Choose a template</p>
                              <div class="template-group">
                                 <button type="button" class="--item" aria-label="Bullets">
                                    <div class="yena-stack css-8unr7z" aria-hidden="true" focusable="false">
                                       <img src="/_next/static/media/Title-with-Bullets.066ad97e.svg" class="yena-image css-u8nm50">
                                    </div>
                                 </button>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div> --}}
                  @persist('builder')
                     <div wire:ignore x-cloak>
                        <div x-show="previewPage == 'post'">
                           <div x-data="{post:generatePost}">
                              <livewire:components.builder.generatePost :$site zzlazy :key="uukey('builder', 'generatePost')" />
                           </div>
                        </div>
                        <div x-show="previewPage == '-'">
                           <livewire:components.builder.index :$site lazy :key="uukey('builder', 'index')" />
                        </div>
                     </div>
                  @endpersist
               </div>
               {{-- <livewire:components.builder.parts.chat :$site :key="uukey('builder', 'ai-chat')" /> --}}
               @if ($site->canEdit())
                  <livewire:components.builder.parts.aiEdit :$site :key="uukey('builder', 'ai-edit')" />
               @endif
            </div>
         </div>
      </div>
   </div>
    
   @foreach(config("yena.sections") as $key => $item)
       @php
           if(!$_name = __a($item, 'components.alpineView')) continue;
           $_name = str_replace('/', '.', $_name);

           $component = "livewire::$_name";
       @endphp
       <template bit-component="section-{{ $key }}">
           <div wire:ignore>
           <x-dynamic-component :component="$component"/>
           </div>
       </template>
   @endforeach
   
   @if ($site->canEdit())
   <div class="ai-chat-button" :class="{
      '!hidden':generateAiId
   }" x-cloak>
      <a @click="generateAiId=true;" class="yena-a-button flex">
          <span>
            <i class="ph ph-sparkle text-lg"></i>
          </span>
          <div class="-txt">
              <span>
                  {{ __('Edit with Ai') }}
              </span>
          </div>
      </a>
  </div>
  @endif

   <div class="nav-bottom">
      <div class="nav-list">
         <ul>
            <li>
               <a @click="navigatePage('pages')">
                  <span>
                     {!! __i('interface-essential', 'item-pen-text-square') !!}
                  </span>
                  {{ __('pages') }}
               </a>
            </li>
            <li class="!hidden">
               <a @click="navigatePage('section')">
                  <span>
                     {!! __i('interface-essential', 'item-pen-text-square') !!}
                  </span>
                  {{ __('Section') }}
               </a>
            </li>
            <li>
               <a @click="navigatePage('design')">
                  <span>
                     {!! __icon('Design Tools', 'Bucket, Paint') !!}
                  </span>
                  {{ __('design') }} 
               </a>
            </li>
            <li>
               <a @click="navigatePage('contact')">
                  <span>
                     {!! __i('Support, Help, Question', 'checklist-user') !!}
                  </span>
                  {{ __('Contacts') }}
               </a>
            </li>
            <li>
               <a @click="navigatePage('analytics')">
                  <span>
                     {!! __icon('Business, Products', 'blackboard-business-chart') !!}
                  </span>
                  {{ __('Analytics') }}
               </a>
            </li>
            <li>
               <a @click="navigatePage('media')">
                  <span>
                     {!! __i('Music, Audio', 'media-library-playlist-play') !!}
                  </span>
                  {{ __('Media') }}
               </a>
            </li>
            <li>
               <a @click="navigatePage('settings')">
                  <span>
                     {!! __i('interface-essential', 'setting4') !!}
                  </span>
                  {{ __('Settings') }}
               </a>
            </li>
         </ul>
      </div>
   </div>

   <template x-ref="category_template">
     <div class="yena-menu-list !w-full">
       <template x-for="(item, index) in generatePrompt" :key="index">
           <a @click="aiContent.category = item" :class="{
               '!bg-[var(--yena-colors-gray-100)] !border-[var(--yena-colors-gray-200)]': aiContent.category == item,
           }" class="yena-menu-list-item">
              <span class="text-sm" x-text="item"></span>
           </a>
       </template>
    </div>
   </template>
  
   <template x-ref="tone_template">
      <div class="yena-menu-list !w-full">
          <template x-for="(item, index) in aiTones" :key="index">
              <a @click="aiContent.textTone = item.prompt" :class="{
                  '!bg-[var(--yena-colors-gray-100)] !border-[var(--yena-colors-gray-200)]': aiContent.textTone == item.prompt,
              }" class="yena-menu-list-item">
                 <div class="--icon" x-html="item.icon"></div>
                 <span class="text-sm" x-text="item.name"></span>
              </a>
          </template>
     </div>
  </template>

  <template x-ref="translate_template">
    <div class="yena-menu-list !w-full">
      <template x-for="(item, index) in aiLanguages" :key="index">
          <a @click="aiContent.textLanguage = item.prompt" :class="{
              '!bg-[var(--yena-colors-gray-100)] !border-[var(--yena-colors-gray-200)]': aiContent.textLanguage == item.prompt,
          }" class="yena-menu-list-item">
             <span class="text-sm" x-text="item.name"></span>
          </a>
      </template>
   </div>
  </template>
   <template x-teleport="body">
      <x-modal name="upgrade-modal" :show="false" removeoverflow="true" maxWidth="max-w-[var(--yena-sizes-5xl)]" focusable>
         <div>
                  
            <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-1 right-1 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
               <i class="fi fi-rr-cross-small"></i>
            </a>
            <livewire:components.upgrade.page lazy :key="uukey('builder', 'upgrade-modal')"/>
         </div>
      </x-modal>
   </template>
   
   <template x-teleport="body">
      <x-modal name="share-modal" :show="false" removeoverflow="true" maxWidth="2xl" focusable>

         <livewire:components.builder.parts.share :key="uukey('builder', 'share-modal')">
      </x-modal>
   </template>
   @script
        <script>
            Alpine.data('yenaBuilder', () => {
               return {
                  renderView: 'normal',
                  renderMobile: false,
                  sidebarClass: '',
                  openChat: true,
                  generateAiId: false,
                  deleteSectionId: false,
                  generatePost: null,
                  page: 'pages',
                  previewPage: '-',
                  __last_page: null,

                  site: @entangle('siteArray'),
                  posts: @entangle('posts'),
                  pages: @entangle('pages'),
                  sections: {!! json_encode($sections) !!},
                  siteheader:{
                     links: @entangle('headerLinks')
                  },
                  footerGroups: @entangle('footerGroups'),
                  planFeatures: @entangle('planFeatures'),
                  products: @entangle('products'),
                  courses: @entangle('courses'),
                  currentPage: null,
                  socials: {!! collect(socials())->toJson() !!},
                  autoSaveTimer: null,
                  _create_position: null,


                  section: [],
                  __section_create_page: null,
                  aiTones: @entangle('aiTone'),
                  aiLanguages: @entangle('aiLanguage'),
                  generatePrompt: @entangle('generatePrompt'),
                  exportingSite: false,
                  getMedia(media){
                     if(!media) return;
                     var _url = "{{gs('media/site/images')}}";

                     return _url + '/' + media;
                  },
                  __o_feature(code){
                     let feature = false;
                     this.planFeatures.forEach((e) => {
                        if(e.code == code){
                           feature = e.type == 'limit' ? e.limit : e.enable;
                        }
                     });

                     return feature;
                  },
                  deleteItem(item_id){

                     this.$dispatch('section::deleteItem', {
                        item: item_id
                     });
                  },
                  isVisible(domElement) {
                     return new Promise(resolve => {
                        const o = new IntersectionObserver(([entry]) => {
                           resolve(entry.intersectionRatio === 1);
                           o.disconnect();
                        });
                        o.observe(domElement);
                     });
                  },

                  openSection(_id){
                     let $this = this;
                     var section = $this.sections.filter(item => item.uuid == _id)[0];

                     var call = function(){
                        return new Promise(resolve => {
                           $this.section = section;
                           $this.navigatePage('section::' + section.section);
                              
                           resolve($this.section);
                        });
                     }

                     return call();
                  },
                  editSection(section){
                     this.openSection(section.uuid).then(function(){
                        var event = new CustomEvent('section:content:' + section.uuid);
                        window.dispatchEvent(event);
                     });
                  },
                  navigateSectionItem(section, item_id){
                     this.openSection(section.uuid).then(function(){
                        var event = new CustomEvent('section:i:' + section.uuid, {
                           detail: item_id,
                        });
                        window.dispatchEvent(event);
                     });
                  },
                  navigateSection(item, section){
                     this.openSection(item.uuid).then(function(){
                        var event = new CustomEvent('section:' + section + ':' + item.uuid);
                        window.dispatchEvent(event);
                     });
                  },

                  navigatePage(page){
                     this.generatePost = null;
                     this.previewPage = '-';
                     if(page == '__last_state') {
                        this.page = this.__last_page;
                        return;
                     }
                     this.__last_page = page;
                     this.page = page;
                  },
                  
                  closePage(page = ''){
                     this.page='-';
                     if(!this.$store.builder.detectMobile()) this.page = 'pages';
                  },

                  openMedia(object){
                     // this.$dispatch('open-modal', 'media-modal');
                     this.page = 'media';
                     var event = new CustomEvent("mediaEventDispatcher", {
                           detail: {
                              ...object
                           }
                     });

                     window.dispatchEvent(event);
                  },

                  sidebarNavigate(sidebarClass, route){

                     this.sidebarClass = sidebarClass;

                     Livewire.navigate(route);
                  },


                  // Broadcast

                  broadcastSite(){
                     
                  },
                  currentPage(){
                     var page = this.pages[0];

                     this.pages.forEach((e, index) => {
                        if(e.uuid == this.site.current_edit_page) page = e;
                     });
                     return page;
                  },
                  currentPage2(){
                     var page = this.pages[0];

                     this.pages.forEach((e, index) => {
                        if(e.uuid == this.site.current_edit_page) page = e;
                     });
                     return page;
                  },

                  getCurrentPage(){
                     this.currentPage = this.pages.filter(item => item.uuid == this.site.current_edit_page)[0];

                     if(!this.currentPage && this.pages.length > 0) this.currentPage = this.pages[0];
                  },

                  generateAiPage($prompt){
                     let $this = this;
                     let $page = $this.currentPage;


                     $this.$nextTick(() => {

                        setTimeout(() => {
                           $this.sections.forEach((section, index) => {
                              if($page.uuid == section.page_id){
                                 $this.$store.builder.generateAi(section, $prompt);
                              }
                           });
                        }, 1500);
                     });
                  },

                  generateAiSite(){
                     let $this = this;
                     if(!this.site.ai_generate) return;
                     
                     let $prompt = $this.site.ai_generate_prompt;
                     $this.generateAiPage($prompt);
                     $this.site.ai_generate = 0;
                     $this.saveSite();
                  },

                  saveSite(){
                     let $this = this;
                     clearTimeout($this.autoSaveTimer);

                     $this.autoSaveTimer = setTimeout(function(){
                        $this.$store.builder.savingState = 0;
                        let event = new CustomEvent("builder::saveSite");
                        window.dispatchEvent(event);
                        // $this.$dispatch('builder::saveSite');
                     }, $this.$store.builder.autoSaveDelay);
                  },

                  insertSectionAt(section, $new){
                     let $this = this;
                     let $sections = $this.getSections();
                     let $key = 0;

                     // console.log(section, $new)
                     $sections.forEach((s, i) => {
                         if(s.uuid == section.uuid){
                             $key = (i + 1);
                           //   $new.position = (i + 1);
                         }
                     });
                     $this.sections.splice($key, 0, $new); // Insert at position section
                     $this.getSections().forEach((s, i) => {
                         let $s = $this.sections.filter(obj => obj.uuid === s.uuid)[0];
                        //  if(s.uuid == $new.uuid) return;
                         
                         $s.position = i;

                         if(s.uuid == $new.uuid){
                           $new.position = i;
                         }
                     });
                     // console.log($key, $new, section, $this.getSections())

                     return $new;
                  },

                  getSections(){
                     let $this = this;
                     let sections = [];

                     this.sections.forEach((element, index) => {
                         if($this.currentPage2().uuid == element.page_id){
                             sections.push(element);
                         }
                     });

                     sections = window._.sortBy(sections, 'position');


                     sections.forEach((element, index) => {
                        if($this.site.header.sticky || $this.site.header._float){
                           if(index == 0){
                              element.first_section = true;
                           }
                        }
                     });

                     return sections;
                  },

                  saveStatic(){
                     let $this = this;
                     setTimeout(() => {
                        // let myNodeCopy = document.querySelector(".edit-blocks-container").cloneNode(true);

                        // myNodeCopy.id = "clonedPage";
                        // document.body.appendChild(myNodeCopy);
                        // myNodeCopy.setAttribute( "style", "position: absolute; opacity: 0; pointer-events:none;");
                        // myNodeCopy.querySelectorAll('.builder-section-add-wrapper').forEach(e => {
                        //    e.remove();
                        // });
                        // just select the new cloneNode
                        html2canvas(document.querySelector('.edit-blocks-container'), {
                           useCORS: true,
                           allowTaint: true,
                           onclone: function(doc){
                              doc.querySelectorAll('.builder-section-add-wrapper').forEach(e => {
                                 e.remove();
                              });
                           },
                        }).then(canvas => {
                              let image = canvas.toDataURL('image/png');
                              // document.body.appendChild(canvas);
                              // document.body.removeChild(myNodeCopy);
                              $this.$wire.saveStaticImage(image);
                        });
                     }, 4000);
                  },
                  makeid(length) {
                     let result = '';
                     const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                     const charactersLength = characters.length;
                     let counter = 0;
                     while (counter < length) {
                        result += characters.charAt(Math.floor(Math.random() * charactersLength));
                        counter += 1;
                     }
                     return result;
                  },

                  exportSite(){
                     let $this = this;
                     if(!$this.__o_feature('feature.export_site')){
                        $dispatch('open-modal', 'upgrade-modal');
                        return;
                     }
                     let $allPagesDone = [];
                     let imagesCount = 0;
                     let pagesImages = [];
                     var zip = window.yenaZip();
                     let baseUrl = window.builderObject.baseUrl;
                     baseUrl = baseUrl + '/';
                     $this.exportingSite = true;

                     let checkPages = function(){
                        let imagesC = pagesImages.length > 0 ? pagesImages.length : 0;

                        console.log($allPagesDone.length, $this.pages.length, imagesCount, imagesC)

                        if($allPagesDone.length == $this.pages.length && imagesCount == imagesC){
                           zip.generateAsync({type:"blob"}).then(function(content) {
                              // see FileSaver.js
                              saveAs(content, "export-" + $this.makeid(3) + ".zip");
                              $this.exportingSite = false;
                           });
                        }
                     };

                     window.siteManifestResources.forEach(asset => {
                        let url = asset;
                        let basename = url.split('/').reverse()[0];

                        if(basename.endsWith('.css')){
                           fetch('/'+url)
                           .then(resp => resp.text())
                           .then((rsp) => {
                              let file = rsp;
                              file = file.replace(/\/build\/assets\//g, "");
                              zip.folder(url.substring(0, url.lastIndexOf('/'))).file(basename, file);
                           });
                        }else{
                           fetch('/'+url)
                           .then(resp => resp.arrayBuffer())
                           .then((rsp) => {
                              let file = rsp;
                              zip.folder(url.substring(0, url.lastIndexOf('/'))).file(basename, file);
                           });
                        }

                        // let response = axios.get('/'+url).then(function (response) {
                        //    let file = response.data;
                        //    file = file.replace(/\/build\/assets\//g, "");
                        //    zip.folder(url.substring(0, url.lastIndexOf('/'))).file(basename, file);
                        // });
                     });

                     $this.pages.forEach(page => {
                        let url = $this.$store.builder.generateSiteLink($this.site) + '/' + page.slug;

                        // Create hidden iframe:
                        const iframe = document.createElement("iframe");
                        iframe.src = url + "?preview-iframe=true";
                        iframe.style.display = "none";

                        // Join iframe to DOM:
                        document.body.appendChild(iframe);
                        
		                  iframe.addEventListener("load", () => {
                           setTimeout(function() {
                              let html = iframe.contentWindow.document.documentElement.outerHTML;
                              let staticHtml = $this.$store.builder.removeAlpineAttributes(html);

                              staticHtml.body.querySelectorAll('.yena-footer-branding').forEach((e) => {
                                 e.remove();
                              });
                              // Get all images length

                              let _images = staticHtml.documentElement.querySelectorAll('img');
                              let favicons = staticHtml.documentElement.querySelectorAll('link[rel="icon"], link[rel="shortcut icon"]');
                              
                              let = images = [..._images, ...favicons];


                              images.forEach(e => {
                                 let src = e.getAttribute('src');
                                 if(e.getAttribute('rel')){
                                    src = e.getAttribute('href');
                                 }
                                 if($this.exportImageDirCheck(src)){
                                    imagesCount++;
                                 }
                              });
                              
                              let exportUtilsUrl = window.siteManifestResources.find(url => url.includes('exportUtils'));
                              if (exportUtilsUrl) {
                                 // Create script tag
                                 let script = document.createElement('script');
                                 script.src = exportUtilsUrl;
                                 script.type = 'module';

                                 // Append script tag to head
                                 staticHtml.head.appendChild(script);
                              }
                              staticHtml.body.querySelectorAll('.screen').forEach((e) => {
                                 e.remove();
                              });

                              let cleanedHtml = staticHtml.documentElement.outerHTML;
                              let regex = new RegExp(baseUrl.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g');
                              cleanedHtml = cleanedHtml.replace(regex, '');
                              $allPagesDone.push({
                                 page_id: page.id,
                                 done: true
                              });
                              zip.file(page.slug + '.html', cleanedHtml);
                              

                              $this.getBase64Images(staticHtml.documentElement).then(base64Images => {
                                 base64Images.forEach(r => {

                                    // console.log($this.removeBeforeAssetsImage(/.*?(assets\/image)/, r.src))

                                    let file = r.src.split('/').reverse()[0];


                                    let folder = 'media/site/images';
                                    let base64 = r.base64.replace(/^data:image\/?[A-z]*;base64,/, '');
                                    

                                    let afterProcess = $this.removeBeforeAssetsImage(/.*?(media\/site)/, r.src);
                                    let afterProcessFile = afterProcess.split('/').reverse()[0];
                                    afterProcess = afterProcess.replace('/' + afterProcessFile, '');
                                    folder = afterProcess;


                                    if(r.src.includes('assets/image')){
                                       folder = 'assets/image/';


                                       afterProcess = $this.removeBeforeAssetsImage(/.*?(assets\/image)/, r.src);
                                       afterProcessFile = afterProcess.split('/').reverse()[0];
                                       afterProcess = afterProcess.replace('/' + afterProcessFile, '');
                                       folder = afterProcess;
                                    }

                                    zip.folder(folder).file(file, base64, {
                                       base64: true
                                    });

                                    pagesImages.push(r.src);
                                    checkPages();
                                 })
                              }).catch(error => {
                                 console.error('Error converting images:', error);
                              });

                              checkPages();
                           }, 1500);
                        });
                     });
                  },
                  removeBeforeAssetsImage(regex, str) {
                     return str.replace(regex, '$1');
                  },
                  exportImageDirCheck(src){

                     let response = false;
                     
                     if(src !== null && src.includes('media/site') || src !== null && src.includes('assets/image')){
                        response = true;
                     }

                     if(src !== null && !this.hasFileExtension(src)){
                        response = false;
                     }

                     return response;
                  },
                  hasFileExtension(url) {
                     // Define a regular expression to match common file extensions
                     const fileExtensionPattern = /\.[0-9a-z]+$/i;

                     // Test the URL against the regular expression
                     return fileExtensionPattern.test(url);
                  },
                  isImageUrl(url) {
                     // Define the image file extensionsjpeg,png,jpg,gif,svg,webp
                     const imageExtensions = /\.(jpg|jpeg|png|gif|bmp|webp|svg)$/i;

                     // Test the URL against the regular expression
                     return imageExtensions.test(url);
                  },
                  getBase64Images(doc) {
                     let $this = this;
                     // Get all image elements
                     let _images = doc.querySelectorAll('img');
                     let favicons = doc.querySelectorAll('link[rel="shortcut icon"]');
                     
                     _images = [..._images, ...favicons];

                     let images = [];

                     _images.forEach(e => {
                        let src = e.getAttribute('src');
                        if(e.getAttribute('rel')){
                           src = e.getAttribute('href');
                        }
                        
                        if($this.exportImageDirCheck(src)){
                           images.push(src);
                        }
                     });
                  
                     // Function to convert image to base64
                     function toBase64(img) {
                        return new Promise((resolve, reject) => {
                           let canvas = document.createElement('canvas');
                           canvas.width = img.width;
                           canvas.height = img.height;
                           let ctx = canvas.getContext('2d');
                           ctx.drawImage(img, 0, 0);
                           
                           resolve(canvas.toDataURL());
                        });
                     }
                  
                     // Convert all images to base64
                     return Promise.all(Array.from(images).map(src => {
                           return new Promise((resolve, reject) => {
                              const newImg = new Image();
                              newImg.crossOrigin = 'Anonymous'; // To avoid CORS issues
                              newImg.onload = () => {
                                    let canvas = document.createElement('canvas');
                                    canvas.width = newImg.width;
                                    canvas.height = newImg.height;
                                    let ctx = canvas.getContext('2d');
                                    ctx.drawImage(newImg, 0, 0);
                                    let base64 = canvas.toDataURL();
                                    resolve({ src, base64 });
                              };
                              newImg.onerror = reject;
                              newImg.src = src;
                           });
                     }));
                  },

                  getProduct(item_id){

                     let product = [];

                     this.products.forEach(item => {
                        if(item.id == item_id){
                           product = item;
                        }
                     });
                     return product;
                  },

                  getCourse(item_id){
                     let course = [];

                     this.courses.forEach(item => {
                        if(item.id == item_id){
                           course = item;
                        }
                     });
                     return course;
                  },
                  // Save
                  init(){
                     var $this = this;

                     // $this.exportSite();
                     // $this.saveStatic();

                     this.$watch('site' , (value, _v) => {
                        $this.saveSite();
                     });

                     this.$wire.$on('createSectionItem', function(content){
                        // $this.$wire.createSectionItem(content).then(item => {
                        //    console.log(item)
                        // });
                     });
                     window.addEventListener('createSectionItem', (event) => {
                        //console.log(event.detail)
                     });
                     window.addEventListener('deleteSectionItem', (event) => {
                        // console.log(event.detail)
                     });
                     window.addEventListener('builder::createdSection', (event) => {
                        $this.sections.push(event.detail[0]);
                     });

                     this.getCurrentPage();
                     $this.generateAiSite();

                     if(this.$store.builder.detectMobile()){
                        this.page='-';
                     }

                     this.$store.builder.waitForElm('.--create-section').then((el) => {
                        document.addEventListener('click', function(event) {
                           // Check if the clicked element is not within the divElement
                           if (!el.contains(event.target)) {
                              // Code to execute when click is outside the divElement
                              $this._create_position=null;

                              // console.log('clicked outside of divElement')
                           }
                        });
                     });
                     
                     // let divElement = document.querySelector('.--create-section');

                     // // Add a click event listener to the document
                     // document.addEventListener('click', function(event) {
                     //    // Check if the clicked element is not within the divElement
                     //    if (!divElement.contains(event.target)) {
                     //       // Code to execute when click is outside the divElement
                     //       console.log('Clicked outside the div!');
                     //    }
                     // });
                  }
               }
            });
        </script>
   @endscript
   </div>
</div>
