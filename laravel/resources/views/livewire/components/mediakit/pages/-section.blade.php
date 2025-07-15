
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

   <div x-data="builder__new_section" wire:ignore>
      <div class="design-navbar">
         <ul >
            <li class="close-header">
                <a @click="closePage()">
                    <span>
                        {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                    </span>
                </a>
            </li>
            <li>{{ __('Sections') }}</li>
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
                   <template x-for="(value, key) in blocks" :key="key">
                       <div>
                           <div class="pb-[12px]">
                               <span class="text-[#4F4F4F] text-[13px] font-medium" x-text="value.name"></span>
                           </div>

                           <div class="grid gap-y-[12px] gap-x-[12px] grid-cols-1">
                               <template x-for="(item, index) in value.items" :key="index">
                                   <div class="flex px-[10px] py-[5px] relative [transition:box-shadow_0.25s_ease-out] items-center rounded-[10px] justify-between bg-[#F7F7F7] cursor-pointer hover:bg-[rgb(255,_255,_255)] hover:[box-shadow:rgba(0,_0,_0,_0.2)_0px_8px_20px]" @click="createSection(index, item)" :class="{
                                       'opacity-40': !item.function || !item.completed
                                   }">
                                       <div class="flex items-center">
                                           <div class="shadow-xl rounded-[10px] w-[30px] h-[30px] ml-[10px] mr-[15px] my-[0] flex items-center justify-center" :style="{
                                               'background': item.color,
                                               'color': $store.builder.getContrastColor(item.color),
                                             }" x-html="item['ori-icon-svg']"></div>

                                           <span class="text-[12px] font-semibold pl-[8px]" x-text="item.name"></span>
                                       </div>

                                       <div class="w-[22px] h-[22px] flex items-center rounded-[5px] justify-center bg-[#FFFFFF] [transition:background-color_0.2s_ease-out]">
                                           <i class="fi fi-rr-plus text-xs"></i>
                                       </div>
                                   </div>
                               </template>
                           </div>
                       </div>
                   </template>
               </div>
            </ul>
         </div>
      </div>
   </div>
   
    @script
    <script>
       Alpine.data('builder__new_section', () => {
          return {
             page_name: '',
             _section: null,
             blocks: {!! collect(generate_section())->toJson() !!},
             currentPage(){
               var page = this.pages[0];

               this.pages.forEach((e, index) => {
                  if(e.uuid == this.site.current_edit_page) page = e;
               });
               return page;
             },
 
             createSection(section, $item){
                let $this = this;
                let $items = [];
                let count = 0;
                this.sections.forEach((s) => {
                  if(this.currentPage().uuid == s.page_id){
                     count++;
                  }
                });

                if($item.function.create.items.length > 0){
                    $item.function.create.items.forEach((_i, index) => {
                        let $newItem = {
                            uuid:  $this.$store.builder.generateUUID(),
                            ..._i
                        };

                        $items.push($newItem);
                    });

                    $item.function.create.items = $items;
                }

                let $section = {
                    uuid: $this.$store.builder.generateUUID(),
                    section: section,
                    page_id: $this.currentPage().uuid,
                    published: 1,
                    position: count,
                    settings: {
                     silence: 'golden',
                    },
                    section_settings: {
                     height: 'fit',
                     width: 'fill',
                     spacing: 'l',
                     align: 'center',
                    },
                    form: {
                     email: '{{__('Email')}}',
                     button_name: '{{__('Signup')}}',
                    },
                    ...$item.function.create,
                };

                $this.$store.builder.savingState = 0;
                if(!$this._create_position){
                  $this.sections.push(JSON.parse(JSON.stringify($section)));
                }

                
               //  $this.sections.forEach((o, index) => {
               //    if(o.uuid == 'cc14ae06-37ef-4d31-9cdb-3fae715f4b98'){
               //       $section = {
               //          ...o,
                        
               //          uuid: $this.$store.builder.generateUUID(),
               //       };
               //    }
               //  })


                if($this._create_position){
                  $section = $this.insertAt($section);
                  let $array = [];
                  $this.getSections().forEach((s) => {
                      let $sort = {
                        uuid: s.uuid,
                        position: s.position,
                      };

                      $array.push($sort);
                  });

                  window.dispatchEvent(new CustomEvent("builder::sort_sections", {
                      detail: {
                          sections: $array,
                      }
                  }));
                  $this._create_position = null;
               }
                
               $this.$wire.createSection($section);
               $this.navigatePage('pages');
               if($this.$store.builder.detectMobile()){
                  $this.closePage();
               }

               //  $this._create_position = null;
               //  $this.$dispatch('close');
            },

            insertAt($new){
               let $key = 0;
               let $this = this;
               let $sections = $this.getSections();
               let section = $this._create_position;
               // console.log(section, $new)
               $sections.forEach((s, i) => {
                  if(s.uuid == section.uuid){
                     $key = (i + 1);
                  }
               });
               $new.position = $key;
               $this.sections.splice($key, 0, $this.deepCopyArray($new));
               $this.getSections().forEach((s, i) => {
                  let $s = $this.sections.filter(obj => obj.uuid === s.uuid)[0];
                  $s.position = i;

                  if(s.uuid == $new.uuid){
                     // $s.position = $key;
                     $new.position = i;
                  }
               });
               // console.log($key, $new, section, $this.getSections())

               return $new;
            },
            deepCopyArray(arr) {
               return JSON.parse(JSON.stringify(arr));
            },

            closeSection(){
               if(this.$store.builder.detectMobile()){
                  this.closePage();
                  return;
               }
               this.navigatePage('pages');
               this._create_position=null;
            },
 
             init(){
                
               // window.addEventListener('section::add_section', (event) => {
               //     let detail = event.detail;
               //     console.log(detail)
               //     // __.insertSectionAt;
               // });
             }
          }
       });
    </script>
    @endscript
</div>