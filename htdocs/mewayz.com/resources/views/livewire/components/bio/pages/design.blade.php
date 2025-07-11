
<?php

use App\Models\SiteSocial;
use function Livewire\Volt\{state, mount, placeholder, updated, on, usesFileUploads};

usesFileUploads();
state(['site']);
on([
   
]);

state([
   'sections' => fn () => [],
   'logo' => null,
   'banner' => null,

   'banners' => fn () => config('bio.layout-banners'),
]);

placeholder('
   <div class="p-5 w-[100%] mt-1">
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>
');

// Methods

$createSocial = function($item){
    $_item = new SiteSocial;
    $_item->fill($item);
    $_item->site_id = $this->site->id;
    $_item->save();
};

$deleteSocial = function($id){
    if(!$_social = SiteSocial::where('uuid', $id)->where('site_id', $this->site->id)->delete()) return;
};

?>

<div>

   <div class="flex flex-col" x-data="builder__design">
      <div class="w-[100%] flex relative flex-shrink-0 flex-col justify-between">
         <div>
            <ul class="flex items-center [box-shadow:inset_0_-1px_0_var(--c-mix-1)]">
               <div class="text-[#f9f9f9] w-[100%] h-[calc(var(--unit)_*_5)] p-0 bg-transparent rounded-none [transition:background-color_150ms_cubic-bezier(0.4,_0,_0.2,_1)_0ms] flex relative text-left items-center cursor-pointer justify-start no-underline hover:bg-[#F7F7F7]" :class="{'!bg-[#F7F7F7]': __page=='-'}" @click="__page='-'">
                  <div class="w-[100%] flex items-center pl-[17px] justify-center">
                     <div class="text-[#BDBDBD] inline-flex min-w-[25px] flex-shrink-0">
                        {!! __i('interface-essential', 'user-profile', 'w-[20px] h-[20px] flex items-center') !!}
                     </div>
                     <div class="text-[#3a3a3a] text-xs font-medium leading-[24px] tracking-[0] [0] mt-[4px] mb-[4px]">
                        {{ __('About') }}
                     </div>
                  </div>
               </div>
               <div class="text-[#f9f9f9] w-[100%] h-[calc(var(--unit)_*_5)] p-0 bg-transparent rounded-none [transition:background-color_150ms_cubic-bezier(0.4,_0,_0.2,_1)_0ms] flex relative text-left items-center cursor-pointer justify-start no-underline hover:bg-[#F7F7F7]" :class="{'!bg-[#F7F7F7]': __page=='layout'}" @click="__page='layout'">
                  <div class="w-[100%] flex items-center pl-[17px] justify-center">
                     <div class="text-[#BDBDBD] inline-flex min-w-[25px] flex-shrink-0">
                        {!! __i('interface-essential', 'grid-layout.14', 'w-[20px] h-[20px] flex items-center') !!}
                     </div>
                     <div class="text-[#3a3a3a] text-xs font-medium leading-[24px] tracking-[0] [0] mt-[4px] mb-[4px]">
                        {{ __('Layout') }}
                     </div>
                  </div>
               </div>
               <div class="text-[#f9f9f9] w-[100%] h-[calc(var(--unit)_*_5)] p-0 bg-transparent rounded-none [transition:background-color_150ms_cubic-bezier(0.4,_0,_0.2,_1)_0ms] flex relative text-left items-center cursor-pointer justify-start no-underline hover:bg-[#F7F7F7]" :class="{'!bg-[#F7F7F7]': __page=='social'}" @click="__page='social'">
                  <div class="w-[100%] flex items-center pl-[17px] justify-center">
                     <div class="text-[#BDBDBD] inline-flex min-w-[25px] flex-shrink-0">
                        {!! __i('Social Medias Rewards Rating', 'hashtag-shine', 'w-[20px] h-[20px] flex items-center') !!}
                     </div>
                     <div class="text-[#3a3a3a] text-xs font-medium leading-[24px] tracking-[0] [0] mt-[4px] mb-[4px]">
                        {{ __('Social') }}
                     </div>
                  </div>
               </div>
               <div class="text-[#f9f9f9] w-[100%] h-[calc(var(--unit)_*_5)] p-0 bg-transparent rounded-none [transition:background-color_150ms_cubic-bezier(0.4,_0,_0.2,_1)_0ms] flex relative text-left items-center cursor-pointer justify-start no-underline hover:bg-[#F7F7F7]" :class="{'!bg-[#F7F7F7]': __page=='appearance'}" @click="__page='appearance'">
                  <div class="w-[100%] flex items-center pl-[17px] justify-center">
                     <div class="text-[#BDBDBD] inline-flex min-w-[25px] flex-shrink-0">
                        {!! __i('Design Tools', 'color-palette', 'w-[20px] h-[20px] flex items-center') !!}
                     </div>
                     <div class="text-[#3a3a3a] text-xs font-medium leading-[24px] tracking-[0] [0] mt-[4px] mb-[4px]">
                        {{ __('Appearance') }}
                     </div>
                  </div>
               </div>
            </ul>
         </div>
      </div>

      <div class="w-[100%] h-full overflow-y-auto max-h-[calc(100vh_-_130px)] flex relative flex-col">
         <template x-if="__page=='-'">
            <x-livewire::components.bio.parts.design.about />
         </template>
         <template x-if="__page=='layout'">
            <x-livewire::components.bio.parts.design.layout :$banners />
         </template>
         <div x-show="__page=='appearance'">
            <livewire:components.bio.parts.design.appearance lazy :$site :key="uukey('builder', 'builder-design-appearance')" />
         </div>
         <div x-show="__page=='social'">
            <x-livewire::components.bio.parts.design.social />
         </div>
         {{-- <template x-if="__page=='font'">
            <x-livewire::components.builder.parts.design.fonts :$banner :$banners />
         </template> --}}
      </div>
   </div>
   
  @script
  <script>
      Alpine.data('builder__design', () => {
         return {
            autoSaveTimer: null,
            // __tab: 'content',
            __page: '-',

            __banners: {!! collect(config('yena.banners'))->toJson() !!},
            __bannerConfig: [],

            styles(){
               var site = this.site;
               return this.$store.builder.generateSiteDesign(site);
            },
            getBannerConfig(){
                //this.__bannerConfig = this.__banners[this.section.settings.banner_style];
            },

            changeBanner(){

            },


            _save(){
                var $this = this;

               //  $this.$dispatch($eventID, $this.section);
                clearTimeout($this.autoSaveTimer);

                $this.autoSaveTimer = setTimeout(function(){
                    $this.$store.builder.savingState = 0;
                    event = new CustomEvent("builder::saveSite");

                    window.dispatchEvent(event);
                }, $this.$store.builder.autoSaveDelay);
            },
            init(){
               var $this = this;
               window.addEventListener("sectionMediaEvent:logo", (event) => {
                   $this.site.logo = event.detail.image;
                   $this._save();
               });
               window.addEventListener("sectionMediaEvent:banner", (event) => {
                   $this.site.banner = event.detail.image;
                   $this._save();
               });
               window.addEventListener("appearanceBackgroundBg", (event) => {
                   $this.site.background.image = event.detail.image;
                   $this._save();
               });

               // this.$watch('siteheader.links' , (value, _v) => {
               //  $this.$dispatch('section::header', $this.siteheader.links);
               //  clearTimeout($this.autoSaveTimer);

               //  $this.autoSaveTimer = setTimeout(function(){
               //      $this.$store.builder.savingState = 0;
               //      event = new CustomEvent("builder::saveHeaderLinks", {
               //          detail: {
               //              links: $this.siteheader.links,
               //              js: '$store.builder.savingState = 2',
               //          }
               //      });

               //      window.dispatchEvent(event);
               //  }, $this.$store.builder.autoSaveDelay);
               // });
               // $this.getBannerConfig();
            }
         }
      });
  </script>
  @endscript
</div>