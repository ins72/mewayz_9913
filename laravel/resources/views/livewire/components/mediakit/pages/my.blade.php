
<?php

   use App\Models\Section;
   use App\Models\SitePage;
   use App\Models\SiteHeaderLink;
   use App\Models\BioSite;
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
   ]);

   mount(function(){
       $this->getPages();
   });


   $getPages = function(){
      $this->pages = BioSite::where('user_id', iam()->id)->where('is_template', 0)->orderBy('id', 'desc')->get();
   };

   // // Methods
   $duplicatePage = function($item){
      $this->skipRender();
      $page = SitePage::where('uuid', ao($item, 'uuid'))->first();
      
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

   <div class="website-section" wire:ignore x-data="builder___pages">
      <div>
         <div x-show="createPage">
            <livewire:components.bio.parts.pages.create :$site zzlazy :key="uukey('builder', 'bio.parts.pages.new-page')" />
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
                  <li class="!pl-0 md:!pl-[20px]">{{ __('LinkinBio') }}</li>
                  <li></li>
               </ul>
            </div>
            <div class="container-small" wire:ignore>
               <div class="mt-2 website-pages">
                  {{-- <a class="yena-button-stack w-full !mt-0" @click="createPage=true">
                     {{ __('New Page') }}
                  </a> --}}
                  <ul class="mb-1 add-new-page !hizdden" @click="createPage=true">
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

                     <div class="mt-2">
                        <div class="flex flex-col gap-4">
                           @foreach ($pages as $item)
                           <div class="border-gray-50 relative rounded-lg border-2 bg-white p-3 pr-1 shadow-lg transition-all hover:shadow-md sm:p-4">
                              <li class="relative flex items-center justify-between">
                                 <div class="relative flex items-center w-[100%]">
                                 
                                    <div>
                                       <div class="w-8 h-8 sm:h-10 sm:w-10">

                                          @if (empty($item->logo))
                                          <div>
                                             <div class="rounded-md p-2 bg-[#eee] !h-full !w-[100%] default">
                                                <div>
                                                   {!! __i('--ie', 'image-picture', 'text-gray-300') !!}
                                                </div>
                                             </div>
                                          </div>
                                          @else
                                          <img alt=" " class="h-full h-full rounded-md object-cover" src="{{ $item->getLogo() }}">
                                          @endif
                                       </div>
                                    </div>


                                    <div class="ml-2 w-[100%]">
                                       <div class="flex items-center w-[100%]">
                                          <a class="w-24 truncate text-sm font-semibold text-blue-800 sm:w-full sm:text-base" href="{{ route('console-bio-index', ['slug' => $item->_slug]) }}">{{ $item->name }}</a>
                                          
                                          @if ($layout = $item->sections()->first())
                                          <a class="flex items-center space-x-1 rounded-md bg-gray-100 px-2 py-0.5 transition-all duration-75 hover:scale-105 active:scale-100 ml-auto">
                                             <i class="fi fi-rr-apps-add text-gray-700 text-sm mr-1"></i>
               
                                             
                                             <p class="whitespace-nowrap text-sm text-gray-500">{{ $item->sections()->count() }}<span class="ml-1 hidden sm:inline-block">{{ __('sections') }}</span></p>
                                          </a>
                                          @endif
                                       </div>
                                       <h3 class="max-w-[200px] truncate text-sm font-medium text-gray-700 md:max-w-md xl:max-w-lg">{{ '@' . $item->address }}</h3>
                                    </div>
                                 </div>
               
                                 <div class="flex items-center">
                                    {{-- <p class="mr-3 hidden whitespace-nowrap text-sm text-gray-500 sm:block">1 year ago</p>
                                    <p class="mr-1 whitespace-nowrap text-sm text-gray-500 sm:hidden">399d</p> --}}
               
               
                                    {{-- <div>
                                       <button type="button" class="rounded-md px-1 py-2 transition-all duration-75 hover:bg-gray-100 active:bg-gray-200 --control" aria-expanded="false">
                                          <i class="fi fi-rr-menu-dots-vertical flex text-base text-gray-500"></i>
                                       </button>
                                    </div> --}}
                                 </div>
                              </li>

                              <a href="{{ route('console-bio-index', ['slug' => $item->_slug]) }}" class="yena-black-btn mt-1 !justify-between">{!! __i('--ie', 'settings.8') !!}{{ __('Manage') }}</a>
                           </div>
                           @endforeach
                        </div>
                     </div>
                  </ul>
               </div>
            </div>
         </div>
      </template>
   </div>

   
   @script
      <script>
         Alpine.data('builder___pages', () => {
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