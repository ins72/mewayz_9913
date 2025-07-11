
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

   placeholder('placeholders.console.builder.sidebar-design');

?>

<div>

   <div x-data="builder__design" :style="styles()">

      <div x-show="__page == 'fonts'">
          <div>
                     
               <div class="settings-section section">
                  <div class="settings-section-content">
            
                     <div class="top-bar">
                        <div class="page-settings-navbar">
                           <ul >
                              <li class="close-header !flex">
                                 <a @click="__page='-'">
                                    <span>
                                       {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                                    </span>
                                 </a>
                           </li>
                           <li class="!pl-0">{{ __('Fonts') }}</li>
                           <li></li>
                           </ul>
                     </div>
                     <div class="sticky container-small">
                           <div class="tab-link">
                              <ul class="tabs">
                              <li class="tab !w-full" @click="_tab = 'title'" :class="{'active': _tab == 'title'}">{{ __('Title') }}</li>
                              <li class="tab !w-full" @click="_tab = 'body'" :class="{'active': _tab == 'body'}">{{ __('Body') }}</li>
                              </ul>
                           </div>
                     </div>
                     </div>
                     <div class="container-small tab-content-box">
                        <div class="tab-content">
                              <div x-cloak :class="{'active': _tab == 'title'}" data-tab-content>
                                 
                                 <div class="w-[100%] py-0 px-[var(--s-2)] mt-2">
                                    <div class="search-font">
                                       <form>
                                          <div class="input-box search-box">
                                             <input type="text" class="input-large search-input" x-model="headSearch" placeholder="{{ __('Search...') }}">
                                             <div class="input-icon zoom-icon">
                                                {!! __i('--ie', 'search.1', 'w-5 h-5') !!}
                                             </div>
                                          </div>
                                       </form>
                                    </div>
                                    <div class="font-list">
                                       <ul>
                                          
                                          <li class="heading-body active" :class="{'!hidden': !site.settings.fontHeadName}">
                                             <span x-text="site.settings.fontHeadName"></span>
                                             <span>
                                                {!! __i('--ie', 'done-check.3', 'w-5 h-5') !!}
                                             </span>
                                          </li>

                                          <template x-for="(item, index) in filteredHeadFont()" :key="index">
                                             <li class="heading-body" x-text="item[0]" @click="selectHeadFont(item)"></li>
                                          </template>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
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
                                    {{-- <div class="font-options !hidden">
                                       <div class="input-box">
                                          <div class="input-label">Style</div>
                                          <div class="input-group">
                                             <select>
                                                <option value="100" style="color: rgb(0, 0, 0);">Thin</option>
                                                <option value="100italic" style="color: rgb(0, 0, 0);">Thin Italic</option>
                                                <option value="200" style="color: rgb(0, 0, 0);">Extra Light</option>
                                                <option value="200italic" style="color: rgb(0, 0, 0);">Extra Light Italic</option>
                                                <option value="300" style="color: rgb(0, 0, 0);">Light</option>
                                                <option value="300italic" style="color: rgb(0, 0, 0);">Light Italic</option>
                                                <option value="regular" style="color: rgb(0, 0, 0);">Regular</option>
                                                <option value="italic" style="color: rgb(0, 0, 0);">Italic</option>
                                                <option value="500" style="color: rgb(0, 0, 0);">Medium</option>
                                                <option value="500italic" style="color: rgb(0, 0, 0);">Medium Italic</option>
                                                <option value="600" style="color: rgb(0, 0, 0);">SemiBold</option>
                                                <option value="600italic" style="color: rgb(0, 0, 0);">SemiBold Italic</option>
                                                <option value="700" style="color: rgb(0, 0, 0);">Bold</option>
                                                <option value="700italic" style="color: rgb(0, 0, 0);">Bold Italic</option>
                                                <option value="900" style="color: rgb(0, 0, 0);">Black</option>
                                                <option value="900italic" style="color: rgb(0, 0, 0);">Black Italic</option>
                                             </select>
                                          </div>
                                       </div>
                                    </div> --}}
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
                  <div class="page-settings-navbar">
                     <ul >
                         <li class="close-header">
                         <a @click="closePage('pages')">
                             <span>
                                 {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                             </span>
                         </a>
                     </li>
                     <li>{{ __('Design') }}</li>
                     <li></li>
                     </ul>
                 </div>
                 <div class="sticky container-small">
                     <div class="tab-link">
                         <ul class="tabs">
                         <li class="tab !w-full" @click="__tab = 'themes'" :class="{'active': __tab == 'themes'}">{{ __('Themes') }}</li>
                         <li class="tab !w-full" @click="__tab = 'custom'" :class="{'active': __tab == 'custom'}">{{ __('Custom') }}</li>
                         </ul>
                     </div>
                 </div>
                </div>
                <div class="container-small tab-content-box">
                    <div class="tab-content">
                        <div x-cloak :class="{'active': __tab == 'themes'}" data-tab-content>
                           <div class="w-[100%] py-0 px-[var(--s-2)] mt-[var(--s-2)]">
                              <div class="theme-list">
                                 <template x-for="(item, index) in themes" :key="index">
                                    <div :class="{'active !bg-[var(--c-mix-10)]': site.settings.currentTheme == item.id}" class="!rounded-lg !bg-[#f1f1f1] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),0_2px_4px_-1px_rgba(0,_0,_0,_0.06)]" @click="setTheme(item)">
                                       <div class="theme-text !p-0 !mb-0 !rounded-lg overflow-hidden [box-shadow:0_0.25em_0.375em_-0.0625em_rgba(0,_0,_0,_0.1),_0_0.125em_0.25em_-0.0625em_rgba(0,_0,_0,_0.06),_0_0_0_0.0625em_rgb(255_255_255_/_64%)]">
                                             <div class="grid [grid-template:'accent'_minmax(2.5em,_auto)_'body'_/_1fr] overflow-hidden flex-1 pointer-events-none" :style="{
                                                'background': '#' + item.config.color,
                                             }"></div>
                                          <div class="p-[var(--s-1)]">
                                             <h1 class="!text-[1.6em]" :style="{
                                                'font-family': item.config.fontHeadName
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
                           <div class="w-[100%] px-[var(--s-2)] py-[0] mt-[var(--s-2)]">
                              <div class="colors-container">
                                 <div class="input-box !pb-1">
                                    <div class="input-label">{{ __('Color') }}</div>
                                    <div class="input-group">
                                       <div class="color-selector">

                                          @foreach ($colors as $item)
                                          <div class="color-box mod" @click="site.settings.color='{{ $item }}'" style="--c: #{{ $item }};"><span style="--c: #{{ $item }}; background: #{{ $item }}"></span></div>
                                          @endforeach
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
                                             
                                 <div class="flex mb-1">
                                    <div class="menu--icon !bg-black !text-[10px] !text-white !w-auto !px-2 !h-5 !cursor-pointer !flex !items-center !justify-center !ml-auto !gap-1 !rounded-none !rounded-b-md" @click="randomHex">
                                       <i class="fi fi-br-dice-alt"></i>
                                       {{ __('Random') }}
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
                              <div class="shapes-container">
                                 <form onsubmit="return false;">
                                    <div class="btn-radius">
                                       <div class="input-box">
                                          <div class="input-label">{{ __('Corner') }}</div>
                                          <div class="input-group">
                                             <button class="btn btn-nav" type="button" @click="site.settings.corner = 'straight'" :class="{'active': site.settings.corner == 'straight'}">
                                                <img src="{{ gs("assets/image/others/corner-straight.png") }}" class="w-7 h-7 -mt-[9px] -ml-[11px]" alt="">
                                             </button>
                                             <button class="btn btn-nav" type="button" @click="site.settings.corner = 'round'" :class="{'active': site.settings.corner == 'round'}">
                                                <img src="{{ gs("assets/image/others/corner-round.png") }}" class="w-7 h-7 -mt-[9px] -ml-[11px]" alt="">
                                             </button>
                                             <button class="btn btn-nav" type="button" @click="site.settings.corner = 'rounded'" :class="{'active': site.settings.corner == 'rounded'}">
                                                <img src="{{ gs("assets/image/others/corner-rounded.png") }}" class="w-7 h-7 -mt-[9px] -ml-[11px]" alt="">
                                             </button>
                                          </div>
                                       </div>
                                       <div class="input-box mt-2">
                                          <div class="input-label">{{ __('Mode') }}</div>
                                          <div class="input-group">
                                             <button class="btn btn-nav" type="button" @click="site.settings.siteTheme = '-'" :class="{'active': site.settings.siteTheme == '-' || !site.settings.siteTheme}">
                                                <i class="ph ph-prohibit text-lg"></i>
                                             </button>
                                             <button class="btn btn-nav" type="button" @click="site.settings.siteTheme = 'light'" :class="{'active': site.settings.siteTheme == 'light'}">
                                                <i class="ph ph-sun text-lg"></i>
                                             </button>
                                             <button class="btn btn-nav" type="button" @click="site.settings.siteTheme = 'dark'" :class="{'active': site.settings.siteTheme == 'dark'}">
                                                <i class="ph ph-moon-stars text-lg"></i>
                                             </button>
                                          </div>
                                       </div>
                                    </div>
                                 </form>
                              </div>
                              <div class="mt-2 layout-container">
                                 <div class="input-box">
                                    <div class="input-label">{{ __('Page width') }}</div>
                                    <div class="input-group">
                                       <input type="range" class="input-small range-slider" min="650" max="1200" x-model="site.settings.page_width" step="10">
                                       <p class="space-size-value" x-text="site.settings.page_width + 'px'"></p>
                                    </div>
                                 </div>
                              </div>
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
               _tab: 'title',
               __page: @entangle('__page'),
               autoSaveTimer: null,
               fonts: [],
               font: [],

               bodyFont: [],
               bodySearch: '',

               headFont: [],
               headSearch: '',

               themes: {!! collect(config('yena.themes')) !!},
               randomHex(){
                  let $hex = this.$store.builder.getRandomHexColor();

                  $hex = $hex.replace(new RegExp('#', 'g'), '');
                  site.settings.color = $hex;
               },
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