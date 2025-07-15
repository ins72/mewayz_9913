
<?php

    use function Livewire\Volt\{state, mount, on, placeholder};

    state(['site']);
    state([
        'page',
        'sections' => [],
    ]);

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

<div class="-mobile-plane builder--page" :class="{'active': renderMobile}">

    <div x-data="builder__index" wire:ignore :data-theme="site.settings.siteTheme && site.settings.siteTheme !== '-' ? site.settings.siteTheme : 'light'">
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
        
            <template x-if="!currentPage().hide_header">
                <div @click="navigatePage('section::header')">
                    <x-livewire::sections.header.viewComponent />
                </div>
            </template>


            {{-- <div class="h-[calc(44px_+_var(--logo-height))] lg:h-[75px] w-[100%] hidden [background:var(--background)]" :class="{
                '!block': site.header.sticky || site.header._float,
            }"></div> --}}

            <div wire:ignore>
                <template x-if="getSections().length == 0">
                    <x-empty-state :title="__('Craft a page in seconds.')" :desc="__('It\'s a little quiet in here. Create a sedction to get started.')" image="17.png">
                    
                    <div class="flex flex-row gap-4 mt-4 lg:flex-row">
                       <a @click="navigatePage('section')" class="cursor-pointer yena-button-stack">
                          <div class="--icon">
                             {!! __icon('interface-essential', 'menu-block-checkmark', 'w-6 h-6') !!}
                          </div>
           
                          {{ __('Create Section') }}
                       </a>
                    </div>
                 </x-empty-state>
                </template>
                <div class="sortable_section_wrapper">
                    <template x-for="(item, index) in getSections()" :key="item.uuid" x-ref="section_template">
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
                                              {{-- <li>{{ __('Duplicate') }}</li> --}}
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
                                    {{-- <a class="yena-button-o !rounded-md !shadow-none !min-w-[var(--yena-sizes-12)] ![transition-property:none] !border-[var(--yena-colors-gray-100)] !text-[var(--yena-colors-gray-800)] !bg-[var(--yena-colors-gradient-light)]">
                                        <i class="ph ph-sparkle text-base"></i>
                                    </a> --}}
                                </div>
                            </div>
                            @endif
                        </div>
                    </template>
                </div>
            </div>
            
        
            <div @click="navigatePage('section::footer')">
                <x-livewire::sections.footer.viewComponent />
            </div>
        </div>

        <template x-ref="tone_template">
            <div class="yena-menu-list !w-full">
                <template x-for="(item, index) in aiTones" :key="index">
                    <a @click="aiContent.textTone = item.prompt" :class="{
                        '!bg-[var(--yena-colors-gray-100)] !border-[var(--yena-colors-gray-200)]': aiContent.textTone == item.prompt,
                    }" class="yena-menu-list-item">
                       <div class="--icon" x-html="item.icon"></div>
                       <span class="text-sm" x-text="item.name"></span>
                    </a>
                </template>
           </div>
        </template>
    
        <template x-ref="translate_template">
          <div class="yena-menu-list !w-full">
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
            <div class="overlay backdrop delete-overlay" :class="{
                '!block': deleteSectionId
            }" @click="deleteSectionId=false">
                <div class="delete-site-card !border-0 !rounded-2xl !shadow-lg" @click="$event.stopPropagation()">
                   <div class="overlay-card-body !rounded-2xl">
                      <h2>{{ __('Delete Section?') }}</h2>
                      <p class="mt-4 mb-4 text-[var(--t-m)] text-center leading-[var(--l-body)] text-[var(--c-mix-3)] w-3/5 ml-auto mr-auto">{{ __('Are you sure you want to delete this section? Once deleted, you will not be able to restore it.') }}</p>
                      <div class="card-button pl-[var(--s-2)] pr-[var(--s-2)] pt-[0] pb-[var(--s-2)] flex gap-2">
                         <button class="btn btn-medium neutral !h-[calc(var(--unit)*_4)]" type="button" @click="deleteSectionId=false">{{ __('Cancel') }}</button>
        
                         <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-[calc(var(--unit)*_4)]" @click="deleteSection(deleteSectionId)">{{ __('Yes, Delete') }}</button>
                      </div>
                   </div>
                </div>
             </div>
        </template>
    </div>
    @script
        <script>
            Alpine.data('builder__index', () => {
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

                    // insertSectionAt(section, $new){
                    //     let $this = this;
                    //     let $sections = $this.getSections();

                    //     let $key = 0;
                    //     $sections.forEach((s, i) => {
                    //         if(s.uuid == section.uuid){
                    //             $key = (i + 1);
                    //         }
                    //     });
                    //     $this.sections.splice($key, 0, $new); // Insert at position section
                    //     $this.getSections().forEach((s, i) => {
                    //         let $s = $this.sections.filter(obj => obj.uuid === s.uuid)[0];
                    //         $s.position = i;

                    //         if(s.uuid == $new.uuid){
                    //             $new.position = i;
                    //         }
                    //     });

                    //     return $new;
                    // },

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

                    generateComponent(component){
                        component = `o-section-${component}`;
                        return `<${component}></${component}>`;
                    },

                    currentPage(){
                        var page = this.pages[0];

                        this.pages.forEach((e, index) => {
                            if(e.uuid == this.site.current_edit_page) page = e;
                        });
                        return page;
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

                    sortReset(){
                        let $this = this;
                        document.querySelectorAll('.sortable-arrows').forEach(element => {
                           element.querySelectorAll('.sortable-arrows-item').forEach((e, index) => {
                              e.setAttribute('data-sort', index);
                           });
                        });
                    },

                    sortUpdated(){
                        let $this = this;
                        let $elements = document.querySelectorAll('.sortable-arrows .sortable-arrows-item');
                        let $array = $this.getSections();
                        let event = new CustomEvent("builder::sort_sections", {
                            detail: {
                                sections: $array,
                            }
                        });

                        window.dispatchEvent(event);
                    },

                    sortUp(){
                        let $this = this;
                        $this.sortReset();

                        let $wrapper = document.querySelector('.sortable-arrows');
                        let $elements = $wrapper.querySelectorAll('.sortable-arrows-item');
                        let $item = $this.$event.target.parentNode.closest('.sortable-arrows-item');
                        let $array = $this.getSections();
                        let steps = Alpine.raw($array);

                        let prev = $item.previousElementSibling;
                        if(prev == null) return;
                        
                        let oldSort = $item.getAttribute('data-sort');
                        let newSort = oldSort - 1;

                        let moved_step = steps.splice(oldSort, 1)[0]
                        steps.splice(newSort, 0, moved_step);

                        let keys = []
                        steps.forEach((step, i) => {
                            keys.push(step.uuid);

                            $array.forEach((x, _i) => {
                                if(x.uuid == step.uuid) x.position = i;
                            });
                        });
                        $this.sortUpdated();
                    },

                    sortDown(){
                        let $this = this;
                        $this.sortReset();

                        let $wrapper = document.querySelector('.sortable-arrows');
                        let $elements = $wrapper.querySelectorAll('.sortable-arrows-item');
                        let $item = $this.$event.target.parentNode.closest('.sortable-arrows-item');
                        let $array = $this.getSections();
                        let steps = Alpine.raw($array);

                        let oldSort = $item.getAttribute('data-sort');
                        let newSort = oldSort + 1;

                        let next = $item.nextElementSibling;
                        if(next == null) return;

                        let moved_step = steps.splice(oldSort, 1)[0]
                        steps.splice(newSort, 0, moved_step);

                        let keys = []
                        steps.forEach((step, i) => {
                            keys.push(step.uuid);

                            $array.forEach((x, _i) => {
                                if(x.uuid == step.uuid) x.position = i;
                            });
                        });
                        $this.sortUpdated();
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
                    saveForm(){

                    },

                    init(){
                        let __ = this;
                        this.initTheme();

                        window.addEventListener('section::addAfter', (event) => {
                            let detail = event.detail;
                            // __.insertSectionAt;
                        });

                        // let lol = new window.banner(this.getSections()[0]);
                        // lol.setPrompt(this.aiContent);
                        // lol.run();
                        // let $sections = this.getSections();
                        // this.$dispatch('builder::processSectionToPages', {
                        //     data: $sections
                        // });
                        
                        this.initSort();
                    }
               }
            });
        </script>
    @endscript
</div>
