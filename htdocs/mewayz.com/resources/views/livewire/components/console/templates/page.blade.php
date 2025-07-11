
<?php

   use App\Models\Site;
   use App\Models\YenaTemplate;
   use Opis\Closure\SerializableClosure;       
   use function Livewire\Volt\{state, mount, placeholder, on};

   mount(function(){
      $this->get();
   });

   placeholder('placeholders.console.sites.page-placeholder');

   state([
      'templates' => [],
      'lol' => '',
   ]);
   
   $get = function() {
    $this->templates = YenaTemplate::get();
   };
?>

<div>
    
   <div class="mb-6 " x-data="sites__templates">

      <template x-teleport="body">
         <div x-cloak x-show="openModal">
   
            <div class="fixed inset-0 z-[99999] top-0 left-0 bg-gradient-to-b from-white to-[#efefef] bg-fixed" :class="{
               'hidden': !openModal,
            }">
               <div class="yena-theme-preview-bar w-full px-4 sm:px-6">
                   <div class="yena-theme-preview-info flex items-center">
                        <a class="yena-theme-preview-back flex" @click="openModal=false; preview.url=null">
                           {{-- <span class="hidden sm:inline text-sm">{{ __('All Templates') }}</span> --}}
                              <span class="inline zzsm:hidden mr-2 relative z-top-[1px] text-sm">&lt;-</span>
                        </a>
                       <span class="hidden sm:inline text-sm">/</span>
                       <h1 class="text-gray-900 font-sans text-sm" x-text="preview.name"></h1>
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
               <section class="yena-section yena-section-theme-preview my-0 mx-auto max-w-10xl w-full px-4 sm:px-6">
                   <div class="my-0 mx-auto max-w-8xl w-full">
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
     

   
    <div class="flex flex-col mb-4 console-template-header">
         
        <div class="flex items-start flex-col">
           <h2 class="text-lg font-semibold ">
                {!! __icon('interface-essential', 'thunder-lightning-notifications', 'w-6 h-6 inline-block') !!}

              <span class="ml-2">{{ __('Templates') }}</span>
           </h2>
           <p>{{ __('Easy to customize, no code required') }}</p>
        </div>
     </div>

     
     <div class="mt-4">
        <div class="template grid !grid-cols-1 md:!grid-cols-2 lg:!grid-cols-3">
            @foreach ($templates as $item)
            @php
                if(!$item->site) continue;
            @endphp
            <div class="template-container bg-white" style="--template-price: '{!! $item->isFree() ? __('Free') : price_with_cur(\Currency::symbol(settings('payment.currency')), $item->price) !!}'">
               <label class="template-block" @click="openPreview('{{ $item->name }}', '{{ $item->site->getAddress() }}', '{{ !$item->isFree() && !$item->isPurchased() ? 'false' : 'true' }}', '{!! price_with_cur(\Currency::symbol(settings('payment.currency')), $item->price) !!}', '{{ $item->id }}')">
                  <div class="template-image">
                     <div class="template-content">
                        <div class="w-full" x-intersect="$store.builder.rescaleDiv($root)">
                            <div class="page-type-options zzmax-w-[360px] !p-0 !m-0">
                               <div class="page-type-item !h-[312px]">
                                  <div class="container-small edit-board overflow-hidden !origin-[0px_0px] ![height:initial]">
                                     <div class="card">
                                        <div class="card-body pointer-event-none relative" wire:ignore>
                                            <div>
                                             @if ($staticPreview = $item->site->staticSitePreview())
                                             <img src="{{ $staticPreview->thumbnail }}" class="object-cover object-top w-full h-full" alt="">
                                             @else
                                             <livewire:site.generate lazy :site="$item->site" :key="uukey('site-page', 'site-template-' . $item->site->_slug)" />
                                             @endif
                                            </div>
                                           <div class="absolute h-full w-full z-[2] bottom-0 left-0"></div>
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
                <button class="btn" @click="$dispatch('open-modal', 'create-template-modal'); selectedTemplate='{{ $item->id }}'">{{ __('Use Template') }}</button>
                @endif
               </div>
            </div>
            @endforeach
         </div>
      </div>
   
      <template x-teleport="body">
          <x-modal name="create-template-modal" :show="false" removeoverflow="true" maxWidth="xl" >
          <livewire:components.console.templates.create-modal lazy :key="uukey('sites', 'create-template-modal-')"/>
          </x-modal>
      </template>
      <template x-teleport="body">
          <x-modal name="buy-template-modal" :show="false" removeoverflow="true" maxWidth="xl" >
          <livewire:components.console.templates.buy-modal lazy :key="uukey('sites', 'buy-template-modal-')"/>
          </x-modal>
      </template>
   </div>

   @script
   <script>
       Alpine.data('sites__templates', () => {
          return {
            openModal: false,
            renderMobile: false,
            previewUrl: null,
            selectedTemplate: null,
            preview: {
               name: '',
               url: null,
               address: null,
               isFree: false,
               price: null,
               id: null,
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
            init(){
               if(this.$store.builder.detectMobile()){
                  // this.renderMobile = true;
               }
            }
          }
       });
   </script>
   @endscript
</div>