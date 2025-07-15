<div>
    <div x-data="builder__sectionBanner" class="alpine-data-section-banner">
        @php
          $_banners = [];
          foreach (collect(config('yena.banners')) as $key => $value) {
            $value['preview'] = Blade::render("<x-livewire::sections.banner.partial.edit.banner.banner_$key/>");
            $_banners[$key] = $value;
          }
        @endphp
      <div x-show="__page == 'form'">
          <div>
            <x-livewire::components.builder.parts.form />
          </div>
      </div>
    
      <div x-show="__page == 'button'">
          <div>
            <x-livewire::components.builder.parts.button />
          </div>
      </div>
    
      <div x-show="__page == 'section'">
          <div>
             <x-livewire::components.builder.parts.section />
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
                    <li>{{ __('Banner') }}</li>
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
                            <x-livewire::sections.banner.partial.edit.content />
                        </div>
                        <div x-cloak x-show="__tab == 'style'" data-tab-content>
                            <x-livewire::sections.banner.partial.edit.style />
                        </div>
                    </div>
                </div>
            </div>
          </div>
      </div>
    </div>

  @script
  <script>
      Alpine.data('builder__sectionBanner', () => {
         return {
            autoSaveTimer: null,
            __tab: 'content',
            __page: '-',

            __banners: {!! collect($_banners)->toJson() !!},
            __bannerConfig: [],

            getBannerConfig(){
                this.__bannerConfig = this.__banners[this.section.settings.banner_style];
            },

            registerEvents(){
                let $this = this;
                window.addEventListener('section:content:' + this.section.uuid, (event) => {
                    $this.__page = '-';
                    $this.__tab = 'content';
                });
                window.addEventListener('section:style:' + this.section.uuid, (event) => {
                    $this.__page = '-';
                    $this.__tab = 'style';
                });
                window.addEventListener('section:form:' + this.section.uuid, (event) => {
                    $this.__page = 'form';
                });
                window.addEventListener('section:button:' + this.section.uuid, (event) => {
                    $this.__page = 'button';
                });
                window.addEventListener('section:section:' + this.section.uuid, (event) => {
                    $this.__page = 'section';
                });
            },
            init(){
               var $this = this;

               $this.getBannerConfig();

               $this.registerEvents();
            }
         }
      });
  </script>
  @endscript
</div>