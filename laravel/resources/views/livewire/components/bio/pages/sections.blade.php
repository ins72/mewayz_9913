
<?php

   use App\Models\Section;
   use App\Models\Page;
   use App\Models\SiteHeaderLink;
   use function Livewire\Volt\{state, mount, placeholder, on};

   // Functions
   placeholder('
   <div class="w-[100%] p-5 mt-1">
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');

   state(['site']);

   state([
      'pages' => [],
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
            <livewire:components.bio.parts.pages.new-page :$site zzlazy :key="uukey('builder', 'pages-new-page')" />
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
                  <a class="yena-button-stack w-[100%] !mt-0" @click="createPage=true">
                     {{ __('New Page') }}
                  </a>
                  <ul class="mb-1 add-new-page !hidden" @click="createPage=true">
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
                     <div class="w-[100%] h-[66px] rounded-[10px] bg-[rgb(247,_247,_247)] opacity-100 cursor-pointer mt-1"  @click="$dispatch('opensection::layout')">
                        <div class="w-[100%] h-[66px] flex pl-[17px] pr-[15px] py-[0] items-center">
                           <div class="w-[10px]"></div>
         
                           <div class="rounded-[10px] w-[36px] h-[36px] ml-[13px] mr-[18px] my-[0] bg-black text-white flex items-center justify-center">
                              {!! __i('interface-essential', 'user-profile', 'w-[20px] h-[20px]') !!}
                           </div>
         
                           <span class="text-[13px] font-semibold tracking-[-0.03em]">{{ __('Header') }}</span>
                           <div class="ml-[auto] mr-[2px] my-[0] flex items-center gap-2" @click="$event.stopPropagation()">
                              <label class="sandy-switch">
                                 <input class="sandy-switch-input" name="settings[enable_header]" x-model="currentPage.settings.enableHeader" type="checkbox">
                                 <span class="sandy-switch-in"><span class="sandy-switch-box is-white"></span></span>
                           </label>
                           <div class="mr-[4px]">
                              
                              <button type="button" class="yena-button-o !px-0 !opacity-0">
                                 <span class="--icon !mr-0">
                                    {!! __icon('interface-essential', 'dots', 'w-5 h-5  text-[color:#BDBDBD]') !!}
                                 </span>
                              </button>
                           </div>
                           </div>
                        </div>
                     </div>
                     <div class="w-[100%] h-[66px] rounded-[15px] bg-[#fff] opacity-100 cursor-pointer border-2 border-gray-300 border-dashed mt-1">
                        <div class="w-[100%] h-[66px] flex pl-[17px] pr-[15px] py-[0] items-center">
                           <div class="w-[10px]"></div>
         
                           <div>
                              <div class="shadow-xl rounded-full w-[36px] h-[36px] ml-[13px] mr-[18px] my-[0] bg-[#000] text-white flex items-center justify-center">
                                 {!! __i('Content Edit', 'book-book-pages', 'w-[20px] h-[20px]') !!}
                              </div>
                           </div>
         
                           <div>
                              <input placeholder="{{ __('Page name') }}" x-model="currentPage.name" type="text" class="text-gray-600 overflow-hidden text-ellipsis min-h-[21px] border-2 border-dashed border-gray-200 rounded-full resize-none !p-2 !px-4 w-[100%] focus:border-2 focus:border-solid">
                           </div>
                           
                           <div class="ml-[auto] mr-[2px] my-[0] flex items-center gap-2" wire:ignore>
         
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
                                    <div class="yena-menu-list !w-full">
                                       <div class="px-3 pt-1">
                                          <p class="yena-text font-bold text-lg">{{__('More Options')}}</p>
                                       </div>
                              
                                       <hr class="--divider">
                                       <template x-if="!currentPage.default">
                                          <a @click="currentPage.published=!currentPage.published" class="yena-menu-list-item">
                                             <div class="--icon">
                                                {!! __icon('--ie', 'eye-hidden', 'w-5 h-5') !!}
                                             </div>
                                             <span x-text="currentPage.published ? '{{ __('Hide Page') }}' : '{{ __('Show Page') }}'"></span>
                                          </a>
                                       </template>
                                       
                                       {{-- <a href="" class="yena-menu-list-item">
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
                                          <span>{{ __('Delete Page') }}</span>
                                       </a>
                                    </div>
                                 </template>
                                 <template x-if="!currentPage.default">
                                    <button type="button" class="yena-button-o !px-0" x-tooltip="tippy" x-ref="tippyButton">
                                       <span class="--icon !mr-0">
                                          {!! __icon('interface-essential', 'dots', 'w-5 h-5  text-[color:#BDBDBD]') !!}
                                       </span>
                                    </button>
                                 </template>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="w-[100%] h-[1px] bg-[var(--yena-colors-blackAlpha-100)] inline-flex mt-[10px]"></div>
         
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
                              <template x-if="!item.default && !item.published">
                                 {!! __i('--ie', 'eye-hidden') !!}
                              </template>
                              
                              <span x-text="item.name"></span>
                           </span>
                           <template x-if="!item.default">
                              <span class="page-list-option" @click="$event.stopPropagation();" x-tooltip="tippy">
                                 {!! __icon('interface-essential', 'dots-menu') !!}
                              </span>
                           </template>
                                          
                           <template x-ref="template">
                              <div class="yena-menu-list !w-[100%]">
                                 <template x-if="!item.default">
                                    <a @click="item.published=!item.published; $dispatch('builder::savePage', {
                                       page: item
                                    })" class="yena-menu-list-item">
                                       <div class="--icon">
                                          {!! __icon('--ie', 'eye-hidden', 'w-5 h-5') !!}
                                       </div>
                                       <span x-text="item.published ? '{{ __('Hide Page') }}' : '{{ __('Show Page') }}'"></span>
                                    </a>
                                 </template>
                                 
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
                  </ul>
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