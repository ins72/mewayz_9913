@props([
    'disabled' => false,
])

<div x-data="builder_input_component" class="w-[100%]">
    {{ $slot }}


    <div class="mt-1">
        <label class="input-group yena--switcher">
           <div class="switchWrapper">
              <input type="checkbox" x-ref="openNewTab" class="switchInput">
              <div class="switchLabel">{{ __('Open in new tab') }}</div>

              <div class="slider"></div>
           </div>
        </label>
    </div>
    <div class="relative flex items-center w-[100%] mb-0 mt-[var(--s-1)] !border-[1px] !border-solid !border-[var(--c-mix-1)] rounded-[5px]" :class="{
        '!hidden': selected == true || _query.length == 0,
    }">
        <div class="flex-[70%] flex flex-col">
            <ul class="w-[100%] [list-style:none] font-[var(--f-base)] text-[12px] text-[var(--c-mix-3)] overflow-y-scroll rounded-[var(--r-small)]">

                <template x-for="(item, index) in _query" :key="index">
                    <li class="leading-[var(--l-body)] flex items-center justify-between cursor-pointer text-[color:var(--foreground)] px-[var(--s-1)] py-[0] h-[32px] rounded-none text-[12px] hover:text-[color:var(--foreground)] hover:bg-[var(--c-mix-1)]" @click="generateLink(item)">
                        <span class="max-w-[180px] whitespace-nowrap overflow-hidden overflow-ellipsis">
                            <span x-text="item.name"></span>
                            <template x-if="isSection(item)">
                                <span x-text="' · ' + item.section_name"></span>
                            </template>
                        </span>
                        <span class="max-w-[180px] whitespace-nowrap overflow-hidden overflow-ellipsis text-[color:var(--c-mix-2)]" x-text="isSection(item) ? '{{ __('Section') }}' : '{{ __('Page') }}'"></span>
                    </li>
                </template>
            </ul>
        </div>
    </div>

    @script
    <script>
        
        Alpine.data('builder_input_component', () => {
            return {
                //sections: this.sections,

                _query: [],
                link: '',
                selected: true,

                isSection(item){
                    if(item.section) return true;
                    return false;
                },
                generateLink(item){
                    var page = item;
                    if(this.isSection(item) && item.page_uuid) page = this._get_page(item.page_uuid);
                    var url = page.slug;
                    var slug = page.slug;

                    this.pages.forEach((x, i) => {
                        if(x.default && x.id == page.id){
                            slug = '';
                        };
                    });

                    url = `/${slug}`;

                    
                    if(this.isSection(item)){
                        url = url + `#section-${item.id}`;
                    }
                    var event = new Event('change');

                    this.$root.querySelector('input').value = url
                    this.$root.querySelector('input').dispatchEvent(event);
                    this.selected = true;
                },
                _get_page(page_id){
                    var page = false;
                    this.pages.forEach((x, i) => {
                        if(x.uuid == page_id){
                            page = x;
                        };
                    });

                    return page;
                },
                getPage(item){
                    var page = false;
                    this.pages.forEach((x, i) => {
                        if(x.uuid == item.page_id){
                            page = x;
                        };
                    });

                    return page;
                },
                filter(){
                    this.selected = false;
                    this._query = [];
                    this.initQuery();
                    var search = this.$root.querySelector('input').value;
                    this._query = this._query.filter((item) => {
                        if(item.name == undefined || item.name == null) return;
                        if(search == '' || search == null) return;
                        var name = item.name.toLowerCase();
                        return name.includes(search.toLowerCase());
                    });
                },
                // filteredQuery() {
                //     return this._query.filter((item) => {
                //         if(item.name == undefined || item.name == null) return;
                //         if(this.selected) return item;

                //         var name = item.name.toLowerCase();
                //         return name.includes(this.link.toLowerCase());
                //     });
                // },
                initQuery(){
                    this._query.push(...this.pages);
                    this._query.push(...this.sections);
                    // console.log(this.sections)

                    this._query.forEach((x, i) => {
                        if(this.isSection(x)) {
                            var page = this.getPage(x);
                            if(!page){
                                // console.log('NO PAGE', page, i)
                                this._query.splice(i, 1);
                            }

                            // console.log(i, x, page)

                            // if(x.content.title == undefined || x.content.title == null){
                            //     return;
                            // }

                            this._query[i].name = page.name;
                            this._query[i].section_name = x.content.title;
                            this._query[i].page_uuid = page.uuid;
                            // this._query[i].page = page;
                        }
                    });
                },
                set(path, value) {
                    var schema = this;  // a moving reference to internal objects within obj
                    var pList = path.split('.');
                    var len = pList.length;
                    for(var i = 0; i < len-1; i++) {
                        var elem = pList[i];
                        if( !schema[elem] ) schema[elem] = {}
                        schema = schema[elem];
                    }

                    schema[pList[len-1]] = value;
                },
                init(){
                    let $this = this;
                    let $model = this.$root.querySelector('input').getAttribute('x-model');
                    let $openTabModel = $model + "_tab";
                    Alpine.bind($this.$refs.openNewTab, {'x-model': $openTabModel});

                    // console.log($openTabModel)


                    Alpine.bind(this.$root.querySelector('input'), {'@change': function(){
                        var model = this.$event.target.getAttribute('x-model');

                        this.set(model, this.$event.target.value);
                        // console.log(this, this.item, this.$event.target.value)
                    }});




                    // var model = this.$root.querySelector('input').getAttribute('x-model');
                    // var parts = model.split(".")
                    // var stra = parts[0] + parts.slice(1).map(p => `['${p}']`).join("")

                    // this.set('section.settings.lol', 'sdfdsf')
                    // console.log('model', model, stra)
                    // this.$dispatch(this.$root.querySelector('input').getAttribute('data-event'), 'sdfsdf')
                    //console.log(this.[stra])
                    //this.initQuery();
                }
            }
        });
    </script>
    @endscript
</div>
