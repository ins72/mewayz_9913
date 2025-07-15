
<?php

    use function Livewire\Volt\{state, mount, on, placeholder};

    state(['site'])->reactive();
    state([
        'sectionConfig' => fn () => config("bio.sections"),

        'generate_addons' => fn () => collect(_generate_addon_config()),
    ]);
    mount(function(){
        
    });
    on([
    // 'builder::createdSection' => function($section){
    //    $this->getSections();
    // },
    // 'builder::setPage' => function(){
    //    $this->getSections();
    // },
    ]);
    placeholder('
    <div class="p-5 w-[100%] mt-1">
        <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)]"></div>
        <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
        <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
    </div>');
    // Methods
    $getSections = function(){
        // Later NOTE: READ THIS; We can condition the published block from the backend on the live site. Fetch published=1 from backend instead of filtering from frontend to save performance. This is for later.


        // $this->sections = $this->site->getEditSections();
    };
?>

<div>
   <div x-data="builder__generate_site">
         
      <div x-cloak x-show="builderPage=='-'" wire:ignore :style="styles()">

         <div class="buildout--page" :class="sectionClass()" :style="{'--section-image': 'url('+$store.builder.getMedia(site.background !== null && site.background.image)+')'}" wire:ignore>
      
            <div class="relative h-full">
         
               <div class="builder-layout-root-main" :style="{
                  '--link-block-bg-color': site.settings.color,
                  '--general-site-color': site.settings.color,
            }">
                  <div class="builder-layout-root-main-content max-w-[100%] w-[100%]">
                     <div class="builder-layout-root-wrapper mt-0 mb-0">
                        <div class="builder-layout-background">
                           <div class="builder-layout-background-view" :class="{
                              '-no-image': site.background.image && site.background.split_color || site.background.image && site.background.gradient
                           }">
                              <template x-if="site.background.split_color">
                                 <div :style="`background: linear-gradient(${selectedAngle}deg, ${color_1} ${offset_angle}%, ${offset_angle}%, ${color_2} 100%)`" class="split-color w-[100%] h-full"></div>
                              </template>
                              <template x-if="site.background.gradient">
                                 <div :style="backgroundStyle" :class="gradientClass"></div>
                              </template>
                              
                              <template x-if="site.background.gradient || site.background.split_color">
                                 <div class="-transparency" :style="transparency"></div>
                              </template>
                           </div>
                        </div>
         
                        <div class="builder-layout-content max-w-700 mx-auto">
                           <div class="builder-page z-10 relative pb-5" wire:ignore>
      
                              <template x-if="site.settings.tab_style == 'hamburger'">
                                 <div>
                                    <div class="fixed top-[10px] z-[9999] left-[10px] handburger-menu">
                                       <button type="button" class="yena-btn-clean !shadow-none w-[var(--yena-sizes-8)]" :class="{
                                          '!hidden': hamburgerOpen,
                                       }" @click="hamburgerOpen=true">
                                          {!! __i('--ie', 'menu-burger.4', 'h-6 h-6 inline-block flex-shrink-0 text-current align-middle w-[var(--chakra-sizes-3)]') !!}
                                       </button>
                                    </div>

                                    <div>
                                       <div class="yena-sidebar -is-page" :class="{
                                          '!transform !translate-x-0 !translate-y-0': hamburgerOpen,
                                       }">
                                          <div class="yena-sidebar-inner  bg-white">
                                    
                                             <div class="-header-sidebar">
                                                <div class="flex items-center justify-end mb-2">
                                                   <div class="block">
                                                      <button type="button" class="yena-btn-clean w-[var(--yena-sizes-8)]" @click="hamburgerOpen=false">
                                                         <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M4 4L20 20M20 4L4 20L20 4Z" stroke="var(--foreground)" stroke-linecap="square"></path>
                                                         </svg>
                                                      </button>
                                                   </div>
                                                </div>
                                             </div>
                                             
                                             <div class="flex flex-col items-center mt-4 mb-2">
                                                
                                                <template x-for="(page, index) in getPages()">
                                                   <a class="sidebar-item" @click="setPage(page.uuid); hamburgerOpen=false" :class="{
                                                      '--active': currentPage().uuid == page.uuid
                                                   }">
                                                      <div class="--inner">
                                                         <p x-text="page.name"></p>
                                                      </div>
                                                   </a>
                                                </template>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </template>
      
                              <template x-if="getPages().length > 1 && site.settings.tab_style !== 'hamburger'">
                                 <div class="w-[100%] mr-auto block translate-x-[-50%] ml-[50%] !p-0 h-12 max-w-[100%] builder-header" :class="{
                                    'fixed bottom-0 z-[1000] w-[100%]': site.settings.tab_style == 'tabs_on_bottom',
                                    'w-[100%]': site.settings.tab_style == 'tabs_on_top' || !site.settings.tab_style
                                 }">
                                    <div class="overflow-hidden min-h-[48px] flex text-[16px] w-[100%] builder-menu-o">
                                       
                                       <div class="relative inline-block flex-auto whitespace-nowrap overflow-x-auto">
                                          <div class="flex" role="tablist">
         
                                             <template x-for="(page, index) in getPages()">
                                                <a class="normal-case max-h-[75px] min-w-[72px] border-b-4 border-solid border-transparent inline-flex items-center justify-center box-border bg-transparent outline-[0px] border-[0px] m-0 rounded-none cursor-pointer select-none align-middle appearance-none no-underline font-medium text-[0.875rem] leading-tight max-w-[360px] relative min-h-[48px] flex-shrink-0 px-[16px] py-[12px] overflow-hidden whitespace-normal text-center flex-col text-[color:rgba(0,_0,_0,_0.6)] -builder-header-item" :class="{
                                                   'font-medium': currentPage().uuid !== page.uuid,
                                                   'font-semibold': currentPage().uuid == page.uuid,
                                                }" @click="setPage(page.uuid)" x-text="page.name"></a>
                                             </template>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </template>
      
                              <template x-if="currentPage().settings.enableHeader">

                                 <section>
                                    <section class="banners-section">
                                       <template x-for="(banner, index) in banners" :key="index">
                                          <template x-if="site.settings.banner == index">
                                             <div x-bit="'section-banner-' + index"></div>
                                          </template>
                                       </template>
                                    </section>
            
                                    <section>
                                       <template x-if="site.socials && site.socials.length > 0 && !bannerSettings().disable_social">
                                          <div class="page-context-social -new-social context-social px-5 md:px-10">
                                             <x-livewire::components.bio.parts.social />
                                          </div>
                                       </template>
                                    </section>
                                 </section>

                              </template>
      
                              <template x-if="story.length > 0">
                                 <section wire:ignore class="mt-5" :class="{
                                    '!hidden': !currentPage().default,
                                    '!mt-0': bannerSettings().disable_social
                                 }">
                                    <div x-init="generateZuck"></div>
                                    <div class="page-stories-section stories-section px-5 md:px-10">
                                       <div class="display-stories">
                                          <div class="swiper">
                                             <div class="swiper-wrapper wrapper-stories flex overflow-x-auto crazy-zuck-story" x-ref="zuck_story">
                                                <!-- HERE -> ITEMS -> AUTOMATICALLY BY JAVASCRIPT -->
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </section>
                              </template>
      
                           </div>
                        </div>
                     </div>
                  </div>
                  
         
                  <div class="builder-sections-o relative z-[5] pb-10" :class="{
                     'px-0': site.background.width == 'fill',
                     'px-[20px]': site.background.width == 'fit' || !site.background.width,
                     's--radius-straight': site.settings.corners == 'straight',
                     's--radius-round': site.settings.corners == 'round',
                     's--radius-rounded': site.settings.corners == 'rounded',
                  }" :style="{
                        '--link-block-bg-color': site.settings.color,
                  }">
                     <template x-for="(item, index) in window._.sortBy(getSections(), 'position')" :key="item.id">
                        <div class="w-[100%] bio-margin -animated bio-block-wrapper" :id="'block-id-' + item.id" :data-block-id="item.id">
                           <template x-if="item.content.title || item.content.subtitle">
                              <div class="flex">
                                 <div class="bio-titles z-50 flex flex-col gap-2">
                                    <template x-if="item.content.title">
                                       <div class="heading !m-0" x-text="item.content.title"></div>
                                    </template>
                                    <template x-if="item.content.subtitle">
                                       <div class="heading !m-0" x-text="item.content.subtitle"></div>
                                    </template>
                                 </div>
                           </div>
                           </template>
                           <div x-bit="'section-' + item.section" x-data="{section:item}"></div>
                        </div>
                     </template>
                  </div>
               </div>
            </div>
      
      
         </div>
      </div>

      {{-- Generate Javascript Components --}}

      {{-- <div wire:ignore>
            @foreach($generate_addons as $key => $item)
               @php
                  if(!__a($item, 'components.alpineView')) continue;

                  $_name = str_replace('/', '.', __a($item, 'components.alpineView'));
                  $component = "addons::$key.$_name";
                  $livewireComponent = "addons.$key.$_name";
               @endphp
               <div x-show="builderPage=='addon::{{ $key }}' && itemAddon">
                  <div wire:ignore>
                     
                     <livewire:is :component="$livewireComponent" :$site lazy :key="uukey('builder::index', 'builder\component' . $component)">

                     <x-dynamic-component :component="$component"/>
                  </div>
               </div>
            @endforeach
      </div> --}}

   </div>
    
   @script
    <script>
      Alpine.data('builder__generate_site', () => {
         return {
              banners: {!! collect(config('bio.layout-banners'))->toJson() !!},
              hamburgerOpen: false,
              zuck: null,
              zuckOptions: {
                 autoFullScreen: false,
                 skin: 'Snapssenger',
                 avatars: false,
                 list: false,
                 openEffect: true,
                 cubeEffect: true,
                 backButton: false,
                 backNative: false,
                 localStorage: false,
                 paginationArrows: true,
              },
              builderPage: '-',
              itemAddon: null,
              generate_addons: {!! $generate_addons !!},
              bannerSettings(){
               let settings = [];

               if(!this.site.settings.banner) return;

               settings = this.banners[this.site.settings.banner].settings;

               return settings;
              },
              backgroundStyle() {
                  this.$store.bioBackground.site = this.site;
                 return this.$store.bioBackground.backgroundStyle();
              },
               gradientClass() {
                  this.$store.bioBackground.site = this.site;
                 return this.$store.bioBackground.gradientClass();
              },
              get transparency(){
                  let cssTransparency = null;
                  let transparencyAmount = parseFloat(this.site.background.transparency);
                  if (transparencyAmount <= 50) {
                     let transparencyPercentage = transparencyAmount / 50 * 100;
                     let transparency = 250 + (750 - (transparencyPercentage * 7.5));
                     transparency = transparency / 1000;
                     cssTransparency = `background: rgba(0, 0, 0, ${transparency});`;
                  }
                  if (transparencyAmount >= 50) {
                     let transparency = (transparencyAmount - 50) / 50 * 100;
                     transparency = transparency / 100;
                     cssTransparency = `background: rgba(255, 255, 255, ${transparency});`;
                  }

                  return cssTransparency;
              },
              get color_1(){
                  let color = this.site.background.color_1;
                  if(!color) color = '#ffffff';
                  return color;
              },
              get color_2(){
                  let color = this.site.background.color_2;
                  if(!color) color = '#000000';
                  return color;
              },
              get offset_angle(){
                  let angle = this.site.background.split_offset;
                  if(!angle) angle = 50;
                  return angle;
              },
              get selectedAngle(){
                  let angle = this.site.background.split_angle;
                  if(!angle) angle = 45;
                  return angle;
              },
              generateComponent(component){
                  component = `o-section-${component}`;
                  return `<${component}></${component}>`;
              },
              sectionClass: function(){
               return this.$store.bio.generateSectionClass(this.site);
              },
              
              generateAddonPage($detail){
                  var $this = this;
                  
                  var $__detail = {
                     config: this.generate_addons[$detail.addon],
                     ...$detail,
                  };

                  $this.builderPage = "addon::" + $__detail.addon;
                  $this.itemAddon = $__detail;
              },

              currentPage(){
                  var page = this.pages[0];

                  this.pages.forEach((e, index) => {
                     if(e.uuid == this.site.current_edit_page) page = e;
                  });
                  return page;
               },
               getPages(){
                  let pages = [];

                  this.pages.forEach((element, index) => {
                     if(!element.published) return; 
                     pages.push(element);
                  });

                  return pages;
               },
               getSections(){
                  var sections = [];

                  this.sections.forEach((element, index) => {
                     if(this.currentPage().uuid == element.page_id){
                        if(!element.published) return;

                        sections.push(element);
                     }
                  });
                  return sections;
               },

               setPage(page_id){
                  this.site.current_edit_page = page_id;

                  this.$dispatch('setupGalleryo');
               },

               styles(){
                     var site = this.site;
                     return this.$store.bio.generateSiteDesign(site);
               },


               // Generate stories
               generateSingleStoryItem(item){
                  let $this = this;
                  let thumbnail = item.thumbnail ? item.thumbnail : $this.site.logo;
                  var media = $this.$store.builder.getMedia(thumbnail);
                  var link = item.link;
                  var linkText = `View`;
                  return {
                     id: `box-${item.uuid}`,
                     photo: $this.$store.builder.getMedia($this.site.logo),
                     name: item.name,
                     link: link,
                     time: new Date(item.updated_at).getTime() / 1000,
                     items: [
                        {
                           id: item.uuid,
                           type: 'photo',
                           length: 5,
                           src: media,
                           preview: media,
                           link: link,
                           linkText: linkText,
                           time: new Date(item.updated_at).getTime() / 1000,
                           seen: false,
                        }
                     ]
                  };
               },

               generateZuckStory(){
                  var $this = this;
                  var $return = [];
                  var stories = window._.sortBy(this.story, 'position');
                  stories.forEach(item => {
                     $return.push($this.generateSingleStoryItem(item));
                  });

                  return $return;
               },

               generateZuck(){
                  let $this = this;
                  let $stories = this.generateZuckStory();
                  let $zuckEl = $this.$root.querySelector('.crazy-zuck-story');
                  $zuckEl.innerHTML = '';



                  $this.$watch('story', (value) => {
                     $stories = $this.generateZuckStory();
                     $zuckEl.innerHTML = '';
                        
                     $this.zuck = Zuck($zuckEl, {
                        ...$this.zuckOptions,
                        stories: $stories,
                     });
                  });
                  
                  $this.zuck = Zuck($zuckEl, {
                     ...$this.zuckOptions,
                     stories: $stories,
                  });
               },

               init(){
                  var $this = this;
                  // $this.generateZuck();
                  
                  window.addEventListener('setAddonPage', (event) => {
                     var $detail = event.detail;
                     
                     $this.generateAddonPage($detail);
                  });

                  window.addEventListener('addon::item', (event) => {
                     var $detail = event.detail;
                     
                     $this.itemAddon = $detail;
                  });
                  
                  window.addEventListener('addon::clearItem', (event) => {
                     var $detail = event.detail;
                        
                     $this.builderPage = "-";
                     $this.itemAddon = null;
                  });
               }
         }
      });
   </script>
   @endscript
</div>
