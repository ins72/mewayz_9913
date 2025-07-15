
<?php

    use function Livewire\Volt\{state, mount, on, placeholder};

    state(['site']);

    state([
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
?>

<div>

    <div class="-mobile-plane builder--page" :class="{'active': renderMobile}">

        <div x-data="builder__index_post" wire:ignore :data-theme="site.settings.siteTheme && site.settings.siteTheme !== '-' ? site.settings.siteTheme : 'light'">
            <template x-ref="contextTemplate">
               <div class="yena-menu-list !w-full">
                  <div class="px-4">
                     <p class="yena-text"></p>
         
                     {{-- <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">Created September 18th, 2023</p> --}}
                     {{-- <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">by Jeff Jola</p> --}}
                  </div>
                  @if (!$site->canEdit())
                  <a href="{{ route('console-index') }}" class="yena-menu-list-item">
                     <span>{{ __('Home') }}</span>
                  </a>
                  @endif
    
                  @if ($site->canEdit())
                  <a @click="duplicateSection(item); $dispatch('hideTippy');" class="yena-menu-list-item">
                     <div class="--icon">
                        {!! __icon('interface-essential', 'copy-duplicate-object-add-plus', 'w-5 h-5') !!}
                     </div>
                     <span>{{ __('Duplicate') }}</span>
                  </a>
                  <hr class="--divider">
                  <a @click="navigateSection(item, 'style'); $dispatch('hideTippy');" class="yena-menu-list-item cursor-pointer">
                     <div class="--icon">
                        {!! __icon('Design Tools', 'color-palette_1', 'w-5 h-5') !!}
                     </div>
                     <span>{{ __('Style') }}</span>
                  </a>
                  <a @click="navigateSection(item, 'section'); $dispatch('hideTippy');" class="yena-menu-list-item cursor-pointer">
                     <div class="--icon">
                        {!! __icon('interface-essential', 'image-picture', 'w-5 h-5') !!}
                     </div>
                     <span>{{ __('Background') }}</span>
                  </a>
                  <a @click="generateAiId=item;showOptions=false;$dispatch('hideTippy');" class="yena-menu-list-item cursor-pointer">
                     <div class="--icon">
                        <i class="ph ph-sparkle text-lg"></i>
                     </div>
                     <span>{{ __('Ai') }}</span>
                  </a>
                  <hr class="--divider">
                  <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="deleteSectionId=item;showOptions=false; $dispatch('hideTippy');">
                     <div class="--icon">
                        {!! __icon('interface-essential', 'trash-bin-delete', 'w-5 h-5') !!}
                     </div>
                     <span>{{ __('Delete') }}</span>
                  </a>
                  @endif
              </div>
            </template>

            <div :style="styles()" wire:ignore>
            
                {{-- <template x-if="!currentPage().hide_header">
                    <div @click="navigatePage('section::header')">
                        <x-livewire::sections.header.viewComponent />
                    </div>
                </template> --}}
    
                <div wire:ignore>
                    <div>
                        <template x-if="post">
                            <x-livewire::sections.posts.alpineView />
                        </template>

                        {{-- <template x-for="(item, index) in getSections()" :key="item.uuid" x-ref="section_template">
                            <div class="w-[100%] sortable-arrows-item box" :class="{
                                'ai-editing-section': generateAiId && generateAiId.uuid == item.uuid
                            }" :data-id="item.uuid" @click="clickedSection(item)" x-data x-init="contextMenu($root);" x-intersect="initSectionItems(item, $root)">
                                @if ($site->canEdit())
                                <div class="block-options" x-data="{showOptions: false}" @click="$event.stopPropagation()">
                                    <div class="top-right-block-options shadow-lg !border-0zz">
                                       <div class="options active">
    
                                        <div class="block-menu handle">
                                             {!! __i('custom', 'grip-dots-vertical', '!w-[10px] !h-[10px]') !!}
                                        </div>
    
    
                                          <div id="control" class="block-menu" @click="showOptions=!showOptions">
                                            {!! __i('--ie', 'dots-menu') !!}
                                          </div>
                                       </div>
                                    </div>
                                    <template x-if="showOptions">
                                        <div class="all-more-block-options">
                                            <div class="more-block-options !bg-blackz !text-whitez">
                                               <ul>
                                                  <li @click="editSection(item);showOptions=false;">{{ __('Edit') }}</li>
                                                  <li @click="generateAiId=item;showOptions=false;$dispatch('hideTippy');">{{ __('Ai') }}</li>
                                                  <li class="more-block-options-delete" @click="deleteSectionId=item;showOptions=false;">{{ __('Delete') }}</li>
                                               </ul>
                                            </div>
                                         </div>
                                    </template>
                                 </div>
                                 @endif
                                 
                                <div class="section-loop-content">
                                    <div x-bit="'section-' + item.section" x-data="{section:item}"></div>
                                </div>
    
                                @if ($site->canEdit())
                                <div class="items-center justify-center relative z-[2] w-[100%] p-[1rem] flex opacity---0 [transition-property:opacity] builder-section-add-wrapper" @click="$event.stopPropagation()">
                                    <div class="inline-flex [box-shadow:var(--yena-shadows-md)] rounded-[var(--yena-radii-md)] opacity-100 [transition-property:opacity]">
                                        <a class="yena-button-o !rounded-md !shadow-none !min-w-[var(--yena-sizes-12)] ![transition-property:none] !border-[var(--yena-colors-gray-100)] !text-[var(--yena-colors-gray-800)] !bg-[var(--yena-colors-gradient-light)]" @click="navigatePage('section');_create_position=item;">
                                            <i class="ph ph-plus text-base"></i>
                                        </a>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </template> --}}
                    </div>
                </div>
                
            
                {{-- <div @click="navigatePage('section::footer')">
                    <x-livewire::sections.footer.viewComponent />
                </div> --}}
            </div>
        </div>
        @script
            <script>
                Alpine.data('builder__index_post', () => {
                   return {
    
                        contacts: {!! collect(config('sections.contact'))->toJson() !!},
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
                        aiContent: {
                            textContent: 'generate',
                            textAmount: 'brief',
                            textTone: 'casual',
                            textLanguage: 'english',
                        },
                        tippy: {
                            allowHTML: true,
                            maxWidth: 360,
                            interactive: true,
                            trigger: 'click',
                            animation: 'scale',
                        },
                        tippyTone: {},
                        tippyTranslate: {},
                        aiTones: @entangle('aiTone'),
                        aiLanguages: @entangle('aiLanguage'),
                        __section_loaded (el){
    
                        },
                        clickedSection(item){
    
                            if(window.rightClicked){
                                this.$event.stopPropagation();
                                return;
                            }
    
                            if(generateAiId && !generateAiId.uuid){
                                this.generateAiId=item;
                                // this.editSection(item);
                                return;
                            }
    
                            editSection(item);
                        },
                        initTheme(){
                            if(this.site.settings.siteTheme && this.site.settings.siteTheme !== '-'){
                                this.siteTheme = this.site.settings.siteTheme;
                            }
                        },
    
                        duplicateSection(section){
                            let position = parseInt(section.position)+1;
                            let $this = this;
                            let $sections = $this.getSections();
                            
                            if(section.items.length > 0){
                                let $items = [];
                                section.items.forEach((_i, index) => {
                                    let $itemUUID = $this.$store.builder.generateUUID();
    
                                    let $newItem = {
                                        uuid:  $itemUUID,
                                        ..._i
                                    };
                    
                                    $items.push($newItem);
                                });
    
                                section.items = $items;
                            }
    
                            let $section = {
                                ...section,
                                uuid: $this.$store.builder.generateUUID(),
                                position: position
                            };
    
                            $section = $this.insertSectionAt(section, $section);
                            
                            let $array = [];
                            $this.getSections().forEach((s) => {
                                //
                                let $new = {
                                    uuid: s.uuid,
                                    position: s.position,
                                };
    
                                $array.push($new);
                            });
    
                            $this.$store.builder.savingState = 0;
                            $this.$dispatch('section::create', {
                                section: $section
                            });
                            let event = new CustomEvent("builder::sort_sections", {
                                detail: {
                                    sections: $array,
                                }
                            });
    
                            window.dispatchEvent(event);
                        },
    
                        generateAiSection(){
                            let section = this.generateAiId.section;
    
    
                            let ai = new window[section](this.generateAiId);
                            ai.setPrompt(this.aiContent);
                            ai.run();
    
    
                            this.generateAiId=false;
                        },
    
                        initSectionItems(section, $root){
                            let $this = this;
                            let items = $root.querySelectorAll('.yena-section-items');
                            items.forEach((element) => {
                                var $id = element.getAttribute('data-id');
                                element.addEventListener('click', function(e) {
                                    e.stopPropagation();
                                    $this.navigateSectionItem(section, $id);
                                })
                            });
                        },
    
                        contextMenu($root){
                            let $this = this;
                            let instance = window.tippy($root, {
                                placement: 'right-start',
                                trigger: 'manual',
                                content: () => $this.$refs.contextTemplate.innerHTML,
                                allowHTML: true,
                                appendTo: $root,
                                interactive: true,
                                arrow: false,
                                offset: [0, 0],
                                onHide(instance) {
                                    window.rightClicked = false;
                                },
                            });
    
                            $root.addEventListener('contextmenu', (event) => {
                                event.preventDefault();
                                event.stopPropagation();
    
                                window.rightClicked = true;
    
                                instance.setProps({
                                    getReferenceClientRect: () => ({
                                        width: 0,
                                        height: 0,
                                        top: event.clientY,
                                        bottom: event.clientY,
                                        left: event.clientX,
                                        right: event.clientX,
                                    }),
                                });
    
                                instance.show();
                            });
                        },
    
                        styles(){
                            var site = this.site;
                            return this.$store.builder.generateSiteDesign(site);
                        },
                        deleteSection(item){
    
                            this.sections.forEach((e, index) => {
                                if(item.uuid == e.uuid){
                                    this.sections.splice(index, 1);
                                }
                            });
    
                            this.$dispatch('section::delete', {
                                id: item.uuid
                            });
                            this.deleteSectionId=false;
                        },
                        initSort(){
                            var $this = this;
                            this.tippy.appendTo = this.$root;
    
                            this.tippyTone = {
                                ...this.tippy,
                                content: this.$refs.tone_template.innerHTML,
                            }
                            this.tippyTranslate = {
                                ...this.tippy,
                                content: this.$refs.translate_template.innerHTML,
                            }
    
                            let $wrapper = this.$root.querySelector('.sortable_section_wrapper');
                            let $template = this.$root.querySelector('[x-ref="section_template"]');
    
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
                                    handle: '.handle',
                                    onEnd: (event) => {
                                        let $array = $this.getSections();
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
    
                        init(){
                            let __ = this;
                            this.initTheme();

                            this.$watch('generatePost', (value) => {
                                __.post = value;
                            });
    
                            // window.addEventListener('section::addAfter', (event) => {
                            //     let detail = event.detail;
                            //     // __.insertSectionAt;
                            // });
    
                            // // let lol = new window.banner(this.getSections()[0]);
                            // // lol.setPrompt(this.aiContent);
                            // // lol.run();
                            // // let $sections = this.getSections();
                            // // this.$dispatch('builder::processSectionToPages', {
                            // //     data: $sections
                            // // });
                            
                            // this.initSort();
                        }
                   }
                });
            </script>
        @endscript
    </div>
</div>