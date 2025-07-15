
<?php

    use App\Yena\YenaMail;
    use App\Models\SiteForm;
    use App\Yena\SandyAudience;
    use function Livewire\Volt\{state, mount};
    state([
        'site'
    ]);

    $saveForm = function($content){
        // Check for plans
        $this->skipRender();

        $_c = SiteForm::where('site_id', $this->site->id)->count();

        if(__o_feature('consume.contacts', $this->site->user) != -1 && $_c >= __o_feature('consume.contacts', $this->site->user)){
            return [
                'status' => 'error',
                'response' => __('Submissions quota reached. Please contact site owner')
            ];
        }

        $extra = [
            'created_by' => 0,
        ];
        $email = ao($content, 'email');
        $_content = is_array(ao($content, 'content')) ? ao($content, 'content') : [];

        $name = !empty(ao($_content, 'first_name')) ? ao($_content, 'first_name') .' '. ao($_content, 'last_name') : explode('@', $email)[0];
        $contact = [
            'name' => $name,
            'email' => $email,
            ...$_content,
        ];
        
        $a = new \App\Models\Audience;
        $a->owner_id = $this->site->user_id;
        $a->contact = $contact;
        $a->extra = $extra;
        $a->save();

        SandyAudience::create_activity($this->site->user_id, $a->id, __('Created'), __('Audience created successfully.'));

        $form = new SiteForm;
        $form->fill($content);

        $form->site_id = $this->site->id;
        $form->save();


        // Send email
        $mail = new YenaMail;
        $mail->send([
           'to' => $this->site->email,
           'subject' => __('New Form Submission on :site', ['site' => $this->site->name]),
        ], 'site.form-submit', [
           'form' => $form,
           'site' => $this->site,
        ]);


        return [
            'status' => 'success',
        ];
    };
