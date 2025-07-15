<x-layouts.app>
   <x-slot:title>{{ __('Sites') }}</x-slot>
 
<script>
  function _pine(){
     return {
        create_modal: false,
        modals: [],
        _delete: '',

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

<div x-data="_pine">
   <div>
   <div class="mb-6 ">
     <div class="flex flex-col mb-4">
       <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center">
         <span class="whitespace-nowrap">{{ __('Admin') }}</span>
       </div>
       <div class="border-b border-solid border-gray-300 w-[100%] my-4 flex"></div>
        
        <div class="flex items-center h-6">
           <h2 class="text-lg font-semibold ">
               {!! __icon('interface-essential', 'browser-internet-web-network-window-app-icon', 'w-6 h-6 inline-block') !!}
               <span class="ml-2">{{ __('All Sites') }}</span>
           </h2>
        </div>
        <div class="flex flex-col gap-4 mt-4 lg:flex-row">
           <a @click="create_modal=true" class="cursor-pointer yena-button-stack">
             {{ __('Create new') }}
           </a>
           </div>
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
      
         <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Site') }}</header>
         <hr class="yena-divider">
      
         <form action="{{ route('dashboard-admin-website-post', 'create') }}" class="px-6 pb-6 pt-4" method="POST">
           @csrf
           <div class="grid grid-cols-1 gap-4 mb-5">
             <div class="form-input">
               <label>{{ __('Name') }}</label>
               <input type="text" name="name">
             </div>
           </div>
           <div class="form-input mt-4">
             <label class="initial">{{ __('Owner') }}</label>
             <select name="user_id">
               @foreach (\App\Models\User::get() as $user)
                  <option value="{{ $user->id }}">{{ $user->name }}</option>
               @endforeach
             </select>
           </div>
           
           <button type="submit" class="yena-button-stack w-[100%] mt-4">
               <div class="relative flex items-center justify-center ">
                 <div class="duration-100 undefined false">{{ __('Save') }}</div>
               </div>
           </button>
         </form>
       </div>
      </div>
      
     
   </div>
 </div>

   <div class="mx-auto w-full max-w-screen-xl px-2.5 pb-10 pt-0">

   <div class="page-trans bg-gray-200">

      
      <ul class="col-span-1 grid auto-rows-min grid-cols-1 gap-3 lg:col-span-5">
         @foreach ($sites as $key => $item)
            <div class="yena-linkbox !shadow-none" {!! __key($item->_slug) !!}>
               <div class="flex flex-row h-full">
                  <div class="w-1/4">
                     <div class="-thumbnail">
                        <div class="--thumbnail-inner" x-intersect="$store.builder.rescaleDiv($root)">
         
                           <div class="page-type-options !p-0 !m-0">
                              <div class="page-type-item !h-[130px] !h-zz[120px]">
                                 <div class="container-small edit-board overflow-hidden !origin-[0px_0px]">
                                    <div class="card">
                                       <div class="card-body pointer-event-none relative" wire:ignore>
                                          @if ($staticPreview = $item->staticSitePreview())
                                          <img src="{{ $staticPreview->thumbnail }}" class="object-cover object-top w-full h-full" alt="">
                                          @else
                                          <livewire:site.generate lazy :site="$item" :key="uukey('admin-sites', 'all-' . $item->_slug)" />
                                          @endif
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
         
                  <div class="-content">
                     <a claas="--over-lay" href="{{ route('dashboard-builder-index', ['slug' => $item->_slug]) }}">
                        <p class="--title !h-auto">{{ $item->name }}</p>
                     </a>
         
                     @if ($item->createdBy())
                     <div class="flex flex-col mt-0 w-[100%]">
                        <a href="{{ route('dashboard-admin-users-index', ['query' => $item->user->email, 'search_by' => 'email']) }}" class="flex justify-between mt-1 w-[100%] items-start bg-gray-100 p-2 rounded-lg">
                           <div class="flex items-center">
                              <div class="relative">
                                 <div class="--avatar">
                                    <img src="{{ $item->createdBy()->getAvatar() }}" class="w-[100%] h-full object-cover" alt="">
                                 </div>
                              </div>
                              <div class="mx-2 w-[100%]">
                                 <p class="meta-text truncate w-[100%] break-all text-xs">
                                    {{ __('Created by :who', [
                                       'who' => $item->createdBy()->name
                                    ]) }}
                                 </p>
                                 <p class="meta-text truncate w-[100%] break-all text-[11px] text-[color:var(--yena-colors-gray-400)]">{{ __('Last edited :date', ['date' => \Carbon\Carbon::parse($item->updated_at)->diffForHumans()]) }}</p>
                              </div>
                           </div>
                        </a>
                     </div>
                     @endif
                  </div>

                  <div class="flex items-center z-menuc ml-auto pr-5" data-max-width="400" data-placement="left-end" data-appends-to=".--appended" wire:ignore.self data-handle=".--control">
                     <p class="mr-3 hidden whitespace-nowrap text-sm text-gray-500 sm:block">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</p>
                     <p class="mr-1 whitespace-nowrap text-sm text-gray-500 sm:hidden">{{ \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($item->created_at)) }}d</p>
      
      
                     <div>
                        <button type="button" class="rounded-md px-1 py-2 transition-all duration-75 hover:bg-gray-100 active:bg-gray-200 --control">
                           <i class="fi fi-rr-menu-dots-vertical flex text-base text-gray-500"></i>
                        </button>
                     </div>
      
                     
      
                     <div class="--appended" wire:ignore></div>
      
                     <div class="z-menuc-content-temp" wire:ignore.self>
                        <ul class="grid w-full gap-1 p-5 sm:w-48">
                           <a class="group flex w-full items-center justify-between rounded-md p-2 text-left text-sm font-medium text-gray-500 transition-all duration-75 hover:bg-gray-100 cursor-pointer" href="{{ route('dashboard-builder-index', ['slug' => $item->_slug]) }}">
                                 <div class="flex items-center justify-start space-x-2">
                                    <i class="fi fi-rr-paint-brush text-sm"></i>
                                    <p class="text-sm">{{ __('Edit') }}</p>
                                 </div>
                           </a>
                           
                          
                          <a class="group flex w-full items-center  justify-between rounded-md p-2 text-left text-sm font-medium  text-red-600 transition-all duration-75 hover:bg-red-600  hover:text-white --open-delete cursor-pointer" @click="_delete={
                           id: '{{ $item->id }}',
                           route: '{{ route('dashboard-admin-sites-post', ['tree' => 'delete']) }}',
                       }">
                             <div class="flex items-center justify-start space-x-2">
                                <i class="fi fi-rr-trash text-sm"></i>
                                <p class="text-sm">{{ __('Delete') }}</p>
                             </div>
                          </a>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         @endforeach
      </ul>
      {{ $sites->links() }}
   </div>

   
    
   <template x-teleport="body">
      <div class="overlay backdrop delete-overlay" :class="{
          '!block': _delete
      }" @click="_delete=false">
          <div class="delete-site-card !border-0 !rounded-2xl !shadow-lg" @click="$event.stopPropagation()">
             <form method="post" :action="_delete.route" class="overlay-card-body !rounded-2xl">
              @csrf
              <input type="hidden" :value="_delete.id" name="_id">
                <h2>{{ __('Delete Site?') }}</h2>
                <p class="mt-4 mb-4 text-[var(--t-m)] text-center leading-[var(--l-body)] text-[var(--c-mix-3)] w-3/5 ml-auto mr-auto">{{ __('Are you sure you want to delete this site? Once deleted, you will not be able to restore it.') }}</p>
                <div class="card-button pl-[var(--s-2)] pr-[var(--s-2)] pt-[0] pb-[var(--s-2)] flex gap-2">
                   <button class="btn btn-medium neutral !h-[calc(var(--unit)*_4)] !text-black !bg-[var(--c-mix-1)]" type="button" @click="_delete=false">{{ __('Cancel') }}</button>
  
                   <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-[calc(var(--unit)*_4)]">{{ __('Yes, Delete') }}</button>
                </div>
             </form>
          </div>
       </div>
  </template>
 </div>
</div>
</x-layouts.app>