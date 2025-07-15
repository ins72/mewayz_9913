<?php
    use App\Models\SiteHeaderLink;
    use function Livewire\Volt\{on, state};

    state(['site']);

    on([
        'section::header:create_link' => function($item, $parent_id = null){
            $this->create_link($item, $parent_id);
        },
    ]);
    // on([
    //     'section::header:create_link' => function($item, $parent_id = null){
    //         $_item = new SiteHeaderLink;
    //         $_item->fill($item);
    //         $_item->site_id = $this->site->id;
    //         $_item->parent_id = $parent_id;
            
    //         $_item->save();
    //     },
    // ]);

    $create_link = function($item, $parent_id = null){

        $_item = new SiteHeaderLink;
        $_item->fill($item);
        $_item->site_id = $this->site->id;
        $_item->parent_id = $parent_id;

        // Check if parent works    
        
        $_item->save();
    };

    $delete_link = function($id){
        if(!$_item = SiteHeaderLink::where('uuid', $id)->first()) return;


        SiteHeaderLink::where('parent_id', $_item->uuid)->delete();
        
        $_item->delete();
    };
?>

<div wire:ignore>
    <div x-data="builder___header" :style="styles()">
        
        @php
            $headers = [];
            foreach ([1,2,3] as $key => $value) {
                $headers[] = Blade::render("<x-livewire::sections.header.partial.edit.header.header_$value/>");
            }
            //   foreach (\Storage::disk('sections')->files('header/partial/edit/header') as $key => $value) {
            //     $file = Str::before($value, '.blade.php');
            //     $name = basename($file);
            //     $component = "livewire::sections.header.partial.edit.header.header_1";

            //     $headers[] = Blade::render(<<<HTML
            //     <x-livewire::sections.header.partial.edit.header.$name/>
            //     HTML);
            //   }
            //   print_r($headers);
        @endphp

        <div x-show="__page == 'links'">
            <div>
                <x-livewire::sections.header.partial.edit.links />
            </div>
        </div>

        <template x-for="(item, index) in siteheader.links" :key="item.uuid">
            <div>
                <div x-show="__page == 'section::link::' + item.uuid">
                    <x-livewire::sections.header.partial.edit.single-link />
                </div>

                <template x-if="item.children && item.children.length > 0">
                    <div>
                        <template x-for="item in item.children" :key="item.uuid">
                            <div x-show="__page == 'section::link::' + item.uuid">
                                <x-livewire::sections.header.partial.edit.single-link />
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>

        <div x-show="__page == 'button'">
            <div>
                <x-livewire::sections.header.partial.edit.button />
            </div>
        </div>
        <div x-show="__page == 'announcement'">
            <div>
                <x-livewire::sections.header.partial.edit.announcement />
            </div>
        </div>

        {{-- <div x-sortable="{
            handle: '.handle', callback(data){
                console.log('bruhaa', data, $wire);
            }}">

            <div class="list-item sortable-item" x-bind:data-id="1">
                <span x-text="`Jeff`" class="name"></span>
                <span class="handle">drag me</spa>
            </div>
            <div class="list-item sortable-item" x-bind:data-id="1">
                <span x-text="`Jola`" class="name"></span>
                <span class="handle">drag me</spa>
                </div>
        </div> --}}
        
        <div x-cloak x-show="__page == '-'">
            <div class="banner-section !block">
                <div>
                    <div class="banner-navbar">
                        <ul >
                            <li class="close-header">
                            <a @click="closePage('pages')">
                                <span>
                                    {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                                </span>
                            </a>
                        </li>
                        <li>{{ __('Header') }}</li>
                        <li></li>
                        </ul>
                    </div>
                    <div class="sticky container-small">
                        <div class="tab-link">
                            <ul class="tabs">
                            <li class="tab !w-full" @click="__tab = 'content'" :class="{'active': __tab == 'content'}">{{ __('Content') }}</li>
                            <li class="tab !w-full" @click="__tab = 'style'" :class="{'active': __tab == 'style'}">{{ __('Style') }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="container-small tab-content-box">
                        <div class="tab-content">
                            <div x-cloak x-show="__tab == 'content'" data-tab-content>
                                <x-livewire::sections.header.partial.edit.content />
                            </div>
                            <div x-cloak x-show="__tab == 'style'" data-tab-content>
                                <x-livewire::sections.header.partial.edit.style />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  @script
  <script>
      Alpine.data('builder___header', () => {
         return {
            autoSaveTimer: null,
            __tab: 'content',
            __page: '-',

            __banners: {!! collect(config('yena.banners'))->toJson() !!},
            __bannerConfig: [],

            __header: {!! collect($headers)->toJson() !!},

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
                clearTimeout($this.autoSaveTimer);

                $this.autoSaveTimer = setTimeout(function(){
                    $this.$store.builder.savingState = 0;
                    event = new CustomEvent("builder::saveSite");

                    window.dispatchEvent(event);
                }, $this.$store.builder.autoSaveDelay);
            },
            init(){
               var $this = this;
               window.addEventListener("sectionMediaEvent:header", (event) => {
                   this.site.header.logo = event.detail.image;
                   $this._save();
               });

               this.$watch('site' , (value, _v) => {
                $this._save();
               });

               this.$watch('siteheader.links' , (value, _v) => {
                $this.$dispatch('section::header', $this.siteheader.links);
                clearTimeout($this.autoSaveTimer);

                $this.autoSaveTimer = setTimeout(function(){
                    $this.$store.builder.savingState = 0;
                    event = new CustomEvent("builder::saveHeaderLinks", {
                        detail: {
                            links: $this.siteheader.links,
                            js: '$store.builder.savingState = 2',
                        }
                    });

                    window.dispatchEvent(event);
                }, $this.$store.builder.autoSaveDelay);
               });
               $this.getBannerConfig();
            }
         }
      });
  </script>
  @endscript
</div>