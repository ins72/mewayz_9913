<div>

    <div class="website-section" x-data="builder__design_social">
        <div class="design-navbar">
           <ul >
               <li class="close-header !flex">
                  <a @click="createPage ? createPage=false : __page='-'">
                   <span>
                       {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                   </span>
                 </a>
              </li>
              <li class="!pl-0">{{ __('Socials') }}</li>
              <li></li>
           </ul>
        </div>
        <div class="container-small p-[var(--s-2)] pb-[150px]">

         <template x-if="(site.socials.length + 1) <= 10">
            <button type="button" class="yena-button-stack !text-sm !h-[2rem] w-full mb-1" :class="{'!hidden': createPage}" @click="createPage=true">
                {{ __('+ Add Social') }}
             </button>
         </template>
    
         <div x-cloak x-show="!createPage">
             <div class="flex flex-col gap-[10px]" x-ref="social_sortable_wrapper">
                 <template x-for="(social, index) in window._.sortBy(site.socials, 'position')" :key="social.uuid" x-ref="social_sortable_template">
                     <div class="social-link-container !mt-0" data-draggable="true">
                        <span class="social-link-container__left-icons">
                           <div class="handle">
                              {!! __i('custom', 'grip-dots-vertical', 'social-link__draggable-icon') !!}
                           </div>
                           <i :class="socials[social.social].icon" class="text-xs"></i>
                        </span>
                        <input type="text" x-model="social.link" :placeholder="socials[social.social].placeholder" class="input-small social-link-container__input">

                        <span class="delete-icon delete-button" @click="removeSocial(social)">
                           {!! __i('interface-essential', 'trash-bin-delete', 'text-[color:var(--c-red)]') !!}
                        </span>
                     </div>
                 </template>
             </div>
         </div>
 
 
         <div x-cloak x-show="createPage">
             <div>
                 <div class="mb-1">
                     <div class="search-filter w-[100%]">
                         <input class="-search-input" type="text" name="query" x-model="searchQuery" x-on:input="filterSocials()" placeholder="{{ __('Search') }}">
     
                         <a class="-filter-btn">
                             {!! __i('--ie', 'search.1', 'w-5 h-5') !!}
                         </a>
                     </div>
                 </div>
                 <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 socials-create-body">
                 
                     <template x-for="(item, index) in socials" :key="index">
                         <div class="h-[72px] flex px-[15px] py-[10px] relative [transition:box-shadow_0.25s_ease-out] items-center rounded-[10px] justify-between bg-[#F7F7F7] cursor-pointer hover:bg-[rgb(255,_255,_255)] hover:[box-shadow:rgba(0,_0,_0,_0.2)_0px_8px_20px] socials-create-item" @click="createSocial(index, item)" :data-social-name="item.name">
                             <div class="flex items-center">
                                 <div class="w-[32px] h-[32px] [box-shadow:inset_0px_-1px_1px_rgba(0,_0,_0,_0.03)] rounded-[8px] flex items-center justify-center bg-white shadow-lg">
                                     
                                     <i :class="item.icon"></i>
                                 </div>
     
                                 <span class="text-[13px] font-semibold pl-[12px]" x-text="item.name"></span>
                             </div>
     
                             <div class="w-[32px] h-[32px] flex items-center rounded-[100px] justify-center bg-[#FFFFFF] [transition:background-color_0.2s_ease-out]">
                                 <i class="fi fi-rr-plus"></i>
                             </div>
                         </div>
                     </template>
                 </div>
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
                  if((this.site.socials.length) >= 10) return;
                  
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
