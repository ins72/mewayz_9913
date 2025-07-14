
<?php
    
    use App\Models\BioSiteSocial;
    use function Livewire\Volt\{state, mount, placeholder, updated, on};
    state(['site']);


    $createSocial = function($item){
        $_item = new BioSiteSocial;
        $_item->fill($item);
        $_item->site_id = $this->site->id;
        $_item->save();
    };

    $deleteSocial = function($id){
        if(!$_social = BioSiteSocial::where('uuid', $id)->delete()) return;
    };
?>
<div class="banner-section !block">
   <div x-data="builder__edit_social" wire:ignore>

       <div class="banner-navbar">
           <ul >
               <li class="close-header">
                  <a @click="__page='-'">
                     <span>
                        {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                     </span>
                  </a>
               </li>
               <li>{{ __('Edit Social') }}</li>
               <li></li>
           </ul>
       </div>
       <div class="sticky container-small !hidden"></div>
       <div class="container-small tab-content-box">
           <div class="tab-content">
               <div x-cloak data-tab-content>
                  <div>
                     <div class="mt-2 content">
                         <div class="panel-input mb-1 px-[var(--s-2)]">
                           <div class="flex flex-col gap-[10px]" x-ref="social_sortable_wrapper">
                               <template x-for="(social, index) in window._.sortBy(site.socials, 'position')" :key="index" x-ref="social_sortable_template">
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
                           <template x-if="site.socials.length == 0">
                               <div class="flex flex-col justify-center items-center px-[20px] py-[60px]">
                                   {!! __i('--ie', 'windows-disable-close-all', 'w-14 h-14') !!}
                                   <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                                       {!! __t('You can add social by clicking on one below.') !!}
                                   </p>
                               </div>
                           </template>
                           <div class="mt-2">
                              <ul class="social-links-list">
                                 <template x-for="(item, index) in _socials.slice(0, socialLimit)" :key="index">
                                    <li class="social-links-list__link socials-create-item" @click="createSocial(item)" :data-social-name="item.name">
                                       <i :class="item.icon"></i>
                                    </li>
                                 </template>
   
                                 <li x-show="socialLimit < _socials.length && !searchQuery" class="social-links-list__link !bg-gray-100" @click="socialLimit += 18">
                                    <i class="ph ph-dots-three"></i>
                                 </li>
                              </ul>
                              <div class="mt-1">
                                  <div class="search-filter w-[100%]">
                                      <input class="-search-input" type="text" name="query" x-model="searchQuery" x-on:input="filterSocials()" placeholder="{{ __('Search') }}">
                  
                                      <a class="-filter-btn">
                                          {!! __i('--ie', 'search.1', 'w-5 h-5') !!}
                                      </a>
                                  </div>
                              </div>
                           </div>
                         </div>
                      </div>
                 
                 </div>
               </div>
           </div>
       </div>
   </div>
   @script
   <script>
      Alpine.data('builder__edit_social', () => {
         return {
            deleteSectionId: false,
            socialLimit: 18,
            maxSocials: 6,
            _socials: [],
            searchQuery: '',
            removeSocial(social){
                this.site.socials.forEach((element, index) => {
                    if(social.uuid == element.uuid){
                        this.site.socials.splice(index, 1);
                    }
                });
                this.$wire.deleteSocial(social.uuid);
            },
            createSocial(item){
                var count = this.site.socials.length + 1;
                var object = {
                    uuid: this.$store.builder.generateUUID(),
                    social: item.social,
                    link: '',
                    postion: count
                }
                this.site.socials.push(object);
                this.$wire.createSocial(object);
            },
            filterSocials(){
                var __ = this;
                var items = this.$root.querySelectorAll('.socials-create-item');
                var searchQuery = this.searchQuery.toLowerCase();
                items.forEach(item => {
                     var _name = item.getAttribute('data-social-name').toLowerCase();
                    
                    if (_name.indexOf(searchQuery) == -1) {
                        item.classList.add('!hidden');
                    }else { item.classList.remove('!hidden') }
                });
            },

            init(){
               
               let $this = this;
               
               for (let key in this.socials) {
                  if (this.socials.hasOwnProperty(key)) {
                     let value = this.socials[key];

                     this._socials.push({
                        ...value,
                        social: key,
                     })
                     // Do something with the key and value
                  }
               }
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