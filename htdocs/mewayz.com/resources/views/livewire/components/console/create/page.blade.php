<?php
    use App\Models\Site;
    use App\Models\Section;
    use App\Yena\Site\Generate;
    use function Livewire\Volt\{state, mount};

    state([
        'sectionAmount' => 4,
        'randomSections' => true,
    ]);

    state([
        'site' => fn() => Site::where('_slug', 'zzzz-23foaeZYYL8yC3s')->first(),
        'sections' => [],
    ]);

    state([
        'aiCompleted' => false,
    ]);

    state([
      'name' => '',
    ]);

    state([
        'randomPrompt' => fn() => collect(config('prompts.randomPrompt')),
        'generatePrompt' => fn () => collect(config('yena.generatePrompt')),
        'aiTone' => function(){
            $tones = [];

            foreach (config('yena.aiTone') as $key => $value) {
                $tone = [
                    ...$value,
                    'icon' => __dashed_svg(ao($value, 'icon'), 'w-4 h-4')
                ];

                $tones[] = $tone;
            }

            return $tones;
        },
        'aiLanguage' => fn() => config('yena.aiLanguage'),
    ]);

    mount(function(){
        $this->generateSite();
    });

    //

    $generateAiSections = function(){

    };

    $getSections = function(){
        $this->sections = $this->site->sections()->get();
    };

    $generateAiSection = function($id){
        if($section = Section::find($id)){
            $config = config("yena.sections.$section->section");

            if($class = ao($config, 'aiClass')){
                $class = app()->make($class);
                $class->setSite($this->site)->setSection($section);
                $class->setImageType('unsplash');
                $class->setContext([
                    'category' => 'Art',
                    'prompt' => 'Landing page for a freelance copywriter specializing in SEO content',
                ]);
                $class->generate();
            }
            
        }

        // Check if it's done calling all sections
    };

    $callSections = function(){
        foreach ($this->sections as $section) {
            if($section->generated_ai) continue;

            $this->js('$wire.generateAiSection('. $section->id .')');
        }
    };

    $checkAiCompletion = function(){
        $i = true;

        foreach($this->sections as $section){
            $config = config("yena.sections.$section->section");
            if($class = ao($config, 'aiClass')){
                if(!$section->generated_ai) $i = false;
            }
        }

        if($i){
            $this->site->ai_completed = true;
            $this->site->save();


            return true;
        }
        return false;
    };


    $createRandomSite = function(){
        // Get random name

        // Get random prompt and category
        $this->getSections();

        $this->callSections();
    };




    // $generateSections = function(){

    // };

    $generateRandomSections = function(){
		$templates = config('sections.landing');
        $newSections = [];


        foreach ($templates as $k => $v) {
            if(!is_array($sections = ao($v, 'sections'))) continue;
            $last_key = array_key_last($sections);

            foreach ($sections as $key => $section) {
                if($key == 0 && $key == $last_key) continue;
                // $k = array_rand($sections);
                // $template = $templates[$k];

                if($key !== 0 && $key !== $last_key){
                    
                }


                // if($key == $last_key){

                // }
            }
        }

        return $newSections;
    };

    $generateSite = function(){
		$templates = config('sections.landing');
		$k = array_rand($templates);
		$template = $templates[$k];


        $sections = $template['sections'];

        // if($this->randomSections){

        // }

        // dd($this->generateRandomSections());
    };

    
    $callSection = function(){
        if($section = $this->site->sections()->where('generated_ai', 0)->where('calling_ai', 0)->first()){
            $section->calling_ai = 1;
            $section->save();
            $this->generateAiSection($section->id);
        }

        if($this->checkAiCompletion()){
            return true;
        }

        return false;
    };

    $generateSiteAi = function(){
        $generate = new Generate;
        $build = $generate->setOwner(iam())->setName('Ai' . str()->random(3))->build();


        $this->site = $build;
        $this->getSections();

        return route('console-builder-index', ['slug' => $this->site->_slug]);
    };


    // Blank
    $createBlank = function(){
        $this->validate([
            'name' => 'required|min:4',
        ]);
        $_c = Site::where('user_id', iam()->id)->count();
        if(__o_feature('consume.sites', iam()) != -1 && $_c >= __o_feature('consume.sites', iam())){
            $this->js('window.runToast("error", "'. __('You have reached your site creation limit. Please upgrade your plan.') .'");');
            return;
        }
        $generate = new Generate;
        $build = $generate->setOwner(iam())->setName($this->name)->build();


        $route = route('console-builder-index', ['slug' => $build->_slug]);

        $this->js(
            '
                window.runToast("success", "'. __('Site created successfully') .'")
                setTimeout(function() {
                window.location.replace("'.$route.'");
                }, 2000);
            '
        );

        $this->dispatch('refreshSites');
    };

    
    $createAi = function($content){
        $this->validate([
            'name' => 'required|min:4',
        ]);
        $_c = Site::where('user_id', iam()->id)->count();
        if(__o_feature('consume.sites', iam()) != -1 && $_c >= __o_feature('consume.sites', iam())){
            session()->flash('_error_error', __('You have reached your site creation limit. Please upgrade your plan.'));
            return;
        }

        if(!__o_feature('feature.ai_site_generator')){
            session()->flash('_error_error', __('Upgrade your plan to create site with ai.'));
            return;
        }

        $source = ['unsplash', 'pexels'];
        $k = array_rand($source);
        $source = $source[$k];
        $content = [
            ...$content,
            'generateImages' => $source 
        ];
        $generate = new Generate;
        $build = $generate->setOwner(iam())->setName($this->name)->build();

        $build->ai_generate = 1;
        $build->ai_generate_prompt = $content;
        $build->save();

        $route = route('console-builder-index', ['slug' => $build->_slug]);

        $this->js(
            '
                window.runToast("success", "'. __('Site created successfully...') .'")
                setTimeout(function() {
                window.location.replace("'.$route.'");
                }, 2000);
            '
        );
    };
