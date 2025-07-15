
<?php

   use App\Models\MediakitSiteSocial;
   use App\Models\MediakitSection;
   use App\Models\MediakitSectionItem;
   use App\Livewire\Actions\ToastUp;
   use function Livewire\Volt\{state, mount, on, updated, uses};

   uses([ToastUp::class]);

   state([
      'site',
      'sections'  => [],
      'section'   => [],
      'pages'     => [],
      'story'     => [],
      'products'  => [],
      'courses'   => [],
      'siteArray' => [],
      'bookingServices' => [],

      'sectionConfig' => fn () => __collect_sectons(),
      'planFeatures' => fn() => $this->site->user()->first()->planJsFeatures(),
   ]);

   state([
       'currency' => fn() => $this->site->user->currency(),
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
      $this->getSections();
      $this->siteArray = $this->site->toArray();

      // dd(iam()->planJsFeatures());
   });

   on([
      'builder::refreshSections' => function($section){
         $this->getSections();
      },

      'section::create' => function($section){
         $this->skipRender();

         // dd($this->site->getEditingPage(), $this->site->pages()->get());

         $_section = new MediakitSection;
         $_section->fill($section);
         $_section->site_id = $this->site->id;
         $_section->page_id = $this->site->getEditingPage();
         $_section->published = 1;
         $_section->uuid = __a($section, 'uuid');
         $_section->save();

         if(is_array($items = __a($section, 'items'))){
            foreach ($items as $key => $value) {
               $_item = new MediakitSectionItem;
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
         if(!$_item = MediakitSectionItem::where('uuid', $item)) return;

         $_item->delete();

         // Dispatch another event with status
      },

      'section::create_section_item' => function($item, $section_id){

         // Check if i own this section;
         $_item = new MediakitSectionItem;
         $_item->fill($item);
         $_item->section_id = $section_id;
         $_item->save();

         // Dispatch another event with status
         $this->dispatch('section::created_section', $_item);
      },


      'builder::saveSection' => function ($section, $js = null){
         $this->skipRender();
         if(!$_section = $this->site->sections()->where('uuid', __a($section, 'uuid'))->first()) return;
         $_section->fill($section);

         $_section->save();


         if(!$js){
            $this->js('$store.builder.savingState = 2');
         }else{
            $this->js($js);
         }
      },

      'builder::save_sections_and_items' => function ($section, $js){
         $this->skipRender();
         if(!$_section = $this->site->sections()->where('uuid', __a($section, 'uuid'))->first()) return;
         $_section->fill($section);

         $_section->save();
         
         if(is_array($items = __a($section, 'items'))){
            foreach ($items as $key => $value) {
               if(!$_item = MediakitSectionItem::where('uuid', __a($value, 'uuid'))->first()) continue;
               $_item->fill($value);

               // $_item->uuid = __a($value, 'uuid');
               $_item->save();
            }
         }

         // $this->js('console.log('. json_encode($section) .')');
         $this->js($js);
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
               if(!$_social = MediakitSiteSocial::where('uuid', __a($value, 'uuid'))->first()) continue;
               $_social->fill($value);
               $_social->site_id = $this->site->id;
               $_social->save();
            }
         }

         $this->js('$store.builder.savingState = 2');
      },

      // Page
      'builder::savePage' => function($page, $js = null){
         $this->skipRender();
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

   $deleteSection = function($id){
      $this->skipRender();

      if(!$section = MediakitSection::where('uuid', $id)->where('site_id', $this->site->id)->first()) return;

      MediakitSectionItem::where('section_id', $section->uuid)->delete();

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

   $getHeaderLinks = function(){
      $this->headerLinks = $this->site->header_links()->where('parent_id', '=', null)->get()->toArray();
   };

   $getFooterGroups = function(){
      $this->footerGroups = $this->site->footer_groups()->get()->toArray();
   };

   $getBooking = function(){
      $this->bookingServices = BookingService::where('user_id', iam()->id)->get()->map(function($item) {
         // $avgRating = ProductReview::where('product_id', $item->id)->avg('rating');

         $item->route = null;
         $item->price_html = $item->getPrice();

         // $item->variants = $item->variant()->where('type', 'color')->get()->toArray();
         // $item->route = null;
         // $item->avg_rating = number_format($avgRating, 1);
         // $item->featured_image = $item->getFeaturedImage();

         // // Add tag "new" if the product was recently created
         // if ($item->created_at >= now()->subDays(5)) {
         //    $item->tag = 'new';
         // }

         // // Add tag "hot" if the product has the highest rating
         // $highestRatedProduct = Product::withAvg('reviews', 'rating')
         //    ->orderBy('reviews_avg_rating', 'DESC')
         //    ->first();

         // if ($highestRatedProduct && $item->id == $highestRatedProduct->id) {
         //    $item->tag = 'hot';
         // }


         return $item;
      })->toArray();


      // $date = \Carbon\Carbon::now();
      // $user = $this->site->user;
      // $timeClass = new \App\Yena\BookingTime($user->id);


      // $day_id = $timeClass->get_day_id(date('l', strtotime($date)));
      // $start_time = $timeClass->format_minutes(ao($user->booking_workhours, "$day_id.from"));
      // $end_time = $timeClass->format_minutes(ao($user->booking_workhours, "$day_id.to"));

      // $lowestPrice = \App\Models\BookingService::where('user_id', $user->id)->min('price') ?: 0;

      // $lowestPrice = $user->price($lowestPrice);

      // $this->bookingSettings = [
      //    'available' => $timeClass->check_workday(date('l', strtotime($date->format('Y-m-d'))), $user->id),
      //    'timevalue' => implode('-', [$start_time, $end_time]),
      //    'services' => \App\Models\BookingService::where('user_id', $user->id)->count(),
      //    'lowestPrice' => $lowestPrice
      // ];
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

   $getStory = function(){
      $this->story = $this->site->getStory()->orderBy('id', 'asc')->get()->toArray();
   };
?>
<div>
   <div class="editor-panel bio-builder-wrapper" x-data="yenaBuilder" :class="{'maximize': renderView == 'max'}">
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
      

      <livewire:components.mediakit.layout.toolbar :$site :key="uukey('builder', 'toolbar')" />
      {{-- <livewire:components.mediakit.parts.openai :key="uukey('builder', 'openai-backend')" />
      <livewire:components.mediakit.parts.ai :key="uukey('builder', 'ai-text-backend')" /> --}}
      
      <div class="container !max-w-[initial]">
         <div class="projects">
            <livewire:components.mediakit.layout.sidebar :$site lazy="on-load" :key="uukey('builder', 'sidebar')" />


            <div class="container-small !max-w-[initial] edit-board">
               <div class="flex flex-col w-[100%]">
                  <div class="edit-blocks-container flex-1">
                     @persist('builder')
                        <div wire:ignore class="flex">
                           <livewire:components.mediakit.build lazy :$site :key="uukey('builder', 'components.mediakit.build')" />


                           {{-- <div class="relative flex-grow rounded-tl-[0] rounded-br-[30px] rounded-tr-[30px] rounded-bl-[0] justify-center hidden lg:flex !p-0 md:[&_.flipdown]:[zoom:0.5]" x-cloak :class="{
                              'overlay backdrop !flex-col': $store.builder.detectMobile(),
                              '!flex': $store.builder.detectMobile() && openPreview,
                           }" x-init="$watch('openPreview', value => {
                                 if (value) {
                                       document.body.classList.add('overflow-y-hidden');
                                 } else {
                                       document.body.classList.remove('overflow-y-hidden');
                                 }
                              })" @click="openPreview=false">
                              <div class="lg:min-w-[345px] lg:max-w-[445px]">
                                 <div class="sticky -top-4 z-[15]">
                                    <div class="flex flex-col items-center px-6 pb-0">
                                       <div class="PreviewM PagePreviewWrapper" @click="$event.stopPropagation()" :class="{
                                          'mt-6': !$store.builder.detectMobile(),
                                          'mt-2': $store.builder.detectMobile(),
                                       }">
                                          <div class="PagePreview">
                                             <div class="h-full bg-white remove-scrollbar overflow-y-auto overflow-x-hidden">
                                                <div wire:ignore>
                                                   <livewire:components.mediakit.index lazy :$site :key="uukey('builder', 'components.mediakit.index')" />
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              
                              <div class="px-6 lg:!hidden">
                                 <a @click="openPreview=false" class="yena-black-btn mt-1 !justify-between"><i class="ph ph-eye-slash"></i> {{ __('Close') }}</a>
                              </div>
                           </div> --}}
                        </div>
                     @endpersist
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div wire:ignore>
         @if (is_array($s = config("bio.sections")))
            @foreach($s as $key => $item)
               @php
                  if(!$_name = __a($item, 'components.alpineView')) continue;
      
                  $_name = str_replace('/', '.', $_name);
                  $component = "livewire::components.mediakit.$_name";
      
      
                  $baseName = basename(__a($item, 'components.alpineView'));
      
                  $tag = 'template';
                  $cond = 'bit-component="section-'.$key.'"';
                  
                  // if(str()->startsWith($baseName, '-')) {
                  //    $tag = 'div';
                  //    $cond = 'show';
                  //    $name = str_replace(['-', '+'], '', $baseName);
                  //    $component = "components/mediakit/sections/$key/$name";
                  // }
      
               @endphp
               <{{ $tag }} {!! $cond !!}>
                  <div wire:ignore>
                     <x-dynamic-component :component="$component"/>
                     {{-- @if(str()->startsWith($baseName, '-'))
                        <livewire:is :component="$component" :key="uukey('f::component', 'component:front-section' . $component)">
                        @else
                        <x-dynamic-component :component="$component"/>
                     @endif --}}
                  </div>
               </{{$tag}}>
               
               @if ($n = __a($item, 'components.alpinePost'))
                   <div class="section-post-{{ $key }}">
                     <livewire:is :component="'components/mediakit/sections/' . $key . '/' . basename($n)" :key="uukey('p::component', 'component:post-section' . $n)">
                   </div>
               @endif
            @endforeach
         @endif
      </div>
   
      <div wire:ignore>
         @if (is_array($b = config('bio.layout-banners')))
            @foreach ($b as $key => $item)
               @php
                  $component = "livewire::components.mediakit.banner.--$key-banner";
                  $livewireComponent = "components.mediakit.banner.--$key-banner";
               @endphp
               <template bit-component="section-banner-{{ $key }}">
                  {{-- <livewire:is :component="$livewireComponent" :$site lazy :key="uukey('builder::index', 'builder\banner\component' . $component)"> --}}

                  <x-dynamic-component :component="$component"/>
               </template>
            @endforeach
         @endif
      </div>

   <div class="ai-chat-button lg:!hidden">
       <a @click="openPreview=true;" class="yena-a-button flex">
           <span>
             <i class="ph ph-eyes text-lg"></i>
           </span>
           <div class="-txt">
               <span>
                   {{ __('Preview') }}
               </span>
           </div>
       </a>
   </div>

   <div class="nav-bottom">
      <div class="nav-list">
         <ul>
            <li>
               <a @click="navigatePage('section')">
                  <span>
                     {!! __i('interface-essential', 'item-pen-text-square') !!}
                  </span>
                  {{ __('Section') }}
               </a>
            </li>
            <li :class="{'active': page=='my'}">
               <a @click="navigatePage('my')">
                  <span>
                     {!! __i('interface-essential', 'browser-internet-web-network-window-app-icon') !!}
                  </span>
                  {{ __('LinkinBio') }}
               </a>
            </li>
            <li>
               <a @click="navigatePage('pages')">
                  <span>
                     {!! __i('interface-essential', 'item-pen-text-square') !!}
                  </span>
                  {{ __('Pages') }}
               </a>
            </li>
            <li>
               <a @click="navigatePage('story')">
                  <span>
                     {!! __icon('interface-essential', 'heart-favorite-like-story') !!}
                  </span>
                  {{ __('Story') }} 
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
     <div class="yena-menu-list !w-[100%]">
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
      <div class="yena-menu-list !w-[100%]">
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
    <div class="yena-menu-list !w-[100%]">
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

         <livewire:components.mediakit.parts.share :key="uukey('builder', 'share-modal')">
      </x-modal>
   </template>
   @script
        <script>
            Alpine.data('yenaBuilder', () => {
               return {
                  openPreview: false,
                  renderView: 'normal',
                  renderMobile: false,
                  sidebarClass: '',
                  openChat: true,
                  generateAiId: false,
                  openBooking: false,
                  page: 'edit',
                  __last_page: null,

                  sections:      {!! json_encode($sections) !!},
                  currency:      @entangle('currency'),
                  site:          @entangle('siteArray'),
                  sectionConfig: @entangle('sectionConfig'),
                  planFeatures:  @entangle('planFeatures'),
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
                  deleteSectionId: false,

                  deleteSection(item){
                     this.deleteSectionId = item;
                  },

                  __delete_section(item){
                     this.sections.forEach((e, index) => {
                        if(item.uuid == e.uuid){
                              this.sections.splice(index, 1);
                        }
                     });

                     this.$dispatch('section::delete', {
                        id: item.uuid
                     });
                     this.deleteSectionId=false;
                  },
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

                  setPage(page_id){
                     this.site.current_edit_page = page_id;

                     this.broadcastSite();
                     this.$dispatch('builder::setPage');
                     this.$dispatch('builder::saveSite');
                     
                     this.getCurrentPage();
                  },

                  openSection(_id){
                     let $this = this;
                     var section = $this.sections.filter(item => item.uuid == _id)[0];

                     var call = function(){
                        return new Promise(resolve => {
                           $this.section = section;
                           console.log(section)
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

                  getBookingService(item_id){
                     let booking = [];

                     this.bookingServices.forEach(item => {
                        if(item.id == item_id){
                           booking = item;
                        }
                     });
                     return booking;
                  },
                  // Save
                  init(){
                     var $this = this;
                     
                    this.$watch('currentPage' , (value, _v) => {
                        // if(!$this.currentPage.seo) $this.currentPage.seo = [];
                        $this.$dispatch('builder::updatePage', $this.currentPage);
                        clearTimeout($this.autoSaveTimer);

                        $this.autoSaveTimer = setTimeout(function(){
                            $this.$store.builder.savingState = 0;
                            event = new CustomEvent("builder::savePage", {
                                detail: {
                                    page: $this.currentPage,
                                    js: '$store.builder.savingState = 2',
                                }
                            });

                            window.dispatchEvent(event);
                        }, $this.$store.builder.autoSaveDelay);
                    });

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

                     $this.$watch('page', value => {
                           if (this.$store.builder.detectMobile() && value !== '-') {
                              document.body.classList.add('!overflow-y-hidden');
                           } else {
                              document.body.classList.remove('!overflow-y-hidden');
                           }
                     })
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
