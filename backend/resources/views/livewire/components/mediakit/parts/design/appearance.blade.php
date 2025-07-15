
<?php

   use function Livewire\Volt\{state, mount, placeholder};
   state([
      '__page' => '-',
      'site',

      'colors' => [
         'ECB159',
         'EEA5A6',
         'BBC3A4',
         'FFF6E9',
         '436850',
         '3B3486',
         '030637',
         '3E3232',
      ]
   ]);

//    placeholder('placeholders.console.builder.sidebar-design');

?>

<div>

   <div x-data="builder__design" :style="styles()">

      <div x-show="__page == 'fonts'">
          <div>
                     
               <div class="settings-section section">
                  <div class="settings-section-content">
            
                     <div class="top-bar">
                        <div class="flex items-center justify-between mb-4 pt-[26px] px-[36px]">
                           <div class="flex items-center gap-3">
                              <div class="rounded-[15px] w-[33px] h-[33px] flex items-center justify-center bg-gray-200 cursor-pointer">
                                    <a @click="__page='-'">
                                       <span>
                                          {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                                       </span>
                                    </a>
                              </div>
                              <div class="text-xl font-extrabold tracking-[-1px]">{{ __('Fonts') }}</div>
                           </div>
                        </div>
                        <div class="sticky container-small">
                              <div class="tab-link">
                                 <ul class="tabs">
                                 {{-- <li class="tab !w-[100%]" @click="_tab = 'title'" :class="{'active': _tab == 'title'}">{{ __('Title') }}</li> --}}
                                 <li class="tab !w-[100%]" @click="_tab = 'body'" :class="{'active': _tab == 'body'}">{{ __('Body') }}</li>
                                 </ul>
                              </div>
                        </div>
                     </div>
                     <div class="container-small tab-content-box !overflow-hidden">
                        <div class="tab-content">
                              <div x-cloak :class="{'active': _tab == 'body'}" data-tab-content>
                                 <div class="w-[100%] py-0 px-[var(--s-2)] mt-2">
                                    <div class="search-font">
                                       <form>
                                          <div class="input-box search-box">
                                             <input type="text" class="input-large search-input" x-model="bodySearch" placeholder="{{ __('Search...') }}">
                                             <div class="input-icon zoom-icon">
                                                {!! __i('--ie', 'search.1', 'w-5 h-5') !!}
                                             </div>
                                          </div>
                                       </form>
                                    </div>
                                    <div class="font-list">
                                       <ul>
                                          
                                          <li class="heading-body active" :class="{'!hidden': !site.settings.fontName}">
                                             <span x-text="site.settings.fontName"></span>
                                             <span>
                                                {!! __i('--ie', 'done-check.3', 'w-5 h-5') !!}
                                             </span>
                                          </li>

                                          <template x-for="(item, index) in filteredBodyFont()" :key="index">
                                             <li class="heading-body" x-text="item[0]" @click="selectBodyFont(item)"></li>
                                          </template>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                        </div>
                     </div>
                  </div>
               </div>
          </div>
      </div>

      <div x-cloak x-show="__page == '-'">
        <div class="settings-section section">
            <div class="settings-section-content">
        
                <div class="top-bar">
                  <div class="flex items-center justify-between mb-4 pt-[26px] px-[36px]">
                      <div class="flex items-center gap-3">
                          <div class="text-xl font-extrabold tracking-[-1px]">{{ __('Design') }}</div>
                      </div>
                  </div>
                 <div class="sticky container-small">
                     <div class="tab-link">
                         <ul class="tabs">
                         <li class="tab !w-[100%]" @click="__tab = 'themes'" :class="{'active': __tab == 'themes'}">{{ __('Themes') }}</li>
                         <li class="tab !w-[100%]" @click="__tab = 'custom'" :class="{'active': __tab == 'custom'}">{{ __('Custom') }}</li>
                         </ul>
                     </div>
                 </div>
                </div>
                <div class="container-small tab-content-box !overflow-hidden">
                    <div class="tab-content">
                        <div x-cloak :class="{'active': __tab == 'themes'}" data-tab-content>
                           <div class="w-[100%] py-0 px-[var(--s-2)] mt-[var(--s-2)]">
                              <div class="theme-list !grid !grid-cols-2 md:!grid-cols-4">
                                 <template x-for="(item, index) in themes" :key="index">
                                    <div :class="{'active !bg-[var(--c-mix-10)]': site.settings.currentTheme == item.id}" class="!rounded-lg !bg-[#f1f1f1] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] !w-[100%]" @click="setTheme(item)">
                                       <div class="theme-text !p-0 !mb-0 !rounded-lg overflow-hidden [box-shadow:0_0.25em_0.375em_-0.0625em_rgba(0,_0,_0,_0.1),_0_0.125em_0.25em_-0.0625em_rgba(0,_0,_0,_0.06),_0_0_0_0.0625em_rgb(255_255_255_/_64%)]">
                                             <div class="grid [grid-template:'accent'_minmax(2.5em,_auto)_'body'_/_1fr] overflow-hidden flex-1 pointer-events-none" :style="{
                                                'background': '#' + item.config.color,
                                             }"></div>
                                          <div class="p-[var(--s-1)]">
                                             <h1 class="!text-[1.6em]" :style="{
                                                'font-family': item.config.fontName
                                             }">{{ __('Title') }}</h1>
                                             
                                             <p class="overflow-hidden overflow-ellipsis !text-[0.9em] !mt-0 !mb-0 font-normal text-[#3d3838]" :style="{
                                                'font-family': item.config.fontName
                                             }">Body & <a class="text-[#2D2E34] cursor-pointer font-bold !underline">link</a>
                                          </p>
   
                                          <button class="btn mt-1" :style="{
                                             'font-family': item.config.fontName,
                                             'background': '#' + item.config.color,
                                             'color': $store.builder.getContrastColor('#' + item.config.color),
                                             'border-radius': item.config.corner == 'rounded' ? 'var(--r-full)' : (item.config.corner == 'round' ? 'var(--r-small)' : (item.config.corner == 'straight' ? 'var(--r-none)' : '')),
                                          }">
                                          {{ __('Link') }}   
                                             </button>
                                          </div>
                                       </div>
                                       {{-- <div class="theme-title">
                                          <p x-text="item.name"></p>
                                          <span x-show="site.settings.currentTheme == index" x-cloak>
                                             {!! __i('--ie', 'done-check.3', 'w-5 h-5') !!}
                                          </span>
                                       </div> --}}
                                    </div>
                                 </template>
                              </div>
                           </div>
                        </div>
                        <div x-cloak :class="{'active': __tab == 'custom'}" data-tab-content>
                           <div class="w-[100%] px-[var(--s-2)] py-[0] mt-[var(--s-2)] mt-4">
                              <div class="colors-container">
                                 <div class="input-box">
                                    <div class="input-label">{{ __('Color') }}</div>
                                    <div class="input-group">
                                       <div class="color-selector">
                                          @if(is_array($colors))
                                             @foreach ($colors as $item)
                                             <div class="color-box mod" @click="site.settings.color='{{ $item }}'" style="--c: #{{ $item }};"><span style="--c: #{{ $item }}; background: #{{ $item }}"></span></div>
                                             @endforeach
                                          @endif
                                       </div>
                                       <div class="custom-color !block">
                                          <form onsubmit="return false;">
                                             <div class="input-box !pb-0">
                                                <div class="input-group">
                                                   <input type="text" class="input-small input-color" x-model="site.settings.color" :style="{
                                                      'background-color': '#'+site.settings.color,
                                                      'color': $store.builder.getContrastColor(site.settings.color)
                                                      }" maxlength="6">
                                                   <span class="hash"  :style="{
                                                      'color': $store.builder.getContrastColor(site.settings.color)
                                                      }">#</span>
                                                   <span class="color-generator" :style="{
                                                      'background-color': '#'+site.settings.color,
                                                      'color': $store.builder.getContrastColor(site.settings.color)
                                                      }"></span>
                                                </div>
                                             </div>
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="font-style">
                                 <div class="input-box">
                                    <div class="input-label">{{__('Fonts')}}</div>
                                    <div class="input-group">
                                       <button :style="{'font-family': site.settings.fontHeadName}" type="button" @click="__page='fonts'; _tab='title';">
                                          {{ __('Title') }} 
                                          <span>
                                             {!! __i('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                                          </span>
                                       </button>
                                       <button :style="{'font-family': site.settings.fontName}" type="button" @click="__page='fonts'; _tab='body';">
                                          {{ __('Body') }} 
                                          <span>
                                             {!! __i('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                                          </span>
                                       </button>
                                    </div>
                                 </div>
                              </div>

                                                   
                              <form onsubmit="return false">
                                 <div class="input-box">
                                    <div class="input-label">{{ __('Color') }}</div>
                                    <div class="input-group btns" id="color">
                                       <button class="btn-nav" :class="{
                                          'active' : site.background !== null && site.background.color == 'transparent',
                                       }" type="button" @click="site.background.color = 'transparent'">
                                          {!! __i('--ie', 'delete-disabled-ross-hexagon.1', 'w-5 h-5') !!}
                                       </button>
                                       <button class="btn-nav" :class="{
                                          'active' :  site.background !== null && site.background.color == 'default',
                                       }" type="button" @click="site.background.color = 'default'">
                                       <span class="w-4 h-4 rounded-full bg-[#ccc]"></span>
                                       </button>
                                       <button class="btn-nav" :class="{
                                          'active' :  site.background !== null && site.background.color == 'accent',
                                       }" type="button" @click="site.background.color = 'accent'">
                                       <span class="w-4 h-4 rounded-full bg-[var(--accent)]"></span>
                                       </button>
                                    </div>
                                 </div>
                                 <div class="relative block h-20 mb-1 text-center border-2 border-dashed rounded-lg cursor-pointer group bg-white- hover:border-solid hover:border-yellow-600" :class="{
                                 'border-gray-200': !site.background.image,
                                 'border-transparent': site.background.image,
                                 }">
                                 <template x-if="site.background.image">
                                    <div class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden w-[100%] h-full group-hover:flex">
                                       <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full icon-shadow" @click="site.background.image = ''; $dispatch('appearanceBackgroundBg', {
                                       image: null,
                                       public: null,
                                       })">
                                          <i class="fi fi-rr-trash"></i>
                                       </div>
                                    </div>
                                 </template>
                                 <template x-if="!site.background.image">
                                    <div class="flex items-center justify-center w-[100%] h-full" @click="openMedia({
                                       event: 'appearanceBackgroundBg',
                                       sectionBack:'navigatePage(\'__last_state\')'
                                 });">
                                       <div>
                                             <span class="m-0 -mt-2 text-black loader-line-dot-dot font-2"></span>
                                       </div>
                                       <i class="fi fi-ss-plus"></i>
                                    </div>
                                 </template>
                                 <template x-if="site.background.image">
                                    <div class="h-full w-[100%]">
                                       <img :src="$store.builder.getMedia(site.background.image)" class="h-full w-[100%] object-cover rounded-md" alt="">
                                    </div>
                                 </template>
                                 </div>
                                 
                                 {{-- <div class="input-box">
                                    <div class="input-label">{{ __('Height') }}</div>
                                    <div class="input-group btns two-col-btns">
                                    <button class="btn" :class="{
                                       'active' : site.background !== null && site.background.height == 'fill',
                                    }" type="button" @click="site.background.height = 'fill'">{{ __('Fill') }}</button>
                                    <button class="btn" :class="{
                                       'active' : site.background !== null && site.background.height == 'fit',
                                    }" type="button" @click="site.background.height = 'fit'">{{ __('Fit') }}</button>
                                 </div>
                                 </div> --}}
                                 
                                 <div class="input-box">
                                    <div class="input-label">{{ __('Width') }}</div>
                                    <div class="input-group btns two-col-btns">
                                    <button class="btn" :class="{
                                       'active' : site.background !== null && site.background.width == 'fill',
                                    }" type="button" @click="site.background.width = 'fill'">{{ __('Fill') }}</button>
                                    <button class="btn" :class="{
                                       'active' : site.background !== null && site.background.width == 'fit',
                                    }" type="button" @click="site.background.width = 'fit'">{{ __('Fit') }}</button>
                                 </div>
                                 </div>
                                 

                              <template x-if="site.background !== null && site.background.image && site.background.overlay && site.background.color !== 'transparent'">
                                 <div class="input-box">
                                    <div class="input-label">{{__('Overlay')}}</div>
                                    <div class="input-group btns">
                                       <button class="btn" type="button" :class="{'active': site.background !== null && site.background.overlay_size == 's'}" @click="site.background.overlay_size = 's'">S</button>
                                       <button class="btn" type="button" :class="{'active': site.background !== null && site.background.overlay_size == 'm'}" @click="site.background.overlay_size = 'm'">M</button>
                                       <button class="btn" type="button" :class="{'active': site.background !== null && site.background.overlay_size == 'l'}" @click="site.background.overlay_size = 'l'">L</button>
                                    </div>
                                 </div>
                              </template>

                              <template x-if="site.background !== null && site.background.blur && site.background.image && !site.background.parallax">
                                 <div class="input-box">
                                    <div class="input-label">{{__('Blur')}}</div>
                                    <div class="input-group btns">
                                       <button class="btn" type="button" :class="{'active': site.background !== null && site.background.blur_size == 's'}" @click="site.background.blur_size = 's'">S</button>
                                       <button class="btn" type="button" :class="{'active': site.background !== null && site.background.blur_size == 'm'}" @click="site.background.blur_size = 'm'">M</button>
                                       <button class="btn" type="button" :class="{'active': site.background !== null && site.background.blur_size == 'l'}" @click="site.background.blur_size = 'l'">L</button>
                                    </div>
                                 </div>
                              </template>

                              {{-- <template x-if="site.background !== null && site.background.height == 'fit'">
                                 <div class="input-box">
                                    <div class="input-label">{{__('Spacing')}}</div>
                                    <div class="input-group btns">
                                       <button class="btn" type="button" :class="{'active': site.background !== null && site.background.spacing == 's'}" @click="site.background.spacing = 's'">S</button>
                                       <button class="btn" type="button" :class="{'active': site.background !== null && site.background.spacing == 'm'}" @click="site.background.spacing = 'm'">M</button>
                                       <button class="btn" type="button" :class="{'active': site.background !== null && site.background.spacing == 'l'}" @click="site.background.spacing = 'l'">L</button>
                                       <button class="btn" type="button" :class="{'active': site.background !== null && site.background.spacing == 'xl'}" @click="site.background.spacing = 'xl'">XL</button>
                                    </div>
                                 </div>
                              </template> --}}

                              {{-- <template x-if="site.background !== null && site.background.height == 'fill'">
                                 <div class="input-box">
                                    <div class="input-label">{{__('Align')}}</div>
                                    <div class="input-group btns">
                                       <button class="btn-nav" :class="{'active': site.background !== null && site.background.align == 'left'}" type="button" @click="site.background.align = 'left'">
                                          {!! __i('Type, Paragraph, Character', 'align-left') !!}
                                       </button>
                                       <button class="btn-nav" :class="{'active': site.background !== null && site.background.align == 'center'}" type="button" @click="site.background.align = 'center'">
                                          {!! __i('Type, Paragraph, Character', 'align-center') !!}
                                       </button>
                                       <button class="btn-nav" :class="{'active': site.background !== null && site.background.align == 'right'}" type="button" @click="site.background.align = 'right'">
                                          {!! __i('Type, Paragraph, Character', 'align-right') !!}
                                       </button>
                                    </div>
                                 </div>
                              </template> --}}
                              
                              <template x-if="site.background.image">
                                 <div class="advanced-section-settings">
                                    <form onsubmit="return false">
                                       <div class="input-box open-tab-box" :class="{'!hidden': site.background.color == 'transparent'}">
                                          <div class="input-group">
                                             <div class="switchWrapper">
                                                <input id="showOverlay-switch" x-model="site.background.overlay" type="checkbox" class="switchInput">
                                                
                                                <label for="showOverlay-switch" class="switchLabel">{{ __('Overlay') }}</label>
                                                <div class="slider"></div>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="input-box open-tab-box" :class="{'!hidden': site.background.parallax}">
                                          <div class="input-group">
                                             <div class="switchWrapper">
                                                <input id="showBlur-switch" x-model="site.background.blur" type="checkbox" class="switchInput">
                                                
                                                <label for="showBlur-switch" class="switchLabel">{{ __('Blur') }}</label>
                                                <div class="slider"></div>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="input-box open-tab-box">
                                          <div class="input-group">
                                             <div class="switchWrapper">
                                                <input id="showGreyscale-switch" x-model="site.background.greyscale" type="checkbox" class="switchInput">
                                                
                                                <label for="showGreyscale-switch" class="switchLabel">{{ __('Greyscale') }}</label>
                                                <div class="slider"></div>
                                             </div>
                                          </div>
                                       </div>
                                       {{-- <div class="input-box open-tab-box">
                                          <div class="input-group">
                                             <div class="switchWrapper">
                                                <input id="showParallax-switch" x-model="site.background.parallax" type="checkbox" class="switchInput">
                                                
                                                <label for="showParallax-switch" class="switchLabel">{{ __('Parallax') }}</label>
                                                <div class="slider"></div>
                                             </div>
                                          </div>
                                       </div> --}}
                                    </form>
                           </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
      </div>
    
      @script
      <script>
          Alpine.data('builder__design', () => {
             return {
               __tab: 'themes',
               _tab: 'body',
               __page: @entangle('__page'),
               autoSaveTimer: null,
               fonts: [],
               font: [],

               bodyFont: [],
               bodySearch: '',

               headFont: [],
               headSearch: '',

               themes: {!! collect(config('yena.themes')) !!},
               setTheme(item){
                  this.site.settings.currentTheme = item.id;
                  this.site.settings = {
                     ...this.site.settings,
                     ...item.config,
                  }
               },
               selectHeadFont(item){
                  this.site.settings.fontHeadName = item[0];
                  this.site.settings.fontHeadSettings = item[1];
               },
               filteredHeadFont() {
                  return this.headFont.filter((item) => {
                     var name = item[0].toLowerCase();
                     return name.includes(this.headSearch.toLowerCase());
                  });
               },

               selectBodyFont(item){
                  this.site.settings.fontName = item[0];
                  this.site.settings.fontSettings = item[1];

                  //this.$store.builder.generateFont(this.site, index, this.font);
               },
               filteredBodyFont() {
                  return this.bodyFont.filter((item) => {
                     var name = item[0].toLowerCase();
                     return name.includes(this.bodySearch.toLowerCase());
                  });
               },

               styles(){
                  var site = this.site;
                  return this.$store.builder.generateSiteDesign(site);
               },
               generateGoogleFont(fontName, fontSettings){
                  var href = `https://fonts.googleapis.com/css?family=${fontName}:${fontSettings.variants}`;
                  var styles = document.createElement('link');
                  styles.rel = 'stylesheet';
                  styles.type = 'text/css';
                  styles.href = href;
                  document.getElementsByTagName('head')[0].appendChild(styles);
               },
               init(){
                  var $this = this;

                  this.themes.forEach((element, index) => {
                     this.generateGoogleFont(element.config.fontHeadName, element.config.fontHeadSettings);
                     this.generateGoogleFont(element.config.fontName, element.config.fontSettings);
                  });

                  fetch('/assets/googlefonts/api-response-two.json')
                  .then((rsp) => rsp.json())
                  .then((obj) => {
                     this.headFont = Object.entries(obj);
                     this.bodyFont = Object.entries(obj);
                  });
                  

                  this.$watch('site', (value, _v) => {
                       //$this.$dispatch('builder::updatePage', $this.currentPage);
                       // clearTimeout($this.autoSaveTimer);

                       $this.autoSaveTimer = setTimeout(function(){
                           $this.$store.builder.savingState = 0;
                           event = new CustomEvent("builder::saveSite");

                           window.dispatchEvent(event);
                       }, $this.$store.builder.autoSaveDelay);
                   });
               }
             }
          });
      </script>
      @endscript
    </div>
</div>