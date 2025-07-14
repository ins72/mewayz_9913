<?php
    use App\Models\SiteSocial;
    use App\Models\SiteFooterGroup;
    use function Livewire\Volt\{on, state};

    state(['site']);
    
    $createGroup = function($item){
        $this->skipRender();
        $_item = new SiteFooterGroup;
        $_item->fill($item);
        $_item->site_id = $this->site->id;
        $_item->uuid = __a($item, 'uuid');
        $_item->links = [];
        $_item->settings = [
            'silence' => 'golden'
        ];
        $_item->save();
    };

    $saveGroup = function($links){
        $this->skipRender();
        foreach ($links as $key => $value) {
            if(!$_item = SiteFooterGroup::where('uuid', __a($value, 'uuid'))->first()) continue;
            $_item->fill($value);
            $_item->save();
        }

        $this->js('$store.builder.savingState = 2');
    };

    $deleteGroup = function($id){
        $this->skipRender();

        if(!$_item = SiteFooterGroup::where('uuid', $id)->where('site_id', $this->site->id)->first()) return;
        $_item->delete();
    };

    $createSocial = function($item){
        $this->skipRender();

        $_item = new SiteSocial;
        $_item->fill($item);
        $_item->site_id = $this->site->id;
        $_item->save();
    };

    $deleteSocial = function($id){
        $this->skipRender();
        if(!$_social = SiteSocial::where('uuid', $id)->where('site_id', $this->site->id)->delete()) return;
    };

?>

<div>

    <div x-data="builder___footer" wire:ignore :style="styles()">
        
        @php
            $footers = [];
            foreach ([1,2,3,4] as $key => $value) {
                $footers[] = Blade::render("<x-livewire::sections.footer.partial.edit.footer.footer_$value/>");
            }
        @endphp

        <div x-show="__page == 'groups'">
            <div>
                <x-livewire::sections.footer.partial.edit.groups />
            </div>
        </div>

        <template x-for="(item, index) in footerGroups" :key="item.uuid">
            <div>
                <div x-show="__page == 'section::groups::' + item.uuid">
                    <x-livewire::sections.footer.partial.edit.single-group />
                </div>

                <template x-if="item.links && item.links.length > 0">
                    <div>
                        <template x-for="item in item.links" :key="item.uuid">
                            <div x-show="__page == 'section::footer_link::' + item.uuid">
                                <x-livewire::sections.footer.partial.edit.single-group />
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>

        <div x-show="__page == 'socials'">
            <div>
                <x-livewire::sections.footer.partial.edit.socials />
            </div>
        </div>

        <div x-show="__page == 'buttons'">
            <div>
                <x-livewire::sections.footer.partial.edit.buttons />
            </div>
        </div>

        <div x-show="__page == 'copyright'">
            <div>
                <x-livewire::sections.footer.partial.edit.copyright />
            </div>
        </div>

        <div x-show="__page == 'text'">
            <div>
                <x-livewire::sections.footer.partial.edit.text />
            </div>
        </div>
        
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
                        <li>{{ __('Footer') }}</li>
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
                                <x-livewire::sections.footer.partial.edit.content />
                            </div>
                            <div x-cloak x-show="__tab == 'style'" data-tab-content>
                                <x-livewire::sections.footer.partial.edit.style />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  @script
  <script>
      Alpine.data('builder___footer', () => {
         return {
            autoSaveTimer: null,
            __tab: 'content',
            __page: '-',

            __banners: {!! collect(config('yena.banners'))->toJson() !!},
            __bannerConfig: [],

            __footer: {!! collect($footers)->toJson() !!},

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
                var $eventID = 'section::' + this.section.uuid;

                $this.$dispatch($eventID, $this.section);
                clearTimeout($this.autoSaveTimer);

                $this.autoSaveTimer = setTimeout(function(){
                    $this.$store.builder.savingState = 0;
                    event = new CustomEvent("builder::saveSite");

                    window.dispatchEvent(event);
                }, $this.$store.builder.autoSaveDelay);
            },
            init(){
               var $this = this;

               this.$watch('site' , (value, _v) => {
                $this._save();
               });

               this.$watch('footerGroups' , (value, _v) => {
                clearTimeout($this.autoSaveTimer);

                $this.autoSaveTimer = setTimeout(function(){
                    $this.$store.builder.savingState = 0;

                    $this.$wire.saveGroup($this.footerGroups);
                }, $this.$store.builder.autoSaveDelay);
               });
               $this.getBannerConfig();
            }
         }
      });
  </script>
  @endscript
</div>