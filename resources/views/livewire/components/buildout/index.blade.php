
<?php

use function Livewire\Volt\{state, mount, on, placeholder};

state(['site']);
state([
   'sections' => [],
   'banners' => fn () => config('yena.layout-banners'),
]);
mount(function(){
   $this->getSections();

   $this->site = $this->site->toArray();
});
on([
   'builder::createdSection' => function($section){
      $this->getSections();
   },
   'builder::setPage' => function(){
      $this->getSections();
   },
]);
placeholder('
   <div class="p-5 w-full mt-1">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');
// Methods
$getSections = function(){
   $this->sections = $this->site->getEditSections();
};
?>

<div class="-mobile-plane" x-data="builder_generate_site" :class="{'active': renderMobile}">
   <div class="buildout--page">
   
      <div class="relative h-full">
   
         <div class="builder-layout-root-main">
            <div class="builder-layout-root-main-content max-w-full w-full">
               <div class="builder-layout-root-wrapper mt-0 mb-0">
                  <div class="builder-layout-background">
                     <!-- Background -->
                     
                  </div>
   
                  <div class="builder-layout-content max-w-700 mx-auto">
                     <div class="builder-page z-10 relative pb-5">
                        @foreach ($banners as $key => $item)
                           @php
                              $component = "livewire::components.buildout.banner.--$key-banner";
                           @endphp
                           <template x-if="site.settings.banner == '{{ $key }}'">
                              <x-dynamic-component :component="$component"/>
                           </template>
                        @endforeach
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      
   </div>

{{--     
    @foreach ($sections as $item)
        @php
            $config = $item->getConfig();
            $component = ao($config, 'components.viewComponent');
        @endphp
        
        
        <div >
            <div @click="navigatePage('sectionComponent:{{ $item->id }}')">
                <livewire:is :component="$component" lazy :section="$item" :key="uukey()" />
            </div>


            <div class="flex items-center justify-center">
                <a class="btn !w-7 !h-7 !rounded-full" @click="navigatePage('section')">
                    <i class="fi fi-rr-plus text-white text-[10px]"></i>
                </a>
            </div>
        </div>
    @endforeach --}}






    
    @script
    <script>
        Alpine.data('builder_generate_site', () => {
           return {
              site: @entangle('site'),
              logo: null,
              banner: null,
              init(){
                 var $this = this;
                 var $eventID = 'site::changed';

                  window.addEventListener($eventID, (event) => {
                     $this.site = event.detail;
                  });
                  window.addEventListener("site::logo", (event) => {
                     this.logo = event.detail;
                  });
                  window.addEventListener("site::banner", (event) => {
                     this.banner = event.detail;
                  });
              }
           }
        });
    </script>
   @endscript
</div>