?>

<div>
    <div x-data="console__create_page">

        <template x-if="loading">
            <div class="loader-card">
                <div class="preloader mb-4">
                   <div class="loader-animation-container">
                        <div class="inner-circles-loader"></div>
                   </div>
                </div>
                <p class="loader-text fade text-center pre-line">{{ __("Generating site...") }}</p>
                {{-- <p class="loader-text fade text-center pre-line">{{ __("Generating site... \n Please dont close this page while we proceess your request.") }}</p> --}}
            </div>
        </template>

        {{-- <a @click="generateSite">zzzz</a> --}}

        <div class="flex w-full min-h-screen relative ptzz-[var(--yena-space-4)]">

            <div class="z-[1] w-full flex-1">
{{--     
                <div class="fixed bottom-[var(--yena-space-4)] right-[var(--yena-space-4)] z-[var(--yena-zIndices-overlay)]">
    
                </div> --}}

                <div x-show="page=='-'" x-cloak>
                    <x-livewire::components.console.create.cards />
                </div>
                <div x-show="page=='blank'" x-cloak>
                    <x-livewire::components.console.create.blank />
                </div>
                <div x-show="page=='generate'" x-cloak>
                    <x-livewire::components.console.create.generate />
                </div>
                <div x-show="page=='template'" x-cloak>
                    <x-livewire::components.console.create.template />
                </div>
            </div>
        </div>
        <template x-ref="category_template">
          <div class="yena-menu-list !w-[300px] md:!w-[400px] !max-w-full">
            <template x-for="(item, index) in generatePrompt" :key="index">
                <a @click="aiContent.category = item" :class="{
                    '!bg-[var(--yena-colors-gray-100)] !border-[var(--yena-colors-gray-200)]': aiContent.category == item,
                }" class="yena-menu-list-item">
                   <span class="text-sm" x-text="item"></span>
                </a>
            </template>
         </div>
        </template>
   
       <template x-ref="translate_template">
        <div class="yena-menu-list !w-[300px] md:!w-[400px] !max-w-full">
           <template x-for="(item, index) in aiLanguages" :key="index">
               <a @click="aiContent.textLanguage = item.prompt" :class="{
                   '!bg-[var(--yena-colors-gray-100)] !border-[var(--yena-colors-gray-200)]': aiContent.textLanguage == item.prompt,
               }" class="yena-menu-list-item">
                  <span class="text-sm" x-text="item.name"></span>
               </a>
           </template>
        </div>
       </template>
       <template x-teleport="body">
          <x-modal name="upgrade-modal" :show="false" removeoverflow="true" maxWidth="max-w-[var(--yena-sizes-5xl)]" focusable>
             <div>
                      
                <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-1 right-1 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
                   <i class="fi fi-rr-cross-small"></i>
                </a>
                <livewire:components.upgrade.page lazy :key="uukey('builder', 'upgrade-modal')"/>
             </div>
          </x-modal>
       </template>
    </div>
    @script
       <script>
           Alpine.data('console__create_page', () => {
              return {
                loading: false,
                generatePrompt: @entangle('generatePrompt'),
                randomPrompt: @entangle('randomPrompt'),
                randomPrompt2: [],
                processingTimer: null,

                page: '-',
                __page: '-',
                // processingInterval: null,

                builderTimer: null,
                aiContent: {
                    category: 'Art',
                    textPrompt: '',
                    textContent: 'generate',
                    textAmount: 'brief',
                    textTone: 'casual',
                    textLanguage: 'english',
                    generateImages: 'none',
                    imageQuery: null,
                },
                tippy: {
                    allowHTML: true,
                    maxWidth: 360,
                    interactive: true,
                    trigger: 'click',
                    animation: 'scale',
                },
                tippyTone: {},
                tippyCategory: {},
                tippyTranslate: {},
                aiTones: @entangle('aiTone'),
                aiLanguages: @entangle('aiLanguage'),

                generateSite(){
                    let $this = this;
                    $this.loading = true;

                    $this.$wire.generateSiteAi().then(response => {

                        $this.builderTimer = setInterval(function(){
                            setTimeout(() => {
                                $this.$wire.callSection().then(r => {
                                    console.log(r);
                                    if(r) {
                                        window.runToast("success", "{{ __('Site created successfully') }}")
                                        setTimeout(function() {
                                            window.location.replace(response);
                                        }, 2000);

                                        clearInterval($this.builderTimer);
                                        $this.loading = false;
                                    }
                                });
                            }, 500);
                        }, 2000);
                    });
                },

                generateRandom(){
                    let $this = this;
                    $this.loading = true;

                    $this.$wire.createRandomSite();

                    $this.processingTimer = setInterval(function(){
                        $this.$wire.checkAiCompletion().then(r => {
                                console.log(r);
                            if(r) {

                                // clearInterval($this.processingTimer);
                                // $this.loading = false;
                            }
                        });
                    }, 2000);
                },

                init(){
                    let $this = this;
                    
                    this.tippy.appendTo = this.$root;

                    this.tippyTranslate = {
                        ...this.tippy,
                        maxWidth: 416,
                        content: this.$refs.translate_template.innerHTML,
                    }
                    this.tippyCategory = {
                        ...this.tippy,
                        placement: 'bottom',
                        maxWidth: 416,
                        content: this.$refs.category_template.innerHTML,
                    }
                    
                    $this.randomPrompt2 = $this.$store.builder.getTwoRandomValues($this.randomPrompt);

                    // console.log($this.$store.builder.selectRandomArray($this.generatePrompt), $this.generatePrompt)
                }
                
              }
           });
       </script>
    @endscript
</div>