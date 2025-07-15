
<?php

   use App\Yena\Page\Generate;
   use App\Models\YenaBioTemplate;
   use App\Models\BioSite;
   use Illuminate\Support\Facades\Validator;
   use function Livewire\Volt\{state, mount, updated, placeholder};

   placeholder('
   <div class="p-5 w-full mt-1">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');


   state([
      'address' => '',
   ]);

   state(['site']);
   state([
      'address' => '',
      'templates' => [],
      'selectedTemplate' => null,
      'selectedTemplateSite' => null,
   ]);

   updated([
      'selectedTemplate' => function(){
         // $this->selectedTemplate = null;
         if($site = YenaBioTemplate::find($this->selectedTemplate)){
            $this->selectedTemplateSite = $site;
         }
      }
   ]);
   mount(function(){
       $this->_get();
   });
   $_get = function(){
      $this->templates = YenaBioTemplate::get();
   };

   $removeSelectedTemplate = function(){
      $this->selectedTemplateSite = null;
   };

   // Methods

   $createPage = function(){
      $this->skipRender();
      // $_c = SitePage::where('site_id', $this->site->id)->count();
      // if(__o_feature('consume.pages', iam()) != -1 && $_c >= __o_feature('consume.pages', iam())){
      //    $this->js('window.runToast("error", "'. __('You have reached your page creation limit. Please upgrade your plan.') .'");');
      //    return;
      // }
      // Create Page
      // $name = "Untitled-" . ao($template, 'name');
      // $slug = slugify(ao($_page, 'name') . str()->random(3), '-');

      if($template = YenaBioTemplate::find($this->selectedTemplate)){
         $randomSlug = str()->random(15);
         $addressSlug = str()->random(7);

         $name = __('My new Page');
         $slug_name = slugify($name, '-');
         $_slug = "$slug_name-$randomSlug";
         $address = $this->address;

         if(!$template->isFree() && !iam()->hasBioTemplateAccess($template->id)){
            $this->js(
               '
                  window.runToast("error", "'. __('Purchase this template to use.') .'");
               '
            );

            // Maybe dispatch event to purchase
            return;
         }

         // Template is free
         $site = $template->site;
            
         // $_c = BioSite::where('user_id', iam()->id)->count();
         // if(__o_feature('consume.sites', iam()) != -1 && $_c >= __o_feature('consume.sites', iam())){
         //    $this->js('window.runToast("error", "'. __('You have reached your site creation limit. Please upgrade your plan.') .'");');
         //    return;
         // }

         $site = $site->duplicateSite();
         $site->user_id = iam()->id;
         $site->created_by = iam()->get_original_user()->id;
         $site->name = $name;
         $site->_slug = $_slug;
         $site->address = $address;
         $site->save();
         $route = route('dashboard-bio-index', ['slug' => $site->_slug]);

         $this->js(
            '
                  window.runToast("success", "'. __('Page created successfully') .'")
                  setTimeout(function() {
                     window.location.replace("'.$route.'");
                  }, 2000);
            '
         );
         return;
      }

      $generate = new Generate;
      $build = $generate->setOwner(iam())->setName('My new Page')->build();
      $build->address = $this->address;
      $build->save();

      $route = route('dashboard-bio-index', ['slug' => $build->_slug]);
      return redirect($route);
   };

   $checkAddress = function($address){
      $address = slugify($address, '-');
      $validator = Validator::make([
         'address' => $address
      ], [
         'address' => 'required|string|min:3|unique:bio_sites'
      ]);

      if($validator->fails()){
         $this->js('$store.builder.savingState = 2');
         return [
            'status' => 'error',
            'response' => $validator->errors()->first('address'),
         ];
      }

      $this->js('$store.builder.savingState = 2');
      return [
         'status' => 'success',
         'response' => __(':address is available', ['address' => $address]),
      ];
   };
?>

<div>
  <div x-data="builder__new_page">
   
      <div>
         <div class="design-navbar">
            <ul >
                <li class="close-header !flex">
                  <a @click="type !== '-' ? type = '-' : createPage=false; removeSelectedTemplate()">
                    <span>
                        {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                    </span>
                  </a>
               </li>
               <li class="!pl-0" x-text="pageName">{{ __('Add Page') }}</li>
               <li></li>
            </ul>
         </div>
         <div class="container-small">
          <div class="p-5">
            <div x-show="type=='-'">
               <div class="flex items-center flex-col gap-12">
                  <div class="ai-items -is-vert">
                      <div class="ai-item [grid-area:paste] -left cursor-pointer" @click="type='blank'">
                          <a class="-item-inner">
                              <div class="-item-stack">
                                  <div class="-item-header !bg-white !text-black">
                                      <div class="">
                                         <div class="absolute top-0 opacity-0 left-0 w-[100%] h-full transition-all duration-300 z-10 !opacity-100 flex items-center justify-center !bg-black">
                                          {!! __i('Files', 'file-blank-edit-pen', 'w-8 h-8 text-white') !!}
                                         </div>
                                       </div>
                                  </div>
                                  <div class="flex items-stretch justify-between flex-row gap-[var(--yena-space-1-5)] p-4 max-w-[100%] min-h-[100px] flex-[1.5_1_0%] relative h-full md:flex-col !bg-[#f7f3f2]">
                                      <div class="flex flex-col gap-2">
                                          <h2 class="leading-[1.33] text-xl font-semibold">{{ __('Blank') }}</h2>

                                          <div class="flex items-center flex-row gap-2">
                                              <div class="text-sm md:text-base font-medium leading-[1.4] text-[var(--yena-colors-gray-600)]">{{ __('Start with a blank page.') }}</div>
                                          </div>
                                      </div>


                                      <div class="flex items-start justify-end flex-row gap-2 max-w-[100%] text-sm p-1 sm:text-base md:p-0">
                                          <p class="hidden [transition:all_300ms_ease_0s] opacity-0 md:block continue-text">{{ __('Continue') }}</p>
                                          <div>
                                              {!! __i('Arrows, Diagrams', 'Arrow', 'h-6') !!}
                                          </div>
                                      </div>

                                  </div>
                              </div>
                          </a>
                      </div>
                      <div class="ai-item [grid-area:advance] -right" @click="type='template'">
                          <a class="-item-inner">
                              <div class="-item-stack">
                                  <div class="-item-header">
                                      <div class="">
                                         <div class="absolute top-0 opacity-0 left-0 w-[100%] h-full transition-all duration-300 z-10 !opacity-100 flex items-center justify-center">
                                              {!! __i('interface-essential', 'thunder-lightning-notifications', 'w-8 h-8') !!}
                                         </div>
                                       </div>
                                  </div>

                                  <div class="flex items-stretch justify-between flex-row gap-[var(--yena-space-1-5)] p-4 max-w-[100%] min-h-[100px] flex-[1.5_1_0%] relative h-full md:flex-col !bg-[#f7f3f2]">
                                      <div class="flex flex-col gap-2">
                                          <h2 class="leading-[1.33] text-xl font-semibold">{{ __('Template') }}</h2>

                                          <div class="flex items-center flex-row gap-2">
                                              <div class="text-sm md:text-base font-medium leading-[1.4] text-[var(--yena-colors-gray-600)]">{{ __('Start from our list of templates') }}</div>
                                          </div>
                                      </div>

                                      <div class="flex items-start justify-end flex-row gap-2 max-w-[100%] text-sm p-1 sm:text-base md:p-0">
                                          <p class="hidden [transition:all_300ms_ease_0s] opacity-0 md:block continue-text">{{ __('Continue') }}</p>
                                          <div>
                                              {!! __i('Arrows, Diagrams', 'Arrow', 'h-6') !!}
                                          </div>
                                      </div>

                                  </div>
                              </div>
                          </a>
                      </div>
                  </div>
              </div>
            </div>
            <div x-show="type=='blank'">
               <form @submit.prevent="_createPage" class="">
                  <div class="flex flex-col gap-3">
                     {{-- <div class="text-xl font-extrabold tracking-[-1px]">{{ __('Domain') }}</div> --}}
                     <div class="flex flex-col justify-center items-center px-[20px] pt-[60px]">
                        <template x-if="selectedTemplate">
                           <div>
                              {!! __icon('--ie', 'thunder-lightning-notifications', 'w-14 h-14 mx-auto') !!}
                              <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                                 {!! __t('Continue with your page name to create your page with the selected template.') !!}
                              </p>
                           </div>
                        </template>
                        <template x-if="!selectedTemplate">
                           <div>
                              {!! __icon('--ie', 'item-pen-text-square', 'w-14 h-14 mx-auto') !!}
                              <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                                 {!! __t('Create new bio page for your visitors to navigate to.') !!}
                              </p>
                           </div>
                        </template>
                     </div>
                  </div>
   

                  <div class="custom-content-input border-2 border-dashed mb-1">
                     <label class="h-10 !flex items-center px-5">
                        <span class="text-sm">{{ config('app.bio_address') . config('app.bio_prefix') . '/' }}</span>
                     </label>
                     <input type="text" x-model="address" @input="checkAddress()" placeholder="{{ __('Address') }}" class="w-[100%] !bg-gray-100">
                  </div>
   
                  <template x-if="backendError">
                     <div class="bg-red-200 text-[11px] p-1 px-2 mb-1 rounded-full">
                        <div class="flex items-center">
                           <div>
                              <i class="fi fi-rr-cross-circle flex text-xs"></i>
                           </div>
                           <div class="flex-grow ml-1 text-xs !text-black" x-text="backendError"></div>
                        </div>
                     </div>
                  </template>
                  <template x-if="addressAvailable">
                     <div class="bg-green-200 text-[11px] p-1 px-2 mb-1 rounded-full">
                        <div class="flex items-center">
                           <div>
                              <i class="ph ph-checkmark flex text-xs"></i>
                           </div>
                           <div class="flex-grow ml-1 text-xs !text-black" x-text="addressAvailable"></div>
                        </div>
                     </div>
                  </template>
                                       
   
                  <button class="yena-button-stack w-[100%]" :disabled="!backendError && !addressAvailable || backendError && !addressAvailable">
                     <span :class="{
                           'hidden': !buttonLoader,
                           'flex': buttonLoader
                     }">
                           <span class="loader-o20 !text-[9px] mx-auto !text-black"></span>
                     </span>
                     <span x-show="!buttonLoader">{{ __('Continue') }}</span>
                  </button>

                  <div x-show="selectedTemplate">
                     @if ($item = $selectedTemplateSite)
                     <div class="template grid !grid-cols-1 mt-2">
                        <div class="template-container bg-white">
                           <label class="template-block templateDiv{{ $item->id }}" @click="openPreview('{{ $item->name }}', '{{ $item->site->getAddress() }}', '{{ !$item->isFree() && !$item->isPurchased() ? 'false' : 'true' }}', '{!! price_with_cur(\Currency::symbol(settings('payment.currency')), $item->price) !!}', '{{ $item->id }}')">
                              <div class="template-image">
                                 <div class="template-content" wire:ignore>
                                    <div class="w-[100%]" x-data x-init="
                                       setTimeout(function(){
                                          $store.builder.rescaleDiv($root)
                                       }, 500);">
                                       <div class="page-type-options zzmax-w-[360px] !p-0 !m-0">
                                          <div class="page-type-item">
                                             <div class="container-small edit-board overflow-hidden !origin-[0px_0px] ![height:initial]">
                                                <div class="card">
                                                   <div class="card-body pointer-event-none relative" wire:ignore>
                                                      <div>
                                                         <livewire:site.bio lazy :site="$item->site" :key="uukey('site-bio', 'zzz-site-template-' . $item->site->_slug)" />
                                                      </div>
                                                      <div class="absolute h-full w-[100%] z-[2] bottom-0 left-0"></div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="!p-0"></div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="template-footer">
                                 <div class="template-details">
                                    <p class="template-name">{{$item->name}}</p>
                                    <p class="template-creator">{{ $item->creator->name }}</p>
                                 </div>
                                 <div class="template-price" data-desc="{{ __('Use Template') }}"></div>
                              </div>
                           </label>
                           <div class="template-footer-button">
                              <a class="btn !w-[100%]" @click="removeSelectedTemplate">{{ __('Remove') }}</a>
                           </div>
                        </div>
                     </div>
                     @endif
                  </div>
               </form>
            </div>
            <div x-show="type=='template'">
               <div class="template grid !grid-cols-1">
                  @foreach ($templates as $item)
                  @php
                     if(!$item->site) continue;
                  @endphp
                  <div class="template-container bg-white" style="--template-price: '{!! $item->isFree() ? __('Free') : price_with_cur(\Currency::symbol(settings('payment.currency')), $item->price) !!}'">
                     <label class="template-block templateDiv{{ $item->id }}" @click="openPreview('{{ $item->name }}', '{{ $item->site->getAddress() }}', '{{ !$item->isFree() && !$item->isPurchased() ? 'false' : 'true' }}', '{!! price_with_cur(\Currency::symbol(settings('payment.currency')), $item->price) !!}', '{{ $item->id }}')">
                        <div class="template-image">
                           <div class="template-content">
                              <div class="w-[100%]" x-intersect="$store.builder.rescaleDiv($root)">
                                 <div class="page-type-options zzmax-w-[360px] !p-0 !m-0">
                                    <div class="page-type-item">
                                       <div class="container-small edit-board overflow-hidden !origin-[0px_0px] ![height:initial]">
                                          <div class="card">
                                             <div class="card-body pointer-event-none relative" wire:ignore>
                                                <div>
                                                   <livewire:site.bio lazy :site="$item->site" :key="uukey('site-bio', 'zz-site-template-' . $item->site->_slug)" />
                                                </div>
                                                <div class="absolute h-full w-[100%] z-[2] bottom-0 left-0"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="!p-0"></div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="template-footer">
                           <div class="template-details">
                              <p class="template-name">{{$item->name}}</p>
                              <p class="template-creator">{{ $item->creator->name }}</p>
                           </div>
                           <div class="template-price" data-desc="{{ __('Use Template') }}"></div>
                        </div>
                     </label>
                     <div class="template-footer-button">
                        <a class="btn" @click="openPreview('{{ $item->name }}', '{{ $item->site->getAddress() }}', '{{ !$item->isFree() && !$item->isPurchased() ? 'false' : 'true' }}', '{!! price_with_cur(\Currency::symbol(settings('payment.currency')), $item->price) !!}', '{{ $item->id }}')">{{ __('Preview') }}</a>

                        @if (!$item->isFree() && !$item->isPurchased())
                        <button class="btn" @click="$dispatch('open-modal', 'buy-template-modal'); selectedTemplate='{{ $item->id }}'">{{ __('Buy Template') }}</button>
                        @else
                        <button class="btn" @click="selectTemplate('{{ $item->id }}');">{{ __('Use Template') }}</button>
                        @endif
                     </div>
                  </div>
                  @endforeach
               </div>
            </div>
          </div>
         </div>
      </div>
      <template x-teleport="body">
         <div x-cloak x-show="openModal">
   
            <div class="fixed inset-0 z-[99999] top-0 left-0 bg-gradient-to-b from-white to-[#efefef] bg-fixed" :class="{
               'hidden': !openModal,
            }">
               <div class="yena-theme-preview-bar w-[100%] px-4 sm:px-6">
                   <div class="yena-theme-preview-info flex items-center">
                        <a class="yena-theme-preview-back flex" @click="openModal=false; preview.url=null">
                           {{-- <span class="hidden sm:inline text-sm">{{ __('All Templates') }}</span> --}}
                              <span class="inline zzsm:hidden mr-2 relative z-top-[1px] text-sm">&lt;-</span>
                        </a>
                       <span class="hidden sm:inline text-sm">/</span>
                       <h1 class="text-gray-900 font-sans text-sm my-auto" x-text="preview.name"></h1>
                   </div>
                   <div class="yena-theme-preview-selector">
                     <button x-on:click="renderMobile=false" :class="{ 'active': !renderMobile }" class="active">
                           <i class="text-base fi fi-rr-computer"></i>
                       </button>
                       <button x-on:click="renderMobile=true" :class="{ 'active': renderMobile }">
                        <i class="text-base fi fi-rr-mobile-notch"></i>
                     </button>
                  </div>
                   <div class="yena-theme-preview-action">
                     <a class="yena-theme-preview-external" target="_blank" :href="preview.url">
                        <span class="capitalize text-sm">
                           <span class="hidden sm:inline">{{ __('Visit') }}</span>
                           <span class="capitalize sm:lowercase">{{ __('demo') }}</span>
                        </span>
                     </a>
      
                     <template x-if="preview.isFree">
                        <a class="whitespace-nowrap border-0 ml-3 sm:ml-5 px-4 py-2.5 rounded-md shadow-sm text-sm font-semibold text-white bg-black text-white" @click="$dispatch('open-modal', 'create-template-modal'); selectedTemplate=preview.id; openModal=false; preview.url=null">{{ __('Use Template') }}</a>
                     </template>
                     <template x-if="!preview.isFree">
                        <a class="whitespace-nowrap border-0 ml-3 sm:ml-5 px-4 py-2.5 rounded-md shadow-sm text-sm font-semibold text-white bg-black text-white" @click="$dispatch('open-modal', 'buy-template-modal'); selectedTemplate=preview.id; openModal=false; preview.url=null">{{ __('Buy') }} <span x-html="preview.price"></span></a>
                     </template>
                  </div>
               </div>
               <section class="yena-section yena-section-theme-preview my-0 mx-auto max-w-10xl w-[100%] px-4 sm:px-6">
                   <div class="my-0 mx-auto max-w-8xl w-[100%]">
                       <div class="yena-theme-preview-demo-wrapper">
                           <div class="yena-theme-preview-demo desktop" :class="!renderMobile ? 'desktop' : 'mobile'">
                               <div class="yena-theme-card-toolbar"><span class="yena-theme-card-toolbar-button"></span></div>
      
                               <template x-if="preview.url">
                                 <iframe class="yena-theme-preview-frame" frameborder="0" scrolling="yes" height="100%" :src="preview.url"></iframe>
                               </template>
                           </div>
                       </div>
                   </div>
               </section>
           </div>
         </div>
      </template>
      <div>
         <div class="design-navbar">
            <ul >
                <li class="close-header !flex">
                  <a @click="createPage=false">
                    <span>
                        {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                    </span>
                  </a>
               </li>
               <li class="!pl-0">{{ __('Add Page') }}</li>
               <li></li>
            </ul>
         </div>
         <div class="container-small">
          <div class="p-5">
            <form @submit.prevent="$wire.createPage(); buttonLoader=true;" class="">
               <div class="flex flex-col gap-3">
                  {{-- <div class="text-xl font-extrabold tracking-[-1px]">{{ __('Domain') }}</div> --}}
                  <div class="flex flex-col justify-center items-center px-[20px] pt-[60px]">
                     {!! __i('Internet, Network', 'Browser, Internet, Web, Network, Site', 'w-14 h-14') !!}
                     <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                        {!! __t('Take your time to think of something unique and creative for your linkinbio address.') !!}
                     </p>
                  </div>
               </div>

               <div class="custom-content-input border-2 border-dashed mb-1">
                  <label class="h-10 !flex items-center px-5">
                     <span class="text-sm">{{ config('app.bio_address') . config('app.bio_prefix') . '/' }}</span>
                  </label>
                  <input type="text" x-model="address" @input="checkAddress()" placeholder="{{ __('Address') }}" class="w-[100%] !bg-gray-100">
               </div>

               <template x-if="backendError">
                  <div class="bg-red-200 text-[11px] p-1 px-2 mb-1 rounded-full">
                     <div class="flex items-center">
                        <div>
                           <i class="fi fi-rr-cross-circle flex text-xs"></i>
                        </div>
                        <div class="flex-grow ml-1 text-xs !text-black" x-text="backendError"></div>
                     </div>
                  </div>
               </template>
               <template x-if="addressAvailable">
                  <div class="bg-green-200 text-[11px] p-1 px-2 mb-1 rounded-full">
                     <div class="flex items-center">
                        <div>
                           <i class="ph ph-checkmark flex text-xs"></i>
                        </div>
                        <div class="flex-grow ml-1 text-xs !text-black" x-text="addressAvailable"></div>
                     </div>
                  </div>
               </template>
                                    

               <button class="yena-button-stack w-[100%]" :disabled="!backendError && !addressAvailable || backendError && !addressAvailable">
                  <span :class="{
                        'hidden': !buttonLoader,
                        'flex': buttonLoader
                  }">
                        <span class="loader-o20 !text-[9px] mx-auto !text-black"></span>
                  </span>
                  <span x-show="!buttonLoader">{{ __('Continue') }}</span>
               </button>
            </form>
          </div>
         </div>
      </div>
  </div>
  @script
      <script>
          Alpine.data('builder__new_page', () => {
             return {
               buttonLoader: false,
               address: @entangle('address'),
               addressAvailable: null,
               backendError: null,
               type: '-',
               openModal: false,
               renderMobile: true,
               selectedTemplate: @entangle('selectedTemplate').live,
               selectedCloneTemplate: null,
               preview: {
                  name: '',
                  url: null,
                  address: null,
                  isFree: false,
                  price: null,
                  id: null,
               },
               cloneTemplateDiv(item_id){
                  let $this = this;
                  html2canvas(this.$root.querySelector('.templateDiv' + item_id), {
                     useCORS: true,
                     allowTaint: true,
                     onclone: function(doc){
                        doc.querySelectorAll('.builder-section-add-wrapper').forEach(e => {
                           e.remove();
                        });
                     },
                  }).then(canvas => {
                        let image = canvas.toDataURL('image/png');
                        $this.selectedCloneTemplate = image;
                  });
               },
               removeSelectedTemplate(){
                  this.selectedTemplate = null;
                  this.$wire.removeSelectedTemplate();
               },
               selectTemplate(item_id){
                  this.selectedTemplate = item_id;
                  this.type = 'blank';

                  // this.cloneTemplateDiv(item_id);
               },
               openPreview(name, url, isFree, price, id){
                  this.preview.url = null;

                  this.preview.name = name;
                  this.preview.url = url;
                  this.preview.isFree = isFree == 'true' ? true : false;
                  this.preview.price = price;
                  this.preview.id = id;

                  // console.log(this.preview, isFree, price);
                  this.openModal = true;
               },

               get pageName(){
                  if(this.type == '-'){
                     return '{{ __('Page Type') }}';
                  }
                  
                  if(this.type == 'template'){
                     return '{{ __('Select Template') }}';
                  }
                  
                  if(this.type == 'blank' && this.selectedTemplate){
                     return '{{ __('Continue with template') }}';
                  }
                  
                  if(this.type == 'blank'){
                     return '{{ __('Blank Page') }}';
                  }
               },
               _createPage(){
                  this.$wire.createPage();
                  this.buttonLoader=true;
               },
               checkAddress(){

                  let $this = this;
                  let address = this.address;
                  address = address.toString() // Cast to string
                              .toLowerCase() // Convert the string to lowercase letters
                              .normalize('NFD') // The normalize() method returns the Unicode Normalization Form of a given string.
                              .trim() // Remove whitespace from both sides of a string
                              .replace(/\s+/g, '-') // Replace spaces with -
                              .replace(/[^\w\-]+/g, '') // Remove all non-word chars
                              .replace(/\-\-+/g, '-');
                  this.address = address;

                  $this.backendError = null;
                  $this.addressAvailable = null;
                  $this.buttonLoader = true;
                  clearTimeout($this.autoSaveTimer);

                  $this.autoSaveTimer = setTimeout(function(){
                     $this.$store.builder.savingState = 0;
                     
                     $this.$wire.checkAddress(address).then(r => {
                        $this.buttonLoader = false;
                        if(r.status == 'error'){
                           $this.backendError = r.response;
                           $this.addressAvailable = null;
                        }
                        if(r.status == 'success'){
                           $this.backendError = null;
                           $this.addressAvailable = r.response;
                        }
                     });

                  }, $this.$store.builder.autoSaveDelay);
               },
               init(){
                  let $this = this;

                  Livewire.hook('request', ({ uri, options, payload, respond, succeed, fail }) => {
                     // Runs after commit payloads are compiled, but before a network request is sent...
                  
                     respond(({ status, response }) => {
                        // Runs when the response is received...
                        // "response" is the raw HTTP response object
                        // before await response.text() is run...
                     })
                  
                     succeed(({ status, json }) => {
                        // $this.$store.builder.rescaleDiv($this.$root)
                     })
                  
                     fail(({ status, content, preventDefault }) => {
                        // Runs when the response has an error status code...
                        // "preventDefault" allows you to disable Livewire's
                        // default error handling...
                        // "content" is the raw response content...
                     })
                  });
               }
             }
          });
      </script>
  @endscript
</div>