
<?php

   use App\Models\Section;
   use App\Models\Page;
   use App\Models\SiteHeaderLink;
   use function Livewire\Volt\{state, mount, placeholder, on};

   // Functions
   placeholder('
   <div class="w-full p-5 mt-1">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');

   state(['site']);

   state([
      'pages' => [],
      'sectionsConfig' => function(){
         $sections = [];
         foreach (config('yena.sections') as $key => $value) {
            $icon = ao($value, 'icons.showBanner');
            $icon = str_replace('/', '.', $icon);

            $view = "livewire.$icon";

            $section = [
               ...$value,
               'section' => $key,
               'icon' => view()->exists($view) ? view($view)->render() : '',
            ];

            $sections[$key] = $section;
         }

         return $sections;
      },
   ]);

   // // Methods
   $duplicatePage = function($item){
      $this->skipRender();
      $page = Page::where('uuid', ao($item, 'uuid'))->first();
      
      $_page = $page->duplicatePage();
      $sections = Section::where('page_id', $_page->uuid)->get()->toArray();

      // $this->js("pages.push(". collect($_page)->toJson() .")");
      // $this->js("sections.push(". collect($layouts)->toJson() .")");
      return [
         'page' => $_page,
         'sections' => $sections
      ];
   };

   $setAsHome = function($id){
      $this->skipRender();
      $this->site->pages()->update([
         'default' => 0,
      ]);
      if(!$page = $this->site->pages()->where('uuid', $id)->first()) return;

      $page->default = 1;
      $page->save();
      $this->dispatch('hideTippy');
   };

   $deletePage = function($id){
      $this->skipRender();
      if(!$page = $this->site->pages()->where('uuid', $id)->first()) return;

      $page->processDelete();

      // $this->getPages();
      $this->dispatch('hideTippy');
   };

   $add_to_header = function($item){
      $this->skipRender();
       $_item = new SiteHeaderLink;
       $_item->fill($item);
       $_item->site_id = $this->site->id;
       $_item->save();
   };
?>

<div>

   <div class="website-section" wire:ignore x-data="builder__pages">
      <div>
         <div x-show="createPage">
            <livewire:components.builder.parts.pages.new-page :$site zzlazy :key="uukey('builder', 'pages-new-page')" />
         </div>
      </div>
   
      <template x-if="!createPage">
         <div>
            <div class="design-navbar">
               <ul >
                  <li class="close-header !flex md:!hidden !w-[calc(var(--unit)*_5)]">
                      <a @click="closePage('-')">
                          <span>
                              {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                          </span>
                      </a>
                  </li>
                  <li class="!pl-0 md:!pl-[20px]">{{ __('Pages') }}</li>
                  <li></li>
               </ul>
            </div>
            <div class="container-small" wire:ignore>
               <div class="mt-2 website-pages">
                  <a class="yena-button-stack w-full !mt-0" @click="createPage=true">
                     {{ __('New Page') }}
                  </a>

                  
                  <div class="flex flex-col gap-2 mt-2">
                     <template x-for="(_item, index) in getPagesAndSections" :key="index">
                        <div class="flex flex-col border-dashed rounded-xl border-2 border-color--hover m-0 hover-select px-4 py-4" :class="{
                          '!bg-[#f5f5f5] !border-transparent': site.current_edit_page == _item.uuid
                        }" x-init="$watch('_item', value => $dispatch('builder::savePage', {page: _item}))">
                             <div class="flex w-[100%]">
                                <div class="flex flex-col gap-2 w-[100%]">
                                   <div class="flex items-center justify-between w-[100%]">
                                      <div @click="setPage(_item.uuid)" class="cursor-pointer flex items-center gap-2" :class="{
                                         'hidden': site.current_edit_page == _item.uuid
                                      }">
                                         <div class="yena-badge-g !bg-[#f7f3f2] !text-xs !text-black">{{ __('View Page') }}</div>
                                         <template x-if="!_item.default && !_item.published">
                                            {!! __i('--ie', 'eye-hidden', 'w-4 h-4 ml-1') !!}
                                         </template>
                                         <template x-if="_item.published">
                                            {!! __i('--ie', 'eye.5', 'w-4 h-4 ml-1') !!}
                                         </template>
                                      </div>
                                      <div class="ml-auto">
                                         
                                         <div class="ml-[4px]" @click="$event.stopPropagation();" x-data="{ tippy: {
                                            content: () => $refs.template.innerHTML,
                                            allowHTML: true,
                                            appendTo: $root,
                                            maxWidth: 360,
                                            interactive: true,
                                            trigger: 'click',
                                            animation: 'scale',
                                         } }"> 
                                             <template x-ref="template">
                                                <div class="yena-menu-list !w-full">
                                                   <a @click="duplicatePage(_item)" class="yena-menu-list-item">
                                                      <div class="--icon">
                                                         {!! __icon('interface-essential', 'copy-duplicate-object-add-plus', 'w-5 h-5') !!}
                                                      </div>
                                                      <span>{{ __('Duplicate') }}</span>
                                                   </a>
                                                   <a x-data="{__text:'{{ __('Copy Link') }}'}" @click="$clipboard($store.builder.generateSiteLink(site) + '/' + _item.slug); __text = window.builderObject.copiedText;" class="yena-menu-list-item">
                                                      <div class="--icon">
                                                         {!! __icon('interface-essential', 'share-arrow.1', 'w-5 h-5') !!}
                                                      </div>
                                                      <span x-text="__text">{{ __('Copy link') }}</span>
                                                   </a>
                                                   <a @click="addToHeader(_item)" class="yena-menu-list-item">
                                                      <div class="--icon">
                                                         {!! __icon('Type, Paragraph, Character', 'header', 'w-5 h-5') !!}
                                                      </div>
                                                      <span>{{ __('Add to Header') }}</span>
                                                   </a>
                                                   
                                                   <a @click="setAsHome(_item);" class="yena-menu-list-item">
                                                      <div class="--icon">
                                                         {!! __icon('interface-essential', 'home-house-line 2', 'w-5 h-5') !!}
                                                      </div>
                                                      <span>{{ __('Set as Home') }}</span>
                                                   </a>
                                                   
                                                   <a @click="setPage(_item.uuid); navigatePage('settings');" class="yena-menu-list-item">
                                                      <div class="--icon">
                                                         {!! __icon('interface-essential', 'setting4', 'w-5 h-5') !!}
                                                      </div>
                                                      <span>{{ __('Settings') }}</span>
                                                   </a>
                                                   
                                                   <hr class="--divider">
                                                   <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="deletePage(_item);">
                                                      <div class="--icon">
                                                         {!! __icon('interface-essential', 'delete-disabled.2', 'w-5 h-5') !!}
                                                      </div>
                                                      <span>{{ __('Permanently delete') }}</span>
                                                   </a>
                                                </div>
                                             </template>
                                            <template x-if="!_item.default">
                                               <button type="button" class="yena-button-o !px-0 !bg-[#f7f3f2]" :class="{
                                                  '!bg-[#fff]': site.current_edit_page == _item.uuid
                                                }" x-tooltip="tippy" x-ref="tippyButton">
                                                  <span class="--icon !mr-0">
                                                     {!! __icon('interface-essential', 'dots-menu', 'w-5 h-5  text-black') !!}
                                                  </span>
                                               </button>
                                            </template>
                                         </div>
                                      </div>
                                   </div>
                                   <div x-data="{show:false}" x-cloak>
                                       <h1 class="text-lg !m-0 cursor-pointer flex items-center gap-1" @click="show=true; $nextTick(() => { $root.querySelector('input').focus() });" x-show="!show">
                                          <template x-if="_item.default">
                                             {!! __i('interface-essential', 'home-house-line', 'w-5 h-5') !!}
                                          </template>
                                         <span x-text="_item.name"></span>
                                         <i class="ph ph-pencil"></i>
                                       </h1>
   
                                       <div class="flex">
                                           <div class="input-group mt-0" x-show="show" @click.outside="show=false">
                                               <input type="text" class="input-small blur-body" x-model="_item.name" name="name" placeholder="{{ __('Add name') }}">
                                           </div>
                                       </div>
                                   </div>
                                </div>
                             </div>
                             <div class="mt-2 relative" :class="{
                              'hidden': site.current_edit_page !== _item.uuid
                           }" x-data x-init="initSort($root, _item.uuid)">
                                <template x-if="_item.published == 0">
                                   <div class="absolute right-0 left-0 top-0 bottom-0 z-50">
                                      <div>
                                         <div class="flex flex-col justify-center items-start px-0 py-[10px]">
                                            {!! __i('--ie', 'eye-cross-circle 2', 'w-10 h-10') !!}
                                            <p class="mt-1 text-base text-left font-bold">
                                               {!! __t('Page disabled') !!}
                                            </p>
                                         </div>
                                      </div>
                                   </div>
                                </template>
                                <template x-if="_item.sections.length == 0">
                                   <div>
                                      <div>
                                         <div class="flex flex-col justify-center items-start px-0 py-[10px]">
                                            {!! __i('--ie', 'eye-cross-circle 2', 'w-10 h-10') !!}
                                            <p class="mt-1 text-base text-left font-bold">
                                               {!! __t('Your page section is empty') !!}
                                            </p>
                                            <a class="yena-black-btn gap-2 mt-1 cursor-pointer"  @click="setPage(_item.uuid); navigatePage('section')">{{ __('Create section') }}</a>
                                         </div>
                                      </div>
                                   </div>
                                </template>
                                <div :class="{
                                 'opacity-20': !_item.published,
                              }">
                                   <div class="sortable_section_wrapper flex flex-col gap-2">
                                      <template x-for="(item, index) in _.sortBy(_item.sections, 'position')" :key="item.uuid" x-ref="section_template">
                                         <div class="flex flex-col m-0">
                                              <div>
                                                  <div  class="w-[100%] rounded-[5px] bg-[rgb(247,_247,_247)] cursor-pointer py-1" :class="{
                                                     '!bg-[#fff]': site.current_edit_page == _item.uuid
                                                   }" @click="editSection(item)">
                                                      <div x-init="item.jsConfig = sectionsConfig[item.section];"></div>
                                                      <div class="w-[100%] flex pl-[17px] pr-[15px] py-[0] items-center">
                                                      <div class="handle cursor-grab">
                                                          {!! __i('custom', 'grip-dots-vertical', '!w-[10px] !h-[10px] text-[color:#BDBDBD]') !!}
                                                      </div>
                                                      
                                                      <div x-html="item.jsConfig.icon" class="!h-[58px] w-[130px] !flex !items-center [zoom:0.5] mx-2"></div>
                                  
                                                      <span class="text-[13px] font-semibold tracking-[-0.03em] truncate" x-text="item.content.title ? item.content.title : item.jsConfig.name"></span>
                                  
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
                                                                  </div>
                                                          
                                                                  <hr class="--divider">                           
                                                                  <a @click="editSection(item)" class="yena-menu-list-item">
                                                                      <div class="--icon">
                                                                          {!! __icon('--ie', 'pen-edit.7', 'w-5 h-5') !!}
                                                                      </div>
                                                                      <span>{{ __('Edit Section') }}</span>
                                                                  </a>
                                                                  <hr class="--divider">
                                                                  <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="deleteSectionId=item;">
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
                        </div>
                     </template>
                  </div>
                  {{-- <ul class="mb-1 add-new-page !hidden" @click="createPage=true">
                     <li >
                        <span >
                           <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M12 5V19" stroke="var(--background)"></path>
                              <path d="M5 12H19" stroke="var(--background)"></path>
                           </svg>
                        </span>
                        {{ __('New Page') }}
                     </li>
                  </ul>
                  <ul >
                     <template x-for="(item, index) in pages" :key="index">
                        <li class="page-list-section" :class="{
                           'active': site.current_edit_page == item.uuid
                           }" @click="setPage(item.uuid)" x-data="{
                           tippy: {
                              content: () => $refs.template.innerHTML,
                              allowHTML: true,
                              appendTo: $root,
                              maxWidth: 360,
                              interactive: true,
                              trigger: 'click',
                              animation: 'scale',
                           }
                        }">
                           <span class="home-icon page-list-item">
                              <template x-if="item.default">
                                 {!! __i('interface-essential', 'home-house-line') !!}
                              </template>
                              
                              <span x-text="item.name"></span>
                           </span>
                           <span class="page-list-option" @click="$event.stopPropagation();" x-tooltip="tippy">
                              {!! __icon('interface-essential', 'dots-menu') !!}
                           </span>
                                          
                           <template x-ref="template">
                              <div class="yena-menu-list !w-full">
                                 <a @click="duplicatePage(item)" class="yena-menu-list-item">
                                    <div class="--icon">
                                       {!! __icon('interface-essential', 'copy-duplicate-object-add-plus', 'w-5 h-5') !!}
                                    </div>
                                    <span>{{ __('Duplicate') }}</span>
                                 </a>
                                 <a x-data="{__text:'{{ __('Copy Link') }}'}" @click="$clipboard($store.builder.generateSiteLink(site) + '/' + item.slug); __text = window.builderObject.copiedText;" class="yena-menu-list-item">
                                    <div class="--icon">
                                       {!! __icon('interface-essential', 'share-arrow.1', 'w-5 h-5') !!}
                                    </div>
                                    <span x-text="__text">{{ __('Copy link') }}</span>
                                 </a>
                                 <a @click="addToHeader(item)" class="yena-menu-list-item">
                                    <div class="--icon">
                                       {!! __icon('Type, Paragraph, Character', 'header', 'w-5 h-5') !!}
                                    </div>
                                    <span>{{ __('Add to Header') }}</span>
                                 </a>
                                 
                                 <a @click="setAsHome(item);" class="yena-menu-list-item">
                                    <div class="--icon">
                                       {!! __icon('interface-essential', 'home-house-line 2', 'w-5 h-5') !!}
                                    </div>
                                    <span>{{ __('Set as Home') }}</span>
                                 </a>
                                 
                                 <a @click="setPage(item.uuid); navigatePage('settings');" class="yena-menu-list-item">
                                    <div class="--icon">
                                       {!! __icon('interface-essential', 'setting4', 'w-5 h-5') !!}
                                    </div>
                                    <span>{{ __('Settings') }}</span>
                                 </a>
                                 
                                 <hr class="--divider">
                                 <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="deletePage(item);">
                                    <div class="--icon">
                                       {!! __icon('interface-essential', 'delete-disabled.2', 'w-5 h-5') !!}
                                    </div>
                                    <span>{{ __('Permanently delete') }}</span>
                                 </a>
                              </div>
                           </template>
                        </li>
                     </template>
                  </ul> --}}
               </div>
            </div>
         </div>
      </template>
   </div>

   
   @script
      <script>
         Alpine.data('builder__pages', () => {
            return {
               createPage: false,
               sectionsConfig: @entangle('sectionsConfig'),
               getPagesAndSections(){
                  return this.pages.map(page => {
                     page.sections = this.sections.filter(section => section.page_id === page.uuid);
                     return page;
                  });
               },
               _get_sections(page_id){
                  var sections = [];

                  this.sections.forEach((element, index) => {
                     if(page_id == element.page_id){
                        sections.push(element);
                     }
                  });
                  return _.sortBy(sections, 'position');
               },
               initSort($root, page_id){
                  var $this = this;

                  let $wrapper = $root.querySelector('.sortable_section_wrapper');
                  let $template = $root.querySelector('[x-ref="section_template"]');

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
                           let $array = $this._get_sections(page_id);
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

               duplicatePage(item){
                  var $this = this;
                  this.$wire.duplicatePage(item).then(r => {
                     $this.pages.push(r.page);
                     // $this.sections.push(r.sections);


                     r.sections.forEach((e) => {
                        $this.sections.push(e);
                     });

                     $this.setPage(r.page.uuid);
                  });
               },
               
               addToHeader(_i){
                  var count = this.siteheader.links.length + 1;

                  var url = _i.slug;
                  var slug = _i.slug;

                  this.pages.forEach((x, i) => {
                      if(x.default && x.uuid == _i.uuid){
                          slug = '';
                      };
                  });

                  url = `/${slug}`;

                  let item = {
                     uuid: this.$store.builder.generateUUID(),
                     title: _i.name,
                     link: url,
                     position: count,
                  };

                  this.siteheader.links.push(item);

                  this.$wire.add_to_header(item);
               },

               setPage(page_id){
                  this.site.current_edit_page = page_id;

                  this.broadcastSite();
                  this.$dispatch('builder::setPage');
                  this.$dispatch('builder::saveSite');
                  
                  this.getCurrentPage();
               },

               setAsHome(item){
                  this.pages.forEach(element => {
                     element.default=0;
                  });
                  item.default=1;
                  this.$wire.setAsHome(item.uuid);
               },

               deletePage(item){
                  let index = 0;
                  this.pages.forEach(element => {
                     if(item.uuid == element.uuid){
                        this.pages.splice(index, 1);
                     }

                     index++;
                  });
                  this.$wire.deletePage(item.uuid);
                  this.$dispatch('builder::reloadPage');
                  
                  this.getCurrentPage();
               },

               init(){
                  window.addEventListener('builder::setAsHomeEvent', (event) => {
                     this.setAsHome(event.detail);
                  });
                  window.addEventListener('builder::setPageEvent', (event) => {
                     this.setPage(event.detail);
                  });
                  window.addEventListener('builder::deletePageEvent', (event) => {
                     this.deletePage(event.detail);
                  });
               }
            }
         });
      </script>
   @endscript
</div>