
<?php

use App\Models\Section;
use App\Yena\Ai\Purify;
use App\Models\SiteAiChatSession;
use App\Models\SiteAiChatHistory;
use function Livewire\Volt\{state, mount, usesFileUploads, updated, on};

// Methods

state([
    'site',
    'randomPrompt' => fn() => collect(config('prompts.randomPrompt')),
]);

mount(function(){
    // $this->get();
});

?>
<div class="ai-edit-livewire relative" :class="{
    'ai-c-livewire-opened': generateAiId
}" x-data="__ai_chat_alpine" wire:ignore>

    <div class="builder-chat" :class="{
        'selected-section': generateAiId.uuid
    }" x-show="generateAiId">

        <div class="builder-partner-panel ai-background-o overflow-hidden">
            <template x-if="!__o_feature('feature.ai_pages_sections')">
                <div class="flex flex-col justify-center items-center px-[20px] py-[60px]">
                    {!! __i('Design Tools', 'magic-wand-circle', 'w-14 h-14') !!}
                    <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                        {!! __t('Upgrade your plan to use AI to build this section.') !!}
                    </p>
                    <button type="button" @click="$dispatch('open-modal', 'upgrade-modal')" class="btn btn-large mt-3 !h-[40px] !border-none !transition-none">{{ __('Upgrade') }}</button>
                </div>
            </template>
        
            <div class="absolute z-0 pointer-events-none top-0 min-h-screen [background:var(--bg-img)_center_center_repeat] animate-[180s_linear_0s_infinite_normal_none_running_animation-1w9onv1] [mask-image:linear-gradient(to_left,_rgba(0,_0,_0,_0.75),_transparent,_rgba(0,_0,_0,_0.75))] [mask-repeat:repeat] [mask-size:140px]" style="--bg-img:url({{ gs('assets/image/others/Stars-2.svg') }})"></div>
           <div class="builder-partner-panel-wrapper bg-center bg-no-repeat bg-cover lg:bg-contain transition-all duration-300 [height:100dvh]" :class="{
            '!hidden': !__o_feature('feature.ai_pages_sections'),
           }" style="background-image:url({{ gs('assets/image/others/pattern.png') }})">
              <div class="-panel-head">
                <a x-on:click="__close_page" class="absolute appearance-none select-none top-2 right-4 p-2 text-foreground-500 rounded-full hover:bg-gray-200 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
                    <i class="fi fi-rr-cross-small text-base"></i>
                </a>
              </div>
              <div class="flex flex-col h-full w-[100%] px-6 pb-2">

                 <div class="flex flex-1 flex-col justify-end">
                    <div class="flex flex-col gap-2 pt-12 -chat-body h-full">
                        
                        <div x-show="!generateAiId.uuid" class="h-full my-auto">
                            <div class="flex flex-col justify-center items-center px-[20px] py-[60px] my-auto">
                                {!! __i('interface-essential', 'menu-block-checkmark', 'w-14 h-14') !!}
                                <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                                   {!! __t('Start by clicking on a section to edit it with Ai.') !!}
                                </p>
                            </div>
                        </div>
                        <h2 class="text-center text-lg mb-3">
                            <i class="ph ph-sparkle"></i> {{ __('Rewrite with AI') }}
                        </h2>

                        <div x-show="generateAiId.uuid">
                            <div class="page-subpanel-section !h-auto !pb-0 px-3" x-intersect="$store.builder.rescaleDiv($root);">
                                <div class="page-type-options !mb-0 !bg-[var(--yena-colors-whiteAlpha-600)]">
                                    <div class="page-type-item !overflow-y-auto !h-[130px]">
                                        <div class="container-small edit-board !origin-[0px_0px] !h-full">
                                            <div class="card">
                                                <div class="card-body" wire:ignore>
                                                    <div class="card-body-inner">
                                                        <template x-if="section.uuid">
                                                            <div x-bit="'section-' + section.section" x-data="{section:section}"></div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="!hidden">
                                        <button class="btn !rounded-full">{{ __('Add Section') }}</button>
                                        <button class="btn"></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="" x-show="generateAiId.uuid">
                            <div class="flex items-start justify-between flex-col gap-2 mb-0 md:items-center md:flex-row">
                                <div class="flex items-center flex-row gap-2 max-w-[100%] overflow-x-hidden">
                                    <button class="yena-button-o !bg-white !border !border-solid !border-[var(--yena-colors-gray-200)] w-[100%]" type="button" x-tooltip="tippyCategory">
                                        <span x-text="aiContent.category"></span>
                                        <span class="--icon ml-2 !mr-0">
                                            <i class="ph ph-caret-down"></i>
                                        </span>
                                    </button>
                                    <button class="yena-button-o !bg-white !border !border-solid !border-[var(--yena-colors-gray-200)] w-[100%] !px-8" type="button" x-tooltip="tippyTranslate">
                                        <span class="--icon">
                                            <i class="ph ph-translate"></i>
                                        </span>
                                        <span x-text="aiContent.textLanguage" class="capitalize"></span>
                                        <span class="--icon !mr-0 !ml-2">
                                            <i class="ph ph-caret-down"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center flex-col gap-4 py-4">
                                <div class="yena-form-group" x-data="{scrollHeight:5}">
                                    <textarea type="text" :style="{
                                        'height': scrollHeight + 'px'
                                    }" @input="scrollHeight-0;scrollHeight=$event.target.scrollHeight" x-model="aiContent.textPrompt" placeholder="{{ __('Tell us more... (e.g., We offer digital marketing services for small businesses)') }}" class="!px-[1rem] !rounded-lg !shadow-lg md:!text-[var(--yena-fontSizes-lg)] focus:!shadow-lg bg-white w-[100%] resize-none min-h-[60px] max-h-[300px]"></textarea>
                                </div>
                                
                                <div class="flex flex-col gap-4 w-[100%]">
                                    <div class="w-[100%]">

                                        <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-[100%] h-[var(--yena-sizes-10)]">
                                            <hr class="opacity-60 [border-image:initial] border-solid w-[100%] border-[var(--yena-colors-blackAlpha-400)]">
                                            <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Images') }}</span>
                                            <hr class="opacity-60 [border-image:initial] border-solid w-[100%] border-[var(--yena-colors-blackAlpha-400)]">
                                        </div>
                    
                                        <div class="inline-flex w-[100%]">
                                            <button class="yena-button-o !border !border-solid !border-[var(--yena-colors-gray-200)] w-[100%] !rounded-r-none" type="button" @click="aiContent.generateImages='none'" :class="{
                                                '!text-[var(--yena-colors-trueblue-500)] ![box-shadow:none] !bg-[var(--yena-colors-trueblue-50)]': aiContent.generateImages == 'none',
                                                '!bg-[var(--yena-colors-gradient-light)]': aiContent.generateImages !== 'none',
                                            }"> 
                                                <div class="--icon">
                                                    <i class="ph ph-prohibit"></i>
                                                </div>
                                                <span>{{ __('None') }}</span>
                                            </button>
                                            <button class="yena-button-o !border !border-solid !border-[var(--yena-colors-gray-200)] w-[100%] !rounded-r-none !rounded-l-none" type="button" @click="aiContent.generateImages='unsplash'" :class="{
                                                '!text-[var(--yena-colors-trueblue-500)] ![box-shadow:none] !bg-[var(--yena-colors-trueblue-50)]': aiContent.generateImages == 'unsplash',
                                                '!bg-[var(--yena-colors-gradient-light)]': aiContent.generateImages !== 'unsplash',
                                            }"> 
                                                <span>{{ __('Unsplash') }}</span>
                                            </button>
                                            <button class="yena-button-o !bg-[var(--yena-colors-gradient-light)] !border !border-solid !border-[var(--yena-colors-gray-200)] w-[100%] !rounded-l-none" type="button" @click="aiContent.generateImages='pexels'" :class="{
                                                '!text-[var(--yena-colors-trueblue-500)] ![box-shadow:none] !bg-[var(--yena-colors-trueblue-50)]': aiContent.generateImages == 'pexels',
                                                '!bg-[var(--yena-colors-gradient-light)]': aiContent.generateImages !== 'pexels',
                                            }"> 
                                                
                                                <span>{{ __('Pexels') }}</span>
                                            </button>
                                            {{-- <button class="yena-button-o !bg-[var(--yena-colors-gradient-light)] !border !border-solid !border-[var(--yena-colors-gray-200)] w-[100%] !rounded-l-none pointer-events-none opacity-40" type="button">
                                                <div class="--icon">
                                                    <i class="ph ph-sparkle"></i>
                                                </div>
                                                
                                                <span>{{ __('Ai') }}</span>
                                            </button> --}}
                                        </div>
                                    </div>

                                    <div x-show="aiContent.generateImages !== 'none'">
                                        <input type="text" x-model="aiContent.imageQuery" placeholder="{{ __('Image to search... (e.g., Cats, cars, animals)') }}" class="!px-[1rem] !rounded-lg !shadow-lg md:!text-[var(--yena-fontSizes-lg)] focus:!shadow-lg bg-white w-[100%] resize-none min-h-[60px] max-h-[300px]">
                                    </div>
                                </div>
                                <div class="flex flex-col gap-[var(--yena-space-4)] w-[100%]">
                                    <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-[100%] h-[var(--yena-sizes-10)]">
                                        <hr class="opacity-60 [border-image:initial] border-solid w-[100%] border-[var(--yena-colors-blackAlpha-400)]">
                                        <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Try it out') }}</span>
                                        <hr class="opacity-60 [border-image:initial] border-solid w-[100%] border-[var(--yena-colors-blackAlpha-400)]">
                                    </div>
                
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <template x-for="(item, index) in randomPrompt2" :key="index">
                                         <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-start !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm" type="button" @click="aiContent.textPrompt = item">
            
                                                <p class="text-[var(--yena-fontSizes-sm)] text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)] overflow-hidden overflow-ellipsis whitespace-normal break-words [--yena-line-clamp:2] md:[--yena-line-clamp:3] [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:var(--yena-line-clamp)] flex-1" x-text="item"></p>
            
                                                <span class=" ml-2 !mr-0 items-center">
                                                    <i class="ph ph-plus text-sm text-[color:#0000003d]"></i>
                                                </span>
                                            </button>
                                        </template>
                                    </div>
                
                                    <div class="flex justify-center">
                                        <button class="yena-button-o !bg-white md:!h-[var(--yena-sizes-10)] md:!min-w-[var(--yena-sizes-10)] md:!text-base md:!px-4 shadow-md bg-[var(--yena-colors-gradient-light)] border border-solid border-[var(--yena-colors-gray-200)]" type="button" @click="randomPrompt2 = $store.builder.getTwoRandomValues(randomPrompt)">
                                            <div class="--icon">{!! __i('Arrows, Diagrams', 'Arrow, Shuffle', 'w-5 h-5') !!}</div>
                                            {{ __('Shuffle') }}
                                        </button>
                                    </div>
                                </div>
                            </div>


                            <p class="mt-4 mb-4 text-[var(--t-m)] text-center leading-[var(--l-body)] text-[var(--c-mix-3)] w-3/5 ml-auto mr-auto">{{ __('Are you sure you want to continue? Section will be overwriten.') }}</p>

                            
                            <div class="pl-[var(--s-2)] pr-[var(--s-2)] pt-[0] pb-[var(--s-2)] flex gap-2 w-[100%]">
                                <template x-if="loading">
                                    <div class="loader-animation-container my-2 mx-auto">
                                      <div class="inner-circles-loader"></div>
                                   </div>
                                </template>
                                <button class="btn btn-medium neutral !h-[calc(var(--unit)*_4)] !w-[100%]" type="button"  @click="sectionEditing" :class="{
                                    '!hidden': loading
                                }"><i class="ph ph-star-four"></i> {{ __('Generate') }}</button>
                            </div>
                            {{-- <button class="yena-button-o !bg-transparent !ml-auto hover:!bg-[var(--yena-colors-blackAlpha-50)]" :class="{
                                '!hidden': loading
                            }" type="button">
                                <span class="--icon">
                                    <i class="ph ph-coins"></i>
                                </span>
                                <span class="capitalize">430 {{ __('credits') }}</span>
                            </button> --}}
                        </div>

                    </div>
                 </div>
                 {{-- <div class="flex flex-col gap-2 sticky bottom-0 pt-4 pb-2 bg-[var(--yena-colors-gray-50)]"></div> --}}
                 <div class="inline-flex absolute left-4 top-1"></div>
              </div>
           </div>
        </div>
     </div>

    @script
        <script>
            Alpine.data('__ai_chat_alpine', () => {
                return {
                    loading: false,
                    message: 'How are you',
                    sectionEditing: null,
                    section: [],
                    randomPrompt: @entangle('randomPrompt'),
                    randomPrompt2: [],
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

                    styles(){
                        var site = this.site;
                        return this.$store.builder.generateSiteDesign(site);
                    },
                    
                    __opened_create_page(){
                        
                        // if(this.generateAiId || this.__section_create_page){
                        //     return true;
                        // }
                        return false;
                    },

                    __close_page(){
                        this.generateAiId = false;
                        // this.__section_create_page = false;
                    },

                    sectionEditing(){
                        this.loading = true;

                        this.$store.builder.generateAi(this.generateAiId, this.aiContent)
                        // var event = new CustomEvent('reaiSection:' + this.generateAiId.uuid, {
                        //    detail: {
                        //     prompt: this.aiContent,
                        //     section: this.generateAiId,
                        //    },
                        // });
                        // window.dispatchEvent(event);
                    },

                    init(){
                        let $this = this;
                        $this.randomPrompt2 = $this.$store.builder.getTwoRandomValues($this.randomPrompt);

                        this.$watch('generateAiId', (value) => {
                            $this.section = [];

                            setTimeout(() => {
                                $this.section = value;
                            }, 100);
                        });

                        window.addEventListener('stopAiLoader', (event) => {
                            $this.loading = false;
                        });
                        
                        // let ai = new Ai(this.generateAiId);
                        // ai.setPrompt(this.aiContent);
                        // ai.run();
                        this.tippy.appendTo = this.$root;

                        this.tippyTone = {
                            ...this.tippy,
                            content: this.$refs.tone_template.innerHTML,
                        }
                        this.tippyTranslate = {
                            ...this.tippy,
                            content: this.$refs.translate_template.innerHTML,
                        }
                        this.tippyCategory = {
                            ...this.tippy,
                            placement: 'bottom',
                            content: this.$refs.category_template.innerHTML,
                        }
                    }
                }
            });
        </script>
    @endscript
</div>