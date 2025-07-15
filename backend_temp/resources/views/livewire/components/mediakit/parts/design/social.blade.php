<div class="pt-[26px] px-[36px]">

    <div x-data="builder__design_social">

        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="rounded-[15px] w-[33px] h-[33px] flex items-center justify-center bg-gray-200 cursor-pointer" :class="{'!hidden': !createPage}">
                    <a @click="createPage=false">
                        <span>
                           {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                        </span>
                    </a>
                </div>
                <div class="text-xl font-extrabold tracking-[-1px]">{{ __('Social') }}</div>
            </div>
    
            <button type="button" class="yena-button-stack !text-sm !rounded-full !h-[2rem]" :class="{'!hidden': createPage}" @click="createPage=true">
                {{ __('+ Add Social') }}
             </button>
        </div>
    
        <div x-cloak x-show="!createPage">
            <div class="flex flex-col gap-2" x-ref="social_sortable_wrapper">
                <template x-for="(social, index) in window._.sortBy(site.socials, 'position')" :key="social.uuid" x-ref="social_sortable_template">

                    <div class="w-[100%] h-[66px] rounded-[15px] bg-[rgb(247,_247,_247)] opacity-100 cursor-pointer">
                        <div class="w-[100%] h-[66px] flex pl-[17px] pr-[15px] py-[0] items-center">
                           <div class="handle">
                              {!! __i('custom', 'grip-dots-vertical', '!w-[10px] !h-[10px] text-[color:#BDBDBD]') !!}
                           </div>
         
                           <div>
                                <div class="[box-shadow:0_4px_5.84px_hsla(0,0%,50.2%,.353)] rounded-[10px] w-[36px] h-[36px] ml-[13px] mr-[18px] my-[0] flex items-center justify-center">
                                    <i :class="socials[social.social].icon"></i>
                                </div>
                           </div>
                           
                           <div class="custom-content-input border-0 m-0 px-1 bg-white w-[100%] mr-2 rounded-full overflow-hidden">
                            <input type="text" x-model="social.link" :placeholder="socials[social.social].placeholder" class="w-[100%]">
                           </div>
         
                           <div class="ml-[auto] mr-[2px] my-[0] flex items-center gap-2">
                             <button class="flex items-center justify-center h-[24px] !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" @click="removeSocial(social)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
                           </div>
                        </div>
                     </div>
                </template>
            </div>
        </div>


        <div x-cloak x-show="createPage">
            <div>
                <div class="mb-4">
                    <div class="search-filter w-[100%]">
                        <input class="-search-input" type="text" name="query" x-model="searchQuery" x-on:input="filterSocials()" placeholder="{{ __('Search') }}">
    
                        <a class="-filter-btn">
                            {!! __i('--ie', 'search.1', 'w-5 h-5') !!}
                        </a>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 socials-create-body">
                
                    <template x-for="(item, index) in socials" :key="index">
                        <div class="h-[72px] flex px-[15px] py-[10px] relative [transition:box-shadow_0.25s_ease-out] items-center rounded-[10px] justify-between bg-[#F7F7F7] cursor-pointer hover:bg-[rgb(255,_255,_255)] hover:[box-shadow:rgba(0,_0,_0,_0.2)_0px_8px_20px]" @click="createSocial(index, item)" :data-social-name="item.name">
                            <div class="flex items-center">
                                <div class="w-[42px] h-[42px] [box-shadow:inset_0px_-1px_1px_rgba(0,_0,_0,_0.03)] rounded-[8px] flex items-center justify-center bg-white shadow-lg">
                                    
                                    <i :class="item.icon"></i>
                                </div>
    
                                <span class="text-[13px] font-semibold pl-[12px]" x-text="item.name"></span>
                            </div>
    
                            <div class="w-[52px] h-[32px] flex items-center rounded-[100px] justify-center bg-[#FFFFFF] [transition:background-color_0.2s_ease-out]">
                                <i class="fi fi-rr-plus"></i>
                            </div>
                        </div>
                    </template>
                </div>
             </div>
        </div>
    </div>

    @script
    <script>
        Alpine.data('builder__design_social', () => {
           return {
                createPage:false,

                searchQuery: '',
                createSocial(social, item){
                    
                    var count = this.site.socials.length + 1;
                    var object = {
                        uuid: this.$store.builder.generateUUID(),
                        social: social,
                        link: '',
                        postion: count
                    }
                    this.site.socials.push(object);
                    this.$wire.createSocial(object);

                    this.createPage = false;
                },
                removeSocial(social){
                    this.site.socials.forEach((element, index) => {
                        if(social.uuid == element.uuid){
                            this.site.socials.splice(index, 1);
                        }
                    });
                    this.$wire.deleteSocial(social.uuid);
                },
                filterSocials(){

                    var __ = this;
                    var items = this.$root.querySelectorAll('.socials-create-item');
                    var searchQuery = this.searchQuery.toLowerCase();
                    items.forEach(item => {
                        var _name = item.getAttribute('data-social-name');
                        
                        if (_name.indexOf(searchQuery) == -1) {
                            item.classList.add('hidden');
                        }else { item.classList.remove('hidden') }
                    });
                },

                init(){

                    if(this.$refs.social_sortable_wrapper){
                        this.$store.builder.generalSortable(this.$refs.social_sortable_wrapper, {
                            handle: '.handle'
                        }, this.$refs.social_sortable_template, this.site.socials, function(){
                            // console.log($this.item);
                        });
                    }
                }
           }
        });
    </script>
    @endscript
</div>