?>
<div class="-mobile-plane builder--page generate-builder-wire">

    <div x-data="builder__generate_index" :data-theme="siteTheme">
        <template x-if="site.settings.preloader">
            <div id="preloader" :class="{
                'done':!_preloader,
                '!hidden': _preloadHidden
            }" :style="{
                '--logo-height': site.header.logo_width + 'px',
              }">
                <template x-if="site.header.logo_type == 'image'">
                   <div>
                      <template x-if="!site.header.logo">
                         <div class="default-image light !block w-5 h-5">
                            {!! __i('--ie', 'image-picture', 'text-gray-300') !!}
                         </div>
                      </template>
                      <template x-if="site.header.logo">
                         <img :src="$store.builder.getMedia(site.header.logo)" class="site-logo light !block" :class="{'!h-[var(--logo-height)]': true}" alt="">
                      </template>
                   </div>
                </template>
                <template x-if="site.header.logo_type == 'text' || !site.header.logo_type">
                   <span class="t-2 logo-text" x-text="site.header.logo_text ? site.header.logo_text : site.name" :style="`--text-count: ${$store.builder.countTotalLetters(site.name)}`"></span>
                </template>
            </div>
        </template>
        <div :style="styles()" wire:ignore>
        
            <template x-if="!currentPage().hide_header">
                <div>
                    <x-livewire::sections.header.viewComponent />
                </div>
            </template>

            {{-- <div class="h-[calc(44px_+_var(--logo-height))] lg:h-[75px] w-[100%] hidden [background:var(--background)]" :class="{
                '!block': site.header.sticky || site.header._float,
            }"></div> --}}

            <div wire:ignore>
                <div class="sortable_section_wrapper yena-builder-sections">
                    <template x-for="(item, index) in currentSections" :key="item.uuid" x-ref="section_template">
                        <div class="w-[100%]" :data-id="item.uuid" :id="`section-${item.id}`" >
                            <div x-bit.clean="'section-' + item.section" x-data="{section:item}"></div>
                        </div>
                    </template>
                </div>
    
                @foreach(config("yena.sections") as $key => $item)
                    @php
                        if(!$_name = __a($item, 'components.alpineView')) continue;
                        $_name = str_replace('/', '.', $_name);
            
                        $component = "livewire::$_name";
                    @endphp
                    <template bit-component="section-{{ $key }}">
                        <div wire:ignore>
                        <x-dynamic-component :component="$component"/>
                        </div>
                    </template>
                @endforeach
            </div>
            
        
            <div>
                <x-livewire::sections.footer.viewComponent />
            </div>
        </div>
        
        <template x-if="showBranding()">
            <div class="fixed bottom-[10px] right-[10px] z-[99999] w-[calc(100%_-_20px)] md:w-[250px] yena-footer-branding" x-cloak>
                <a href="{{ config('app.url') }}" target="_blank" class="yena-a-button flex">
                    <span>
                        <template x-if="siteTheme == 'light' || !siteTheme">
                            <img src="{{ logo_branding('light') }}" class="w-5 h-5 object-contain" alt="">
                        </template>
                        <template x-if="siteTheme == 'dark'">
                            <img src="{{ logo_branding('dark') }}" class="w-5 h-5 object-contain" alt="">
                        </template>
                    </span>
                    <div>
                        <span>
                            {{ __('Create sites with AI') }}
                        </span>
                    </div>
                </a>
            </div>
        </template>
    </div>
    @script
        <script>
            Alpine.data('builder__generate_index', () => {
               return {
                    formContent: {
                        email: '',
                        first_name: '',
                        last_name: '',
                        phone: '',
                        message: '',
                    },
                    formError: false,
                    formSuccess: false,
                    siteTheme: 'light',
                    _preloader: true,
                    _preloadHidden: false,

                    showBranding(){
                        let show = true;
                        if(this.__o_feature('feature.branding') && this.site.settings.disable_branding){
                            show = false;
                        }
                        
                        if(this.site.is_admin_selected){
                            show = false;
                        }


                        return show;
                    },

                    toggleDark(){
                        if(this.siteTheme == 'light'){
                            this.siteTheme = 'dark';
                        }
                        sessionStorage.setItem(this.site.id + 'theme', this.siteTheme);
                        this.initTheme();
                    },
                    toggleLight(){
                        if(this.siteTheme == 'dark'){
                            this.siteTheme = 'light';
                        }

                        sessionStorage.setItem(this.site.id + 'theme', this.siteTheme);
                        this.initTheme();
                    },
                    getSections(){
                        var sections = [];

                        this.sections.forEach((element, index) => {
                            if(this.currentPage().uuid == element.page_id){
                                sections.push(element);
                            }
                        });

                        return window._.sortBy(sections, 'position');
                    },
                    initTheme(){
                        this.siteTheme = 'light';
                        
                        if(this.site.settings.siteTheme && this.site.settings.siteTheme !== '-'){
                            this.siteTheme = this.site.settings.siteTheme;
                        }
                        let sessionTheme = sessionStorage.getItem(this.site.id + 'theme');
                        if(sessionTheme){
                            this.siteTheme = sessionTheme;
                        }

                        if(window.initNavigate){
                            let body = document.querySelector('body');
                            body.setAttribute('data-theme', this.siteTheme);
                        }
                    },
                    saveForm(){
                        let content = {
                            content: this.formContent,
                        };
                        this.$wire.saveForm(content);
                    },

                    styles(){
                        var site = this.site;
                        return this.$store.builder.generateSiteDesign(site);
                    },

                    init(){
                        this.initTheme();
                        let $this = this;

                        // setTimeout(function() {
                        //     let staticHtml = $this.$store.builder.removeAlpineAttributes(document.querySelector('body').innerHTML);


                        //     document.querySelector('body').innerHTML = staticHtml.body.innerHTML;
                            
                        //     console.log(staticHtml);
                        // }, 3000)

                        if(this.site.settings.preloader){
                            $this.$nextTick(() => { 
                                setTimeout(() => {
                                    $this._preloader = false;
                                    setTimeout(() => {
                                        $this._preloadHidden = true;
                                    }, 1500);
                                }, 1000);
                            });
                            window.addEventListener('load', function(){
                            });
                        }

                        // console.log(this.$store.site.link('/about'))
                    }
               }
            });
        </script>
    @endscript
</div>
