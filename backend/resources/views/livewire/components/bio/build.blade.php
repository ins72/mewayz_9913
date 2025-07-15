
<?php
   use function Livewire\Volt\{state, mount, on, placeholder};
   
   state(['site']);

   $checkAddress = function($address){
      $address = slugify($address, '-');
      $validator = Validator::make([
         'address' => $address
      ], [
         'address' => 'required|string|min:3|unique:bio_sites,address,'.$this->site->id
      ]);

      if($validator->fails()){
         $this->js('$store.builder.savingState = 2');
         return [
            'status' => 'error',
            'response' => $validator->errors()->first('address'),
         ];
      }

      $this->site->address = $address;
      $this->site->save();

      $this->js('$store.builder.savingState = 2');
      return [
         'status' => 'success',
         'response' => '',
      ];
   };
?>
<div>

    <div x-data="builder_bio_build" class="lg:w-[650px] max-w-full">
        
        <div class="yena-builder-sections">
            <div class="w-[100%]">
               <div>
                  <div>
                     <div>
                        <div class="wire-section section-width-fill banner-box new box section-bg-wrapper focus">
                           <section class="section-content">
                              <div class="banner-box section-bg-wrapper transparent color section-height-fit section-width-fill align-items-center [--spacingLR:calc(var(--unit)_*_8)] [--bg-grayscale:0%] [--background-upper:center] [--background-bottom:center] [--bg-blurscale:1.1] [--bg-blur:0px] [--bg-opacity:1]">
                                 <div class="inner-content section-container align-items-center">
                                    <div>
                                       <div class="banner-layout-2 w-boxed !pt-5">
                                          <div class="banner section-component !pb-0">
                                             <div class="banner-text content-heading">
                                                <section class="subtitle-width-size [text-align:inherit] flex flex-col gap-2">
                                                     <div x-data="{show:false}" x-cloak>
                                                         <h1 class="title pre-line --text-color t-3 [text-align:inherit]" @click="show=true; $nextTick(() => { $root.querySelector('input').focus() });" x-show="!show" x-text="site.name"></h1>

                                                         <div class="flex">
                                                             <div class="input-group mt-0" x-show="show" @click.outside="show=false">
                                                                 <input type="text" class="input-small blur-body" x-model="site.name" name="name" placeholder="{{ __('Add name') }}">
                                                             </div>
                                                         </div>
                                                     </div>
                                                     <div x-data="{show:false}" x-cloak>
                                                         <h1 class="title pre-line --text-color t-1 [text-align:inherit]" @click="show=true; $nextTick(() => { $root.querySelector('input').focus() });" x-show="!show" x-text="'@' + site.address"></h1>


                                                         <div class="flex">
                                                             <div class="input-group mt-0" x-show="show" @click.outside="show=false">
                                                                 <input type="text" class="input-small blur-body" maxlength="20" :value="site.address" @input="checkAddress($event.target.value)" placeholder="{{ __('Site Address') }}">
                                                             </div>
                                                         </div>
                                                     </div>
                                       
                                                   <template x-if="addressError">
                                                      <div class="bg-red-200 text-[11px] p-1 px-2 rounded-md">
                                                         <div class="flex items-center">
                                                            <div>
                                                               <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                                            </div>
                                                            <div class="flex-grow ml-1 text-xs" x-text="addressError"></div>
                                                         </div>
                                                      </div>
                                                   </template>
                                                </section>
                                                <section class="flex flex-col subtitle-width-size">
                                                   <div x-data="{show:false}" x-cloak>
                                                      <p class="t-2 pre-line subtitle-width-size subtitle --text-color !w-[100%]" @click="show=true; $nextTick(() => { $root.querySelector('textarea').focus() });" x-show="!show" x-text="site.bio"></p>
                                                      <div class="input-group mt-2" x-show="show" @click.outside="show=false">
                                                         <x-builder.textarea class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="site.bio" name="title" placeholder="{{ __('Add text here') }}"/>
                                                      </div>
                                                   </div>

                                                   <div class="mt-2" x-cloak>
                                                      <div class="gallery-box section-width-fill box focus wire-section section-height-fit align-items-center">
                                                         <div class="inner-content">
                                                            <div class="gallery-container w-boxed !pt-0 xpx-0 !pb-0" style="--grid-height: 60px;--grid-height-mobile: 60px;--grid-width: 60px;--grid-width-mobile: 60px;">
                                                               <div class="gallery-container__wrapper">
                                                                  <div class="gallery-container__items !flex !overflow-x-auto !pb-[var(--s-2)]">
                                                                     <a class="gallery-container__item flex-[0_0_var(--grid-width)] !h-[var(--grid-height)]" @click="$dispatch('opensection::social')">
                                                                        <div class="default-image !bg-white border-2 border-dashed border-black">
                                                                          <i class="ph ph-plus text-xl text-black"></i>
                                                                        </div>
                                                                     </a>
                                                                     <template x-if="site.socials && site.socials.length > 0">
                                                                         <template x-for="(social, index) in window._.sortBy(site.socials, 'position')" :key="index">
                                                                            <a class="gallery-container__item flex-[0_0_var(--grid-width)]" @click="$dispatch('opensection::social')">
                                                                             <div class="default-image">
                                                                                <i :class="socials[social.social].icon" class="text-xl"></i>
                                                                             </div>
                                                                            </a>
                                                                         </template>
                                                                     </template>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   {{-- <div style="width: 100%;">
                                                      <div class="mt-2 button-holder subtitle-width-size">
                                                         <div class="flex items-center">
                                                            <a class="btn-1 yena-site-link" @click="navigatePage('section::button')">
                                                            <button class="t-1 shape">{{ __('Edit Button') }}</button>
                                                            </a>
                                                            <a class="btn-2 yena-site-link" href="javascript:void(0)" target="_self">
                                                            <button class="t-1 shape">Button 2</button>
                                                            </a>
                                                         </div>
                                                      </div>
                                                   </div> --}}
                                                </section>
                                             </div>
                                             <div>
                                                 <div>
                               
                                                    <div class="section-item-image w-[100%] h-[350px] lg:!h-[280px] lg:!mb-[var(--s-4)]" @click="openMedia({event: 'siteBanner:change', sectionBack:'navigatePage(\'__last_state\')'})" :class="{
                                                     'bg-[var(--c-mix-1)] flex justify-center items-center rounded-[20px]': !site.banner
                                                    }">
                                                       <img :src="$store.builder.getMedia(site.banner)" :class="{
                                                          '!hidden': !site.banner
                                                       }" class="!h-full w-[100%] object-cover rounded-[20px]">
                                     
                                                       <template x-if="!site.banner">
                                                         <div class="w-[100%] h-[350px] lg:!h-[280px] flex items-center justify-center">
                                                            {!! __i('--ie', 'image-picture', 'text-gray-300 h-10 w-10') !!}
                                                         </div>
                                                       </template>
                                                    </div>
                                                 </div>
                                                <div class="-mt-10 lg:!-mt-28">
                                                   <div class="flex flex-col items-start avatar-image" :class="{
                                                      'default': !site.logo,
                                                      }" @click="openMedia({event: 'siteLogo:change', sectionBack:'navigatePage(\'__last_state\')'})">
                                                      <img :src="$store.builder.getMedia(site.logo)" class="Fit accent banner-image rounded-[100%] mb-[var(--s-2)] object-cover !h-[130px] !w-[130px]" :class="{
                                                         '!hidden': !site.logo
                                                         }">
                                                      <template x-if="!site.logo">
                                                         <div>
                                                            <div class="banner-image section-item-image !h-[130px] !w-[130px]" :class="{'default': !site.logo}">
                                                               <div>
                                                                  {!! __i('--ie', 'image-picture', 'text-gray-300') !!}
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </template>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                        </div>
                        </section>
                     </div>
                  </div>
               </div>
            </div>
         </div>

        <div>
            
            <div class="p-5">
                <div class="flex flex-col gap-4 sortable_section_wrapper ![--accent:#eee] ![--contrast-color:#000]">
                    <template x-for="(item, index) in getSections()" :key="item.uuid" x-ref="section_template">
                       <div class="flex flex-col border-dashed rounded-xl border-2 border-color--hover m-0 hover-select px-4 py-5">

                            <div class="relative" @click="editSection(item)">
                                <div x-bit="'section-' + item.section" x-data="{section:item}"></div>
                                <div class="screen"></div>
                            </div>
                            <div class="mt-2">
                                <div class="w-[100%] h-[66px] rounded-[15px] bg-[rgb(247,_247,_247)] opacity-100 cursor-pointer" x-data="{
                                    init(){
                                    
                                    {{-- this.$watch('item.published', (value) => {
                                        let data = {
                                            uuid: item.uuid,
                                            published: value
                                        };
            
                                        this.$wire.set_section_staus(data);
                                    }); --}}
            
                                    }
                                }" @click="editSection(item)">
                                    <div x-init="item.jsConfig = sectionConfig[item.section];"></div>
                                    <div class="w-[100%] h-[66px] flex pl-[17px] pr-[15px] py-[0] items-center">
                                    <div class="handle cursor-grab">
                                        {!! __i('custom', 'grip-dots-vertical', '!w-[10px] !h-[10px] text-[color:#BDBDBD]') !!}
                                    </div>
                
                                    <div class="{{--[box-shadow:0_4px_5.84px_hsla(0,0%,50.2%,.353)]--}} shadow-xl rounded-[10px] w-[36px] h-[36px] ml-[13px] mr-[18px] my-[0] flex items-center justify-center" :style="{
                                        'background': item.jsConfig.color,
                                        'color': $store.builder.getContrastColor(item.jsConfig.color),
                                        }" x-html="item.jsConfig['ori-icon-svg']"></div>
                
                                    <span class="text-[13px] font-semibold tracking-[-0.03em]" x-text="item.content.title ? item.content.title : item.jsConfig.name"></span>
                
                                    <div class="ml-[auto] mr-[2px] my-[0] flex items-center gap-2" @click="$event.stopPropagation();">
                                        <label class="sandy-switch">
                                            <input class="sandy-switch-input" name="settings[published]" x-model="item.published" value="true" :checked="item.published" @input="$dispatch('builder::saveSection', {
                                             section: item
                                          })" type="checkbox">
                                            <span class="sandy-switch-in"><span class="sandy-switch-box is-white"></span></span>
                                    </label>
                                    
                                        <div class="mr-[4px]" @click="$event.stopPropagation();" x-data="{ tippy: {
                                            content: () => $refs.template.innerHTML,
                                            allowHTML: true,
                                            appendTo: $root,
                                            maxWidth: 360,
                                            interactive: true,
                                            trigger: 'click',
                                            animation: 'scale',
                                        } }">
                                            <template x-ref="template">
                                                <div class="yena-menu-list !w-[100%]">
                                                <div class="px-3 pt-1">
                                                    <p class="yena-text font-bold text-lg">{{__('More Options')}}</p>
                                        
                                                    {{-- <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">Created September 18th, 2023</p>
                                                    <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">by Jeff Jola</p> --}}
                                                </div>
                                        
                                                <hr class="--divider">                           
                                                <a @click="editSection(item)" class="yena-menu-list-item">
                                                    <div class="--icon">
                                                        {!! __icon('--ie', 'pen-edit.7', 'w-5 h-5') !!}
                                                    </div>
                                                    <span>{{ __('Edit Section') }}</span>
                                                </a>
                                        
                                                {{-- <a href="" class="yena-menu-list-item">
                                                    <div class="--icon">
                                                        {!! __icon('Cursor Select Hand', 'Cursor, Select, Hand, Click', 'w-5 h-5') !!}
                                                    </div>
                                                    <span>{{ __('Move Page') }}</span>
                                                </a>
                                        
                                                <a href="" class="yena-menu-list-item">
                                                    <div class="--icon">
                                                        {!! __icon('--ie', 'document-text-edit', 'w-5 h-5') !!}
                                                    </div>
                                                    <span>{{ __('Rename') }}</span>
                                                </a> --}}
                                                {{-- <hr class="--divider">
                                                <a href="" class="yena-menu-list-item">
                                                    <div class="--icon">
                                                        {!! __icon('--ie', 'copy-duplicate-object-add-plus', 'w-5 h-5') !!}
                                                    </div>
                                                    <span>{{ __('Duplicate') }}</span>
                                                </a>
                                                <a href="" class="yena-menu-list-item">
                                                    <div class="--icon">
                                                        {!! __icon('--ie', 'share-arrow.1', 'w-5 h-5') !!}
                                                    </div>
                                                    <span>{{ __('Copy link') }}</span>
                                                </a> --}}
                                                <hr class="--divider">
                                                <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="deleteSection(item)">
                                                    <div class="--icon">
                                                        {!! __icon('interface-essential', 'trash-bin-delete', 'w-5 h-5') !!}
                                                    </div>
                                                    <span>{{ __('Delete Section') }}</span>
                                                </a>
                                            </div>
                                            </template>
                                            <button type="button" class="yena-button-o !px-0" x-tooltip="tippy">
                                                <span class="--icon !mr-0">
                                                {!! __icon('interface-essential', 'dots', 'w-5 h-5  text-[color:#BDBDBD]') !!}
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                       </div>
                    </template>
                 </div>
            </div>
        </div>

        
        <template x-teleport="body">
            <div class="overlay backdrop delete-overlay" :class="{
                '!block': deleteSectionId
            }" @click="deleteSectionId=false">
                <div class="delete-site-card !border-0 !rounded-2xl !shadow-lg" @click="$event.stopPropagation()">
                   <div class="overlay-card-body !rounded-2xl">
                      <h2>{{ __('Delete Section?') }}</h2>
                      <p class="mt-4 mb-4 text-[var(--t-m)] text-center leading-[var(--l-body)] text-[var(--c-mix-3)] w-3/5 ml-auto mr-auto">{{ __('Are you sure you want to delete this section? Once deleted, you will not be able to restore it.') }}</p>
                      <div class="card-button pl-[var(--s-2)] pr-[var(--s-2)] pt-[0] pb-[var(--s-2)] flex gap-2">
                         <button class="btn btn-medium neutral !h-[calc(var(--unit)*_4)]" type="button" @click="deleteSectionId=false">{{ __('Cancel') }}</button>
        
                         <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-[calc(var(--unit)*_4)]" @click="__delete_section(deleteSectionId)">{{ __('Yes, Delete') }}</button>
                      </div>
                   </div>
                </div>
             </div>
        </template>
    </div>
    
   @script
    <script>
      Alpine.data('builder_bio_build', () => {
         return {
           addressError: false,
           checkAddress(address){
             address = address.toString() // Cast to string
                         .toLowerCase() // Convert the string to lowercase letters
                         .normalize('NFD') // The normalize() method returns the Unicode Normalization Form of a given string.
                         .trim() // Remove whitespace from both sides of a string
                         .replace(/\s+/g, '-') // Replace spaces with -
                         .replace(/[^\w\-]+/g, '') // Remove all non-word chars
                         .replace(/\-\-+/g, '-');
             this.$event.target.value = address;
             let $this = this;

             $this.addressError = false;
             clearTimeout($this.autoSaveTimer);

             $this.autoSaveTimer = setTimeout(function(){
                $this.$store.builder.savingState = 0;
                 
                $this.$wire.checkAddress(address).then(r => {
                   if(r.status === 'error'){
                      $this.addressError = r.response;
                   }
                   if(r.status === 'success'){
                      $this.addressError = false;
                   }
                });

             }, $this.$store.builder.autoSaveDelay);
           },
            currentPage(){
               var page = this.pages[0];

               this.pages.forEach((e, index) => {
                  if(e.uuid == this.site.current_edit_page) page = e;
               });
               return page;
            },
            
            getSections(){
               var sections = [];

               this.sections.forEach((element, index) => {
                  if(this.currentPage().uuid == element.page_id){
                     sections.push(element);
                  }
               });
               return _.sortBy(sections, 'position');
            },
            initSort(){
                var $this = this;

                let $wrapper = this.$root.querySelector('.sortable_section_wrapper');
                let $template = this.$root.querySelector('[x-ref="section_template"]');

                let callback = function(e){
                   let $array = [];

                   e.forEach((item, index) => {
                      let $new = {
                         uuid: item.uuid,
                         position: item.position,
                      };

                      $array.push($new);
                   });

                   let event = new CustomEvent("builder::sort_sections", {
                         detail: {
                            sections: $array,
                         }
                   });

                   window.dispatchEvent(event);
                };






                if($wrapper){
                      window.Sortable.create($wrapper, {
                         ...$this.$store.builder.sortableOptions,
                         onEnd: (event) => {
                            let $array = $this.getSections();
                            let steps = Alpine.raw($array)
                            let moved_step = steps.splice(event.oldIndex, 1)[0]
                            steps.splice(event.newIndex, 0, moved_step);
                            let keys = []
                            steps.forEach((step, i) => {
                               keys.push(step.uuid);

                               $array.forEach((x, _i) => {
                                  if(x.uuid == step.uuid) x.position = i;
                               });
                            });

                            $template._x_prevKeys = keys;

                            callback($array);
                         },
                      });
                }
               },

            init(){
                let $this = this;
                $this.initSort();
    
                window.addEventListener("siteLogo:change", (event) => {
                    $this.site.logo = event.detail.image;
                });
                window.addEventListener("siteBanner:change", (event) => {
                    $this.site.banner = event.detail.image;
                });
            }
         }
      });
   </script>
   @endscript
</div>