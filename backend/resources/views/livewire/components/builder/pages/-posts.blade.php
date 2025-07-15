
<?php

   use App\Models\Section;
   use App\Models\Page;
   use App\Models\SitePost;
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

   $deletePost = function($id){
      $this->skipRender();
      if(!$page = $this->site->posts()->where('uuid', $id)->first()) return;

      $page->delete();

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

   $savePost = function($item){
        if(!$_item = $this->site->posts()->where('uuid', __a($item, 'uuid'))->first()) return;
        $_item->fill($item);
        $_item->save();

        $this->js('$store.builder.savingState = 2');
   };

   $createPost = function($item){
        $this->skipRender();
        $_post = new SitePost;
        $_post->fill($item);
        $_post->site_id = $this->site->id;
        $_post->save();
   };
?>

<div>

   @php
     $_banners = [];
     foreach (collect(config('yena.banners')) as $key => $value) {
       $value['preview'] = Blade::render("<x-livewire::sections.banner.partial.edit.banner.banner_$key/>");
       $_banners[$key] = $value;
     }
   @endphp
   <div wire:ignore x-data="builder__posts">
        <template x-if="_page=='edit'">
            <div>
                <x-livewire::components.builder.parts.posts.edit />
            </div>
        </template>
   
        <template x-if="_page == '-'">
            <div class="website-section">
                <div class="design-navbar">
                <ul >
                    <li class="close-header !flex md:!hidden !w-[calc(var(--unit)*_5)]">
                        <a @click="closePage('-')">
                            <span>
                                {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                            </span>
                        </a>
                    </li>
                    <li class="!pl-0 md:!pl-[20px]">{{ __('Posts') }}</li>
                    <li></li>
                </ul>
                </div>
                <div class="container-small" wire:ignore>
                <div class="mt-2 website-pages">
                    <ul class="mb-1 add-new-page" @click="createPost">
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
                    <a class="yena-button-stack w-full !mt-0" @click="createPost">
                        {{ __('New Page') }}
                    </a>
                    <ul>
                        <template x-for="(item, index) in posts" :key="index">
                            <li class="page-list-section" x-data="{
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
                            
                            <span class="home-icon page-list-item" @click="editPost(item.uuid)">
                                <template x-if="item.default">
                                    {!! __i('interface-essential', 'home-house-line') !!}
                                </template>
                                
                                <span x-text="item.name"></span>
                            </span>
                            <span class="page-list-option" x-tooltip="tippy">
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
                                    <a x-data="{__text:'{{ __('Copy Link') }}'}" @click="$clipboard($store.builder.generateSiteLink(site) + '/post/' + item.slug); __text = window.builderObject.copiedText;" class="yena-menu-list-item">
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
                                    <div x-data="{confirm:false}">
                                        <div class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="!confirm ? confirm = true : deletePost(item);" x-init="$watch('confirm', value => {
                                            if (value) {
                                                setTimeout(function(){
                                                    confirm = false;
                                                }, 5000)
                                            }
                                        })">
                                            <div class="--icon">
                                            {!! __icon('interface-essential', 'delete-disabled.2', 'w-5 h-5') !!}
                                            </div>
                                            <span x-text="!confirm ? '{{ __('Permanently delete') }}' : '{{ __('Confirm Delete?') }}'"></span>
                                            <template x-if="confirm">
                                                <div x-data="confirmDotsHandler">
                                                    <template x-for="(dots, index) in dotsArray" :key="index">
                                                        <span x-text="dots"></span>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
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
         Alpine.data('builder__posts', () => {
            return {
                _page: '-',
                post: [],
                autoSaveTimer: null,
                __banners: {!! collect($_banners)->toJson() !!},
                __bannerConfig: [],

                getBannerConfig(){
                    this.__bannerConfig = this.__banners[this.post.settings.banner_style];
                },
               createPost(){
                    let $this = this;

                    let item = {
                        uuid: this.$store.builder.generateUUID(),
                        name: 'Untitled Post ' + (this.posts.length + 1),
                        slug: 'untitled-'+(Math.random() + 1).toString(36).substring(7),
                        content: {
                            'silence': 'golden',
                        },
                        seo: {
                            'silence': 'golden',
                        },
                        settings: {
                            banner_style: 1, 
                            shape_avatar: 100, 
                            enable_image: true, 
                            actiontype: 'button', 
                            align: 'left',
                            height: '320',
                            width: '75',
                            title: 'l',
                            image_type: 'fill', 
                        },
                        section_settings: {
                            height: 'fit',
                            width: 'fill',
                            spacing: 'l',
                            align: 'center',
                        },
                    };
                    $this.posts.push(item);
                    $this.$wire.createPost(item);
               },

               editPost(uuid){
                    let post = [];
                    this.posts.forEach((x, i) => {
                        if(x.uuid == uuid){
                            post = x;
                        };
                    });

                    this.post = post;
                    this.generatePost = post;
                    this.previewPage = 'post';
                    this._page = 'edit';
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

               deletePost(item){
                  let index = 0;
                  this.posts.forEach(element => {
                     if(item.uuid == element.uuid){
                        this.posts.splice(index, 1);
                     }

                     index++;
                  });
                  this.$wire.deletePost(item.uuid);
                //   this.$dispatch('builder::reloadPost');
                  
                //   this.getCurrentPage();
               },

                _save(){
                    let $this = this;
                    // var $eventID = 'section::' + this.section.uuid;


                    // $this.$dispatch($eventID, $this.section);
                    clearTimeout($this.autoSaveTimer);

                    $this.autoSaveTimer = setTimeout(function(){
                        $this.$store.builder.savingState = 0;
                        $this.$wire.savePost($this.post);

                        // event = new CustomEvent("builder::save_sections_and_items", {
                        //     detail: {
                        //         section: $this.section,
                        //         js: '$store.builder.savingState = 2',
                        //     }
                        // });

                        // window.dispatchEvent(event);
                    }, $this.$store.builder.autoSaveDelay);
                },

               init(){
                let $this = this;
                this.$watch('post' , (value) => {
                    $this._save();
                });

                // this.editPost(this.posts[0].uuid);
               
                window.addEventListener("sectionPostMedia:" + this.post.uuid, (event) => {
                  this.post.content.image = event.detail.image;
                  $this._save();
                });
                //   window.addEventListener('builder::setAsHomeEvent', (event) => {
                //      this.setAsHome(event.detail);
                //   });
                //   window.addEventListener('builder::setPageEvent', (event) => {
                //      this.setPage(event.detail);
                //   });
                //   window.addEventListener('builder::deletePageEvent', (event) => {
                //      this.deletePage(event.detail);
                //   });
               }
            }
         });
      </script>
   @endscript
</div>