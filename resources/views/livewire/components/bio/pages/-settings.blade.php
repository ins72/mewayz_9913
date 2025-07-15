
<?php
   use App\Models\Page;
   use App\Models\Site;
   use Illuminate\Support\Facades\Validator;
   use function Livewire\Volt\{state, mount, placeholder, on};

   state([
      '__page' => '-',
      'site',
   ]);

   placeholder('
   <div class="w-[100%] p-5 mt-1">
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');

   on([
    
   ]);

   $checkAddress = function($address){
      $address = slugify($address, '-');
      $validator = Validator::make([
         'address' => $address
      ], [
         'address' => 'required|string|min:4|unique:sites,address,'.$this->site->id
      ]);

      if($validator->fails()){
         $this->js('$store.builder.savingState = 2');
         return [
            'status' => 'error',
            'response' => $validator->errors()->first('address'),
         ];
      }

      $this->site->address = $address;
      $this->site->save();

      $this->js('$store.builder.savingState = 2');
      return [
         'status' => 'success',
         'response' => '',
      ];
   };

   $duplicateSite = function(){
      // Check for plan
      $_c = Site::where('user_id', $this->site->user->id)->count();
      if(__o_feature('consume.sites', $this->site->user) != -1 && $_c >= __o_feature('consume.sites', $this->site->user)){
         return [
            'status' => 'error',
            'response' => __('You have reached your site creation limit. Please upgrade your plan.'),
         ];
         return;
      }

      $site = $this->site->duplicateSite();
      
      return [
         'status' => 'success',
         'response' => route('dashboard-builder-index', ['slug' => $site->_slug]),
      ];
   };

   $deleteSite = function(){
      // Check team member


      // Check if the site belongs to the current user
      // if($this->site->user_id !== iam()->id) return false;


      $this->site->deleteCompletely();

      return [
         'status' => 'success',
         'response' => route('dashboard-index'),
      ];
   };

   $duplicatePage = function($item){
      $page = Page::where('uuid', ao($item, 'uuid'))->first();
      
      $_page = $page->duplicatePage();
      $sections = \App\Models\Section::where('page_id', $_page->uuid)->get()->toArray();

      // $this->js("pages.push(". collect($_page)->toJson() .")");
      // $this->js("sections.push(". collect($layouts)->toJson() .")");
      return [
         'page' => $_page,
         'sections' => $sections
      ];
   };
?>

<div>

   <div x-data="builder__settings">

      <div x-show="__page == 'seo'">
         <div>
           <x-livewire::components.bio.parts.settings.seo />
         </div>
     </div>
     <div x-show="__page == 'domain'">
         <div>
           <livewire:components.bio.parts.settings.domain lazy :$site :key="uukey('builder::settings', 'domain')" />
         </div>
     </div>
     <div x-show="__page == 'pixel'">
         <div>
           <livewire:components.bio.parts.settings.pixel lazy :$site :key="uukey('builder::settings', 'pixel')" />
         </div>
     </div>
     <div x-show="__page == 'social'">
         <div>
           <livewire:components.bio.parts.settings.social zzzlazy :$site :key="uukey('builder::settings', 'bio.parts.settings.social')" />
         </div>
     </div>
     <div x-show="__page == 'layout'">
         <div>
           <livewire:components.bio.parts.settings.layout lazy :$site :key="uukey('builder::settings', 'bio.parts.settings.appearance')" />
         </div>
     </div>
     <div x-show="__page == 'appearance'">
         <div>
           <livewire:components.bio.parts.settings.appearance lazy :$site :key="uukey('builder::settings', 'bio.parts.settings.layout')" />
         </div>
     </div>

      <div x-cloak x-show="__page == '-'">
        <div class="settings-section section">
            <div class="settings-section-content">
        
                <div class="top-bar">
                  <div class="page-settings-navbar">
                     <ul >
                         <li class="close-header">
                         <a @click="closePage('pages')">
                             <span>
                                 {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                             </span>
                         </a>
                     </li>
                     <li>{{ __('Settings') }}</li>
                     <li></li>
                     </ul>
                 </div>
                 <div class="sticky container-small">
                     <div class="tab-link">
                         <ul class="tabs">
                         <li class="tab !w-[100%]" @click="__tab = 'site'" :class="{'active': __tab == 'site'}">{{ __('Page') }}</li>
                         </ul>
                     </div>
                 </div>
                </div>
                <div class="container-small tab-content-box">
                    <div class="tab-content">
                     <div x-cloak :class="{'active': __tab == 'site'}" data-tab-content>
                         <div class="mt-2 site-settings-section">
                             <form action="" method="post" onsubmit="return false">
                                <div class="input-box">
                                   <div class="input-label active" for="display">{{ __('Title') }}</div>
                                   <div class="input-group">
                                      <input type="text" class="input-small" placeholder="{{ __('Your Site') }}" x-model="site.name">
                                   </div>
                                </div>
                                <div class="input-box">
                                   <div class="input-label active">{{ __('Location') }}</div>
                                   <div class="input-group">
                                      <input type="text" class="input-small" placeholder="{{ __('United states') }}" x-model="site.location">
                                   </div>
                                </div>
                                <div class="input-box">
                                   <div class="input-label active">{{ __('Bio') }}</div>
                                   <div class="input-group">
                                      <input type="text" class="input-small" placeholder="{{ __('About you') }}" x-model="site.bio">
                                   </div>
                                </div>



                                <div class="input-box">
                                   <div class="input-label active" for="display">{{ __('Username') }}</div>
                                   <div class="input-group flex-col">
                                       {{-- <span class="![right:initial] left-[0] !border-l-0 [border-right:1px_solid_var(--c-mix-1)] z-10">{{ parse(config('app.url'), 'host') }}/</span> --}}

                                       <input type="text" class="input-small !zpl-[calc(var(--unit)*_7.5)]" maxlength="20" :value="site.address" @input="checkAddress($event.target.value)" placeholder="{{ __('Site Link') }}">
                                       
                                       <template x-if="addressError">
                                          <div class="bg-red-200 text-[11px] p-1 px-2 rounded-md">
                                             <div class="flex items-center">
                                                <div>
                                                   <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                                </div>
                                                <div class="flex-grow ml-1 text-xs" x-text="addressError"></div>
                                             </div>
                                          </div>
                                       </template>
                                    </div>
                                </div>

                                <div class="input-box">
                                   <div class="input-label">{{ __('Avatar') }}</div>
                                   
                                   <div class="input-group">
                                                               
                                    <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed text-center hover:border-solid hover:border-yellow-600 relative mb-1 w-[100%]" :class="{
                                       'border-gray-200': !site.logo,
                                       'border-transparent': site.logo,
                                    }">
                                       <template x-if="site.logo">
                                          <div class="group-hover:flex hidden w-[100%] h-full items-center justify-center absolute right-0 top-0 left-0 bottom-0">
                                             <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center icon-shadow" @click="site.logo = ''; $dispatch('siteFavicon:change', {
                                                image: null,
                                                public: null,
                                                })">
                                                <i class="fi fi-rr-trash"></i>
                                             </div>
                                       </div>
                                       </template>
                                       <template x-if="!site.logo">
                                          <div class="w-[100%] h-full flex items-center justify-center" @click="openMedia({
                                             event: 'siteFavicon:change', sectionBack:'navigatePage(\'__last_state\')'
                                             })">
                                             <i class="fi fi-ss-plus"></i>
                                          </div>
                                       </template>
                                       <template x-if="site.logo">
                                          <div class="h-full w-[100%]">
                                             <img :src="$store.builder.getMedia(site.logo)" class="h-full w-[100%] object-cover rounded-md" alt="">
                                          </div>
                                       </template>
                                    </div>
                                   </div>
                                </div>
                                <div class="input-box">
                                   <div class="input-label active" for="display">{{ __('Email') }}</div>
                                   <div class="input-group">
                                       <input type="text" x-model="site.email" class="input-small" placeholder="{{ __('Site Email') }}">
                                   </div>
                                </div>
                             </form>
                             <div class="mt-2 site-actions">
                                {{-- <div class="input-box" @click="!__o_feature('feature.branding') ? $dispatch('open-modal', 'upgrade-modal') : ''" x-init="!__o_feature('feature.branding') ? site.settings.disable_branding = false : ''">
                                   <div class="input-label" for="link"></div>
                                   <div class="input-group">
                                      <div class="switchWrapper">
                                         <input id="made-in-yena" type="checkbox" class="switchInput" value="-" x-model="site.settings.disable_branding" :disabled="!__o_feature('feature.branding')"><label for="made-in-yena" class="switchLabel">{{ __('Remove Branding') }}</label>
                                         <div class="slider"></div>
                                      </div>
                                   </div>
                                </div>

                                <div class="input-box">
                                   <div class="input-label" for="link"></div>
                                   <div class="input-group">
                                      <div class="switchWrapper">
                                         <input id="site-preloader" type="checkbox" class="switchInput" x-model="site.settings.preloader">
                                         <label for="site-preloader" class="switchLabel">{{ __('Site preloader') }}</label>

                                         <div class="slider"></div>
                                      </div>
                                   </div>
                                </div> --}}
                                <ul>
                                   {{-- <li @click="$dispatch('open-modal', 'upgrade-modal')">
                                      <a name="site plans">
                                         <p>{{ __('Site plans') }}</p>
                                         <span>
                                             {!! __i('Arrows, Diagrams', 'Arrow.5') !!}
                                         </span>
                                      </a>
                                   </li> --}}
                                   <li @click="__page='appearance'">
                                      <a name="signup">
                                         <span>
                                             {!! __i('Design Tools', 'color-palette') !!}
                                         </span>
                                         <p>{{ __('Appearance') }}</p>
                                         <span>
                                             {!! __i('Arrows, Diagrams', 'Arrow.5') !!}
                                         </span>
                                      </a>
                                   </li>
                                   <li @click="__page='layout'">
                                      <a name="signup">
                                          <span>
                                             {!! __i('interface-essential', 'grid-layout.14') !!}
                                          </span>
                                          <p>{{ __('Layout') }}</p>
                                          <span>
                                                {!! __i('Arrows, Diagrams', 'Arrow.5') !!}
                                          </span>
                                      </a>
                                   </li>
                                   <li @click="__page='social'">
                                      <a name="signup">
                                          <span>
                                             {!! __i('Social Medias Rewards Rating', 'hashtag-shine') !!}
                                          </span>
                                          <p>{{ __('Social') }}</p>
                                          <span>
                                                {!! __i('Arrows, Diagrams', 'Arrow.5') !!}
                                          </span>
                                      </a>
                                   </li>
                                   <li @click="__page='domain'">
                                      <a name="signup">
                                          <span>
                                             {!! __i('--ie', 'earth-globe-more-setting') !!}
                                          </span>
                                         <p>{{ __('Custom domain') }}</p>
                                         <span>
                                             {!! __i('Arrows, Diagrams', 'Arrow.5') !!}
                                         </span>
                                      </a>
                                   </li>
                                   <li @click="__page='pixel'">
                                      <a name="export-site">
                                          <span>
                                             {!! __i('Social Media', 'google-analytics') !!}
                                          </span>
                                          <p>{{ __('Pixel code') }}</p>
                                          <span>
                                                {!! __i('Arrows, Diagrams', 'Arrow.5') !!}
                                          </span>
                                      </a>
                                   </li>
                                   
                                   <li @click="duplicateSite()" :class="{
                                    '!h-full': duplicateError,
                                   }">
                                      <a name="signup" :class="{
                                       'min-h-[calc(var(--unit)_*_4)]': duplicateError
                                      }">
                                          <span>
                                             {!! __i('interface-essential', 'copy-duplicate-object-add-plus') !!}
                                          </span>
                                          <p>{{ __('Duplicate site') }}</p>
                                          <span></span>
                                      </a>
                                       
                                      <template x-if="duplicateError">
                                         <div class="bg-red-200 text-[11px] p-1 px-2 rounded-none">
                                            <div class="flex items-center">
                                               <div>
                                                  <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                               </div>
                                               <div class="flex-grow ml-1 text-xs" x-text="duplicateError"></div>
                                            </div>
                                         </div>
                                      </template>
                                   </li>
                                   <template x-if="site.published">
                                       <li @click="site.published=false">
                                          <a name="unpublish-site">
                                             <p class="error">{{ __('Unpublish site') }}</p>
                                             <span></span>
                                          </a>
                                       </li>
                                   </template>
                                   <li class="mb-2" @click="deleteSite=true">
                                      <a class="delete-site" name="yena-pro">
                                          <span>
                                             {!! __i('interface-essential', 'trash-bin-delete') !!}
                                          </span>
                                          <p>{{ __('Delete site') }}</p>
                                          <span></span>
                                      </a>
                                   </li>
                                </ul>
                             </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
      </div>
    
      <template x-teleport="body">
         <div class="overlay backdrop delete-overlay" :class="{
             '!block': deleteSite
         }" @click="deleteSite=false">
             <div class="delete-site-card !border-0 !rounded-2xl !shadow-lg" @click="$event.stopPropagation()">
                <div class="overlay-card-body !rounded-2xl">
                   <h2>{{ __('Delete Page?') }}</h2>
                   <p class="mt-4 mb-4 text-[var(--t-m)] text-center leading-[var(--l-body)] text-[var(--c-mix-3)] w-3/5 ml-auto mr-auto">{{ __('Are you sure you want to permanently delete your page? Once deleted, you will not be able to restore it.') }}</p>
                   <div class="card-button pl-[var(--s-2)] pr-[var(--s-2)] pt-[0] pb-[var(--s-2)] flex gap-2">
                      <button class="btn btn-medium neutral !h-[calc(var(--unit)*_4)]" type="button" @click="deleteSite=false">{{ __('Cancel') }}</button>
     
                      <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-[calc(var(--unit)*_4)]" @click="delete_site">{{ __('Yes, Delete') }}</button>
                   </div>
                   <template x-if="deleteError">
                      <div class="pl-[var(--s-2)] pr-[var(--s-2)] pt-[0] pb-[var(--s-2)]">
                        <div class="bg-red-200 text-[11px] p-1 px-2 rounded-md">
                           <div class="flex items-center">
                              <div>
                                 <i class="fi fi-rr-cross-circle flex text-xs"></i>
                              </div>
                              <div class="flex-grow ml-1 text-xs" x-text="deleteError"></div>
                           </div>
                        </div>
                      </div>
                   </template>
                   <template x-if="deleteSuccess">
                     <div class="pl-[var(--s-2)] pr-[var(--s-2)] pt-[0] pb-[var(--s-2)]">
                      <div class="bg-green-200 text-[11px] p-1 px-2 rounded-md">
                         <div class="flex items-center">
                            <div>
                               <i class="fi fi-rr-cross-circle flex text-xs"></i>
                            </div>
                            <div class="flex-grow ml-1 text-xs">{{ __('Site deleted successfully.') }}</div>
                         </div>
                      </div>
                     </div>
                   </template>
                </div>
             </div>
          </div>
     </template>
      @script
      <script>
          Alpine.data('builder__settings', () => {
             return {
                __tab: 'site',
                __page: @entangle('__page'),
                autoSaveTimer: null,
                deleteSite: false,
                addressError: false,
                duplicateError: false,
                deleteError: false,
                deleteSuccess: false,
                deletePagePrompt: false,
                delete_site(){
                  let $this = this;
                  $this.deleteError = false;
                  $this.deleteSuccess = false;

                  $this.$wire.deleteSite().then(r => {
                     if(r.status === 'error'){
                        $this.deleteError = r.response;
                     }
                     
                     if(r.status === 'success'){
                        $this.deleteError = false;
                        $this.deleteSuccess = true;

                        
                        setTimeout(function() {
                           window.location.replace(r.response);
                        }, 2000);
                     }
                  });
                },
                checkAddress(address){
                  address = address.toString() // Cast to string
                              .toLowerCase() // Convert the string to lowercase letters
                              .normalize('NFD') // The normalize() method returns the Unicode Normalization Form of a given string.
                              .trim() // Remove whitespace from both sides of a string
                              .replace(/\s+/g, '-') // Replace spaces with -
                              .replace(/[^\w\-]+/g, '') // Remove all non-word chars
                              .replace(/\-\-+/g, '-');
                  this.$event.target.value = address;
                  let $this = this;

                  $this.addressError = false;
                  clearTimeout($this.autoSaveTimer);

                  $this.autoSaveTimer = setTimeout(function(){
                     $this.$store.builder.savingState = 0;
                      
                     $this.$wire.checkAddress(address).then(r => {
                        if(r.status === 'error'){
                           $this.addressError = r.response;
                        }
                        if(r.status === 'success'){
                           $this.addressError = false;
                        }
                     });

                  }, $this.$store.builder.autoSaveDelay);
                },
                duplicateSite(){
                  let $this = this;
                  $this.duplicateError = false;
                  $this.$wire.duplicateSite().then(r => {
                     if(r.status === 'error'){
                        $this.duplicateError = r.response;
                     }
                     if(r.status === 'success'){
                        $this.duplicateError = false;
                        window.location.replace(r.response);
                     }
                  });
                },

               duplicatePage(){
                  var $this = this;
                  this.$wire.duplicatePage($this.currentPage).then(r => {
                     $this.pages.push(r.page);
                     r.sections.forEach((e) => {
                        $this.sections.push(e);
                     });
                     $dispatch('builder::setPageEvent', r.page.uuid);
                  });
               },
                deletePage() {
                  let $this = this;
                    this.pages.forEach((element, index) => {
                        if($this.currentPage.uuid == element.uuid){
                            this.pages.splice(index, 1);
                        }
                    });
                    $dispatch('builder::deletePageEvent', this.currentPage);
                    $dispatch('builder::setPageEvent', this.pages[0].uuid);
                },
                init(){
                  let $this = this;
                     
                  // this.$watch('pages' , (value, _v) => {
                  //    if(!$this.currentPage.seo) $this.currentPage.seo = [];
                  // });

                   window.addEventListener("siteDuplicate", (event) => {
                     $this.__tab = 'site';
                     $this.duplicateSite();
                   });

                  window.addEventListener("siteFavicon:change", (event) => {
                     $this.site.logo = event.detail.image;
                  });

                  window.addEventListener("opensection::layout", (event) => {
                     $this.navigatePage('settings');
                     $this.__page = 'layout';
                  });

                  window.addEventListener("opensection::social", (event) => {
                     $this.navigatePage('settings');
                     $this.__page = 'social';
                  });
                }
             }
          });
      </script>
      @endscript
    </div>
</div>