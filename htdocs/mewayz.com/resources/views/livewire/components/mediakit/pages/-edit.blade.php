
<?php

use App\Models\BioSection;
use App\Models\BioSectionItem;
use function Livewire\Volt\{state, mount, placeholder, updated, on};

state(['site']);
on([
   
]);

updated([
   
]);

mount(fn() => '');

placeholder('
   <div class="w-[100%] p-5 mt-1">
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>
');

$createSection = function($section){
   $this->skipRender();

   $_section = new BioSection;
   $_section->fill($section);
   $_section->site_id = $this->site->id;
   $_section->page_id = $this->site->getEditingPage();
   $_section->published = 1;
   $_section->uuid = __a($section, 'uuid');
   $_section->save();

   if(is_array($items = __a($section, 'items'))){
      foreach ($items as $key => $value) {
           $_item = new BioSectionItem;
           $_item->fill($value);
           $_item->section_id = $_section->uuid;
           $_item->uuid = __a($value, 'uuid');
           $_item->save();
      }
   }

   $this->js('$store.builder.savingState = 2');
   // $this->dispatch('builder::createdSection', $_section);
};
?>

<div class="website-section --create-section">

   <div x-data="builder__edit_page" wire:ignore>
      <div class="design-navbar">
         <ul >
            <li class="close-header">
                <a @click="closePage()">
                    <span>
                        {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                    </span>
                </a>
            </li>
            <li>{{ __('Mediakit') }}</li>
            <li class="!flex md:!hidden">
                <a @click="closeSection()" class="menu--icon !bg-black !text-[10px] !text-white !w-auto !px-2 !h-5 cursor-pointer">
                    {{ __('Close') }}
                </a>
            </li>
         </ul>
      </div>
      <div class="container-small ![overflow:initial] lg:!overflow-y-auto">
         <div class="mt-2 all-pages-style">
            <ul>
               <div class="flex flex-col gap-4">
                  <a class="yena-black-btn mt-1 !justify-center gap-2"><i class="ph ph-plus"></i> {{ __('Add Block') }}</a>

                  <div>
                      <div class="pb-[12px]">
                          <span class="text-[#4F4F4F] text-[13px] font-medium">
                           {{ __('Header Section') }}
                          </span>
                      </div>

                      <div class="grid gap-y-[12px] gap-x-[12px] grid-cols-1">
                        <div class="flex px-[10px] py-[10px] relative [transition:box-shadow_0.25s_ease-out] items-center rounded-[10px] justify-between bg-[#F7F7F7] cursor-pointer hover:bg-[rgb(255,_255,_255)] hover:[box-shadow:rgba(0,_0,_0,_0.2)_0px_8px_20px]">
                            <div class="flex items-center">
                                <div class="shadow-xl rounded-[10px] w-[30px] h-[30px] ml-[10px] mr-[15px] my-[0] flex items-center justify-center bg-white">
                                 {!! __i('--ie', 'user-circle.1', 'w-5 h-5') !!}
                                </div>

                                <span class="text-[12px] font-semibold pl-[8px]">
                                 {{ __('Header') }}
                                </span>
                            </div>

                            <div class="flex items-center h-full">

                             <div>
                                {!! __i('--ie', 'eye.5', 'w-4 h-4') !!}
                             </div>
                             <div class="ml-3 mr-2 w-[1px] bg-gray-300 h-full block"></div>
                             <div class="w-[22px] h-[22px] flex items-center rounded-[5px] justify-center bg-[#FFFFFF] [transition:background-color_0.2s_ease-out]">
                              <i class="ph ph-caret-right text-xs"></i>
                             </div>
                            </div>
                        </div>
                        <div class="flex px-[10px] py-[10px] relative [transition:box-shadow_0.25s_ease-out] items-center rounded-[10px] justify-between bg-[#F7F7F7] cursor-pointer hover:bg-[rgb(255,_255,_255)] hover:[box-shadow:rgba(0,_0,_0,_0.2)_0px_8px_20px]">
                            <div class="flex items-center">
                              <div class="shadow-xl rounded-[10px] w-[30px] h-[30px] ml-[10px] mr-[15px] my-[0] flex items-center justify-center bg-white">
                               {!! __i('emails', 'email-cursor-square', 'w-5 h-5') !!}
                              </div>

                                <span class="text-[12px] font-semibold pl-[8px]">
                                 {{ __('Contact form') }}
                                </span>
                            </div>

                            <div class="flex items-center h-full">

                             <div>
                                {!! __i('--ie', 'eye.5', 'w-4 h-4') !!}
                             </div>
                             <div class="ml-3 mr-2 w-[1px] bg-gray-300 h-full block"></div>
                             <div class="w-[22px] h-[22px] flex items-center rounded-[5px] justify-center bg-[#FFFFFF] [transition:background-color_0.2s_ease-out]">
                              <i class="ph ph-caret-right text-xs"></i>
                             </div>
                            </div>
                        </div>
                        <div class="flex px-[10px] py-[10px] relative [transition:box-shadow_0.25s_ease-out] items-center rounded-[10px] justify-between bg-[#F7F7F7] cursor-pointer hover:bg-[rgb(255,_255,_255)] hover:[box-shadow:rgba(0,_0,_0,_0.2)_0px_8px_20px]">
                            <div class="flex items-center">
                              <div class="shadow-xl rounded-[10px] w-[30px] h-[30px] ml-[10px] mr-[15px] my-[0] flex items-center justify-center bg-white">
                               {!! __i('Health', 'user-heart-health', 'w-5 h-5') !!}
                              </div>

                                <span class="text-[12px] font-semibold pl-[8px]">
                                 {{ __('Total Followers') }}
                                </span>
                            </div>

                            <div class="flex items-center h-full">

                             <div>
                                {!! __i('--ie', 'eye.5', 'w-4 h-4') !!}
                             </div>
                             <div class="ml-3 mr-2 w-[1px] bg-gray-300 h-full block"></div>
                             <div class="w-[22px] h-[22px] flex items-center rounded-[5px] justify-center bg-[#FFFFFF] [transition:background-color_0.2s_ease-out]">
                              <i class="ph ph-caret-right text-xs"></i>
                             </div>
                            </div>
                        </div>
                      </div>
                  </div>
               </div>
            </ul>
         </div>
      </div>
   </div>
   
    @script
    <script>
       Alpine.data('builder__edit_page', () => {
          return {
             page_name: '',
 
             init(){
               
             }
          }
       });
    </script>
    @endscript
</div>