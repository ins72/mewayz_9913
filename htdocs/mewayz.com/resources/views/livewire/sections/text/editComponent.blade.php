<div>

    <div x-data="builder__sectionText">
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
                        <li >{{ __('Text') }}</li>
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
                                <x-livewire::sections.text.partial.edit.content/>
                            </div>
                            <div x-cloak x-show="__tab == 'style'" data-tab-content>
                                <x-livewire::sections.text.partial.edit.style />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  @script
  <script>
      Alpine.data('builder__sectionText', () => {
         return {
            autoSaveTimer: null,
            __tab: 'content',
            __page: '-',
            skeleton: {
                label: '',
                title: '',
            },
            editor: null,

            itemSkeleton: {

            },
            registerEvents(){
                let $this = this;
                window.addEventListener('section:content:' + this.section.uuid, (event) => {
                    $this.__page = '-';
                    $this.__tab = 'content';
                });
                window.addEventListener('section:style:' + this.section.uuid, (event) => {
                    $this.__page = '-';
                    $this.__tab = 'content';
                });
                window.addEventListener('section:section:' + this.section.uuid, (event) => {
                    $this.__page = '-';
                });
               window.addEventListener("reaiSection:" + this.section.uuid, (event) => {
                    let detail = event.detail;

                    $this.generateSection = detail.section;
                    $this.aiContent = detail.prompt;

                    $this.regenerateAi();
               });
            },

            init(){
                this.registerEvents();
               //if(!this.section) this.section = this.skeleton;
               var $this = this;

               this.editor = new window.SimpleMDE({
                element: this.$refs.textarea_markdown,
                toolbar: [
                    {
                        name: "bold",
                        action: window.SimpleMDE.toggleBold,
                        className: "fa fa-bold",
                        title: "Bold",
                        default: true
                    },
                    {
                        name: "italic",
                        action: window.SimpleMDE.toggleItalic,
                        className: "fa fa-italic",
                        title: "Italic",
                        default: true
                    },
                    {
                        name: "strikethrough",
                        action: window.SimpleMDE.toggleStrikethrough,
                        className: "fa fa-strikethrough",
                        title: "Strikethrough"
                    },
                    {
                        name: "heading",
                        action: window.SimpleMDE.toggleHeadingSmaller,
                        className: "fa fa-header",
                        title: "Heading",
                        default: true
                    },
                    {
                        name: "unordered-list",
                        action: window.SimpleMDE.toggleUnorderedList,
                        className: "fa fa-list-ul",
                        title: "Generic List",
                        default: true
                    },
                    {
                        name: "ordered-list",
                        action: window.SimpleMDE.toggleOrderedList,
                        className: "fa fa-list-ol",
                        title: "Numbered List",
                        default: true
                    },
                    {
                        name: "clean-block",
                        action: window.SimpleMDE.cleanBlock,
                        className: "fa fa-eraser fa-clean-block",
                        title: "Clean block"
                    },
                    {
                        name: "link",
                        action: window.SimpleMDE.drawLink,
                        className: "fa fa-link",
                        title: "Create Link",
                        default: true
                    },
                    {
                        name: "image",
                        action: window.SimpleMDE.drawImage,
                        className: "fa fa-picture-o",
                        title: "Insert Image",
                        default: true
                    },
                    {
                        name: "horizontal-rule",
                        action: window.SimpleMDE.drawHorizontalRule,
                        className: "fa fa-minus",
                        title: "Insert Horizontal Line"
                    },
                ],
               });

               this.editor.value(this.section.content.subtitle);
               this.editor.codemirror.on('change', () => {
                this.section.content.subtitle = this.editor.value();
               });

            //    this.$watch('section' , (value, _v) => {
            //         $this.editor.value($this.section.content.subtitle);
            //    });
                if(this.section.section_settings === undefined || this.section.section_settings == null){
                    this.section.section_settings = {
                        color: 'transparent',
                    };
                }
                
            }
         }
      });
  </script>
  @endscript
</div>