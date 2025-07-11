
<?php

   use App\Models\BioSitesLinkerTrack;
   use App\Models\MySession;
   use function Livewire\Volt\{state, mount, placeholder};

   placeholder('
   <div class="p-5 w-[100%] mt-1">
      <div class="--placeholder-skeleton w-[100%] h-[100px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-[100%] h-[100px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-[100%] h-[100px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');


   state(['site']);

   state([
      'visitors' => [],
      'live' => [],
      'linksVisit' => [],
   ]);

   mount(function(){
      if(__o_feature('feature.insight')){
         $this->visitors = $this->site->getInsight();
         $this->live = MySession::activity(10)->where('page_id', $this->site->id)->limit(5)->get();
         $this->linksVisit = (new BioSitesLinkerTrack)->totalVisits($this->site);
      }
   });
?>

<div>
   <div x-data="builder__page_insight">
      {{-- <div x-show="__page == 'views'">
         <div>
            <livewire:components.bio.parts.insight.views lazy :$site :key="uukey('builder', 'insight-page-views')">
         </div>
     </div> --}}
     
    <div x-show="__page == 'views'">
      <div>
         <x-livewire::components.builder.parts.insight.views :$visitors />
      </div>
    </div>
    <div x-show="__page == 'clicks'">
      <div>
         <livewire:components.bio.parts.insight.clicks lazy :$site :$linksVisit :key="uukey('builder', 'insight-page-clicks')">
      </div>
    </div>
    <div x-show="__page == 'live'">
      <div>
         <livewire:components.bio.parts.insight.live lazy :$site :$linksVisit :key="uukey('builder', 'insight-page-live')">
      </div>
    </div>
    <div x-cloak x-show="__page == '-'">
     <div class="website-section">
        <div class="design-navbar">
           <ul >
              <li class="close-header">
                  <a @click="closePage('pages')">
                     <span>
                        {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                     </span>
                  </a>
              </li>
              <li >{{ __('Insight') }}</li>
              <li ><button class="btn btn-save close-edit-settings" @click="closePage('-')">{{ __('Done') }}</button></li>
           </ul>
        </div>
  
  
        @if (!__o_feature('feature.insight'))
        <div class="flex flex-col justify-center items-center px-[20px] py-[60px]">
            {!! __i('Business, Products', 'blackboard-business-chart', 'w-14 h-14') !!}
               <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                  {!! __t('Upgrade your site plan to see site views, visitors & insight.') !!}
               </p>
            <button type="button" @click="$dispatch('open-modal', 'upgrade-modal')" class="btn btn-large mt-3 !h-[40px] !border-none !transition-none">{{ __('Upgrade') }}</button>
         </div>
        @endif
  
      
        <div class="px-[var(--s-2)] py-[0] mt-[var(--s-2)] {{ !__o_feature('feature.insight') ? 'hidden' : '' }}">
           <div class="details-item w-[100%] p-3 md:p-5 z[box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] mb-2 rounded-md !bg-white [border:1px_solid_var(--c-mix-1)] !w-full">
               <div class="details-head">
                   <div class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                       {!! __i('Business, Products', 'Business, Chart.24', 'w-5 h-5') !!}
                   </div>
                   <div class="details-text caption-sm text-xs md:text-base">{{ __('Total Views') }}</div>
               </div>
               <div class="details-counter text-2xl md:text-4xl truncate font-bold truncate">{{ number_format(ao($visitors, 'getviews.visits')) }}</div>
  
               <a class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[2rem] [2.5rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] mt-1" @click="__page='views'">{{ __('View') }}</a>
           </div>
           <div class="details-item w-[100%] p-3 md:p-5 z[box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] mb-2 rounded-md !bg-white [border:1px_solid_var(--c-mix-1)] !w-full">
               <div class="details-head">
                   <div class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                       {!! __i('Cursor Select Hand', 'Cursor, Select, Hand, Click', 'w-5 h-5') !!}
                   </div>
                   <div class="details-text caption-sm text-xs md:text-base">{{ __('Total Clicks') }}</div>
               </div>
               <div class="details-counter text-2xl md:text-4xl truncate font-bold truncate">{{ ao($linksVisit, 'visits') }}</div>
  
  
               <a class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[2rem] [2.5rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] mt-1" @click="__page='clicks'">{{ __('View') }}</a>
           </div>
  
           
           <div class="details-item w-[100%] p-3 md:p-5 z[box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] mb-2 rounded-md !bg-white [border:1px_solid_var(--c-mix-1)] !w-full">
              <div class="details-head">
                  <div class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                      {!! __i('Payments Finance', 'chart-rates-clock', 'w-5 h-5') !!}
                  </div>
                  <div class="details-text caption-sm text-xs md:text-base">{{ __('Live Visitors') }}</div>
              </div>
              <div class="details-counter text-2xl md:text-4xl truncate font-bold truncate">{{ \App\Models\MySession::activity(10)->where('page_id', $site->id)->count() }}</div>
  
              <a class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[2rem] [2.5rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] mt-1" @click="__page='live'">{{ __('View') }}</a>
          </div>
          
        </div>
     </div>
    </div>
   </div>
   
   @script
      <script>
         Alpine.data('builder__page_insight', () => {
            return {
               __page: '-',
            }
         });
      </script>
   @endscript
</div>