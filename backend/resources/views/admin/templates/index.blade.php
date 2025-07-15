<x-layouts.app>
    <x-slot:title>{{ __('Templates') }}</x-slot>
  
  
  <script>
    function alpine(){
       return {
          create_modal: false,
          filter_modal: false,
          _delete: '',
          _edit: '',
          modals: [],
  
          openModal(id){
            var _this = this;
            if(!_this.$event.target.classList.contains('app-un')){
                this.modals[id] = true;
            }
          },
  
          closeModal(id){
            this.modals[id] = false;
          },
  
          isShownModal(id){
            if(this.modals[id]){
                return true;
            }
  
            return false;
          },
          init(){
            
          }
       }
    }
  </script>
  
  <div x-data="alpine">
    <div class="mb-6 ">
      <div class="flex flex-col mb-4">
        <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center">
          <span class="whitespace-nowrap">{{ __('Admin') }}</span>
        </div>
        <div class="border-b border-solid border-gray-300 w-[100%] my-4 flex"></div>
         
         <div class="flex items-center h-6">
            <h2 class="text-lg font-semibold ">
                {!! __icon('interface-essential', 'thunder-lightning-notifications', 'w-6 h-6 inline-block') !!}
                <span class="ml-2">{{ __('All Templates') }}</span>
            </h2>
         </div>
         <div class="flex flex-col gap-4 mt-4 lg:flex-row">
            <a @click="create_modal=true" class="cursor-pointer yena-button-stack">
              {{ __('Create new') }}
            </a>
         </div>
      </div>
   </div>
    <div x-show="create_modal" x-cloak x-transition.opacity.duration.500ms="" class="alpine-dialog-modal">
  
      <div class="-alpine-modal-overflow" x-on:click="create_modal = false"></div>
      
        <div class="-alpine-dialog my-auto bg-white rounded-large shadow-xl transform transition-all w-[calc(100%_-_50px)] sm:w-full sm:max-w-xl sm:mx-auto" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-show="create_modal !== false">
         <div>
          <div class="h-full overflow-y-auto">
            <a @click="create_modal=false" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
               <i class="fi fi-rr-cross text-sm"></i>
            </a>
         
            <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Template') }}</header>
            <hr class="yena-divider">
         
            <form action="{{ route('console-admin-templates-post', 'create') }}" class="px-6 pb-6 pt-4" method="POST">
              @csrf
              <div class="grid grid-cols-2 gap-4 mb-5">
                <div class="form-input">
                  <label>{{ __('Name') }}</label>
                  <input type="text" name="name">
                </div>
                <div class="custom-content-input border-2 border-dashed mb-1">
                    <label class="h-10 !flex items-center px-5">
                        {!! \Currency::symbol(settings('payment.currency')) !!}
                    </label>
                    <input type="text" name="price" value="0" placeholder="{{ __('Price of template') }}" class="w-[100%] !bg-gray-100">
                </div>
              </div>
              
              <button type="submit" class="yena-button-stack w-[100%]">
                  <div class="relative flex items-center justify-center ">
                    <div class="duration-100 undefined false">{{ __('Save') }}</div>
                  </div>
              </button>
            </form>
          </div>
         </div>
         
        
      </div>
    </div>
    
    <div class="template grid !grid-cols-1 md:!grid-cols-2 lg:!grid-cols-3">
        @foreach ($templates as $item)
        @php
            if(!$item->site) continue;
        @endphp
        <div class="template-container bg-white" style="--template-price: '{!! $item->price == 0.00 ? __('Free') : price_with_cur(\Currency::symbol(settings('payment.currency')), $item->price) !!}'">
           <label class="template-block">
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
                                          <livewire:site.generate lazy :site="$item->site" :key="uukey('site-page', 'admin-site-template-' . $item->site->_slug)" />
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
           <div class="template-footer-button !grid grid-cols-2 !h-[initial] gap-4 !p-[20px]">
                <a class="btn !w-full" @click="_edit={
                    route: '{{ route('console-admin-templates-post', ['tree' => 'edit']) }}',
                    id: '{{ $item->id }}',
                    name: '{{ $item->name }}',
                    price: '{{ $item->price }}',
                }" >{{ __('Edit') }}</a>
                
                <a class="btn !w-full !bg-black !text-white" href="{{ route('console-builder-index', ['slug' => $item->site->_slug]) }}">{{ __('Build') }}</a>

                <button class="btn col-span-2 !bg-[var(--c-red)] !w-full" @click="_delete={
                    id: '{{ $item->id }}',
                    route: '{{ route('console-admin-templates-post', ['tree' => 'delete']) }}',
                }">{{ __('Delete') }}</button>
           </div>
        </div>
        @endforeach
     </div>

    <div class="mt-5">
        {!! $templates->links() !!}
    </div>
    
    <template x-teleport="body">
        <div class="overlay backdrop delete-overlay" :class="{
            '!block': _delete
        }" @click="_delete=false">
            <div class="delete-site-card !border-0 !rounded-2xl !shadow-lg" @click="$event.stopPropagation()">
               <form method="post" :action="_delete.route" class="overlay-card-body !rounded-2xl">
                @csrf
                <input type="hidden" :value="_delete.id" name="_id">
                  <h2>{{ __('Delete Template?') }}</h2>
                  <p class="mt-4 mb-4 text-[var(--t-m)] text-center leading-[var(--l-body)] text-[var(--c-mix-3)] w-3/5 ml-auto mr-auto">{{ __('Are you sure you want to delete this template? Once deleted, you will not be able to restore it.') }}</p>
                  <div class="card-button pl-[var(--s-2)] pr-[var(--s-2)] pt-[0] pb-[var(--s-2)] flex gap-2">
                     <button class="btn btn-medium neutral !h-[calc(var(--unit)*_4)] !text-black !bg-[var(--c-mix-1)]" type="button" @click="_delete=false">{{ __('Cancel') }}</button>
    
                     <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-[calc(var(--unit)*_4)]">{{ __('Yes, Delete') }}</button>
                  </div>
               </form>
            </div>
         </div>
    </template>
    <div x-show="_edit" x-cloak x-transition.opacity.duration.500ms="" class="alpine-dialog-modal">
  
      <div class="-alpine-modal-overflow" x-on:click="_edit = false"></div>
      
        <div class="-alpine-dialog my-auto bg-white rounded-large shadow-xl transform transition-all w-[calc(100%_-_50px)] sm:w-full sm:max-w-xl sm:mx-auto" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-show="_edit !== false">
         <div>
          <div class="h-full overflow-y-auto">
            <a @click="_edit=false" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
               <i class="fi fi-rr-cross text-sm"></i>
            </a>
         
            <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Edit Template') }}</header>
            <hr class="yena-divider">
         
            <form :action="_edit.route" class="px-6 pb-6 pt-4" method="POST">
              @csrf
              <input type="hidden" :value="_edit.id" name="_id">
              <div class="grid grid-cols-2 gap-4 mb-5">
                <div class="form-input">
                  <label>{{ __('Name') }}</label>
                  <input type="text" name="name" :value="_edit.name">
                </div>
                <div class="custom-content-input border-2 border-dashed mb-1">
                    <label class="h-10 !flex items-center px-5">
                        {!! \Currency::symbol(settings('payment.currency')) !!}
                    </label>
                    <input type="text" name="price" :value="_edit.price" value="0" placeholder="{{ __('Price of template') }}" class="w-[100%] !bg-gray-100">
                </div>
              </div>
              
              <button type="submit" class="yena-button-stack w-[100%]">
                  <div class="relative flex items-center justify-center ">
                    <div class="duration-100 undefined false">{{ __('Save') }}</div>
                  </div>
              </button>
            </form>
          </div>
         </div>
         
        
      </div>
    </div>

  </div>
  </x-layouts.app>