<x-layouts.app>
  <x-slot:title>{{ __('Users') }}</x-slot>


<script>
  function _pine(){
     return {
        create_modal: false,
        filter_modal: false,
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

<div x-data="_pine">
  <div class="mb-6 ">
    <div class="flex flex-col mb-4">
      <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center">
        <span class="whitespace-nowrap">{{ __('Admin') }}</span>
      </div>
      <div class="border-b border-solid border-gray-300 w-[100%] my-4 flex"></div>
       
       <div class="flex items-center h-6">
          <h2 class="text-lg font-semibold ">
              {!! __icon('Users', 'single-user-add-plus_1', 'w-6 h-6 inline-block') !!}
              <span class="ml-2">{{ __('All Users') }}</span>
          </h2>
       </div>
       <div class="flex flex-col gap-4 mt-4 lg:flex-row">
          <a @click="create_modal=true" class="cursor-pointer yena-button-stack">
            {{ __('Create new') }}
          </a>
          <div class="flex">
            <a class="yena-button-stack cursor-pointer" @click="filter_modal=true">
              <div class="flex h-full items-center">
                  <div class="px-2 py-0.5 text-sm font-medium"><i class="fi fi-rr-filter"></i></div>
              </div>
          </a>
          </div>
       </div>
    </div>
 </div>

 <div x-show="filter_modal" x-cloak x-transition.opacity.duration.500ms="" class="alpine-dialog-modal">

  <div class="-alpine-modal-overflow" x-on:click="filter_modal = false"></div>
  
    <div class="-alpine-dialog my-auto bg-white rounded-large shadow-xl transform transition-all w-[calc(100%_-_50px)] sm:w-full sm:max-w-xl sm:mx-auto" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-show="filter_modal !== false">
     <div>
      <div class="h-full overflow-y-auto">
        <a @click="filter_modal=false" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
           <i class="fi fi-rr-cross text-sm"></i>
        </a>
     
        <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Filter User') }}</header>
        <hr class="yena-divider">
     
        <div class="px-6 pb-6 pt-4">

          @includeIf('admin.users.filter')
        </div>
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
       
          <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create User') }}</header>
          <hr class="yena-divider">
       
          <form action="{{ route('console-admin-users-post', 'create') }}" class="px-6 pb-6 pt-4" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-5">
              <div class="form-input">
                <label>{{ __('Name') }}</label>
                <input type="text" name="name">
              </div>
              <div class="form-input">
                <label>{{ __('Email') }}</label>
                <input type="email" name="email">
              </div>
            </div>
            <div class="form-input mb-4">
              <label>{{ __('Password') }}</label>
              <input type="password" name="password">
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
  

<div class="mx-auto w-full max-w-screen-xl pb-10 pt-0">
  <div class="page-trans">
    <div class="flex-table">
       <div class="flex-table-header">
          <span class="is-grow">{{ __('User') }}</span>
          <span class="justify-center">{{ __('Last Activity') }}</span>
          <span class="justify-center">{{ __('Status') }}</span>
          <span class="justify-center">{{ __('Registration') }}</span>
          <span class="cell-end">{{ __('Actions') }}</span>
       </div>
       @foreach ($users as $_user)
       <div class="flex-table-item rounded-2xl overflow-x-auto shadow-none">
          <div class="flex-table-cell is-media is-grow" data-th="{{ __('User') }}">
             <div>
                  <div class="flex min-w-[10rem] max-w-[15rem] gap-2 ml-4 md:ml-0">
                    <div class="relative flex-none">
                      <div class="relative">
                          <div class="sj-avatar-container">
                            <img src="{{ $_user->getAvatar() }}" alt=" " class="w-10 h-10 rounded-full" referrerpolicy="no-referrer">
                          </div>
                          <div class="absolute -bottom-2 -right-1">
                            <div class="rounded-full bg-white/75 p-1 backdrop-blur-sm">
                                <div class=""></div>
                            </div>
                          </div>
                      </div>
                    </div>
                    <div class="flex flex-col justify-center truncate">
                      <div class="truncate whitespace-pre-wrap text-gray-900">{{ $_user->name }}</div>
                      <div class="truncate whitespace-pre-wrap">{{ $_user->email }}</div>
                    </div>
                </div>
             </div>
          </div>
          <div class="flex-table-cell !px-0" data-th="{{ __('Last Activity') }}">

            <div class="h-full min-w-[18rem] max-w-[25rem] flex items-center justify-center">
                <div class="row-content truncate whitespace-pre-wrap text-gray-600" >{{ \Carbon\Carbon::parse($_user->lastActivity)->toDayDateTimeString() }}</div>
            </div>
          </div>
          <div class="flex-table-cell !px-0" data-th="{{ __('Status') }}">
            <div class="ml-4 mt-2 flex flex-wrap gap-1">
              @if ($_user->status)
              <span class="rounded-full px-2 py-1 text-xs font-medium bg-green-400 text-black">{{ __('Active') }}</span>
              @else
              <span class="rounded-full px-2 py-1 text-xs font-medium bg-red-500 text-white">{{ __('Disabled') }}</span>
              @endif
            </div>
          </div>
          <div class="flex-table-cell !px-0" data-th="{{ __('Registration') }}">
            <div class="flex-none text-xs ml-4">{{ \Carbon\Carbon::parse($_user->created_at)->toFormattedDateString() }}</div>
          </div>
          <div class="flex-table-cell cell-end !px-0" data-th="{{ __('Actions') }}">

            <div class="flex ml-4 md:ml-0">
              
              <a x-on:click="openModal('user-' + {{ $_user->id }})" class="rounded-full border-[1.5px] px-2 py-1 text-xs font-medium duration-200 bg-white shadow-xl border-amber-500/0 hover:border-amber-500 text-amber-600 flex items-center gap-1 --control cursor-pointer">
                <i class="fi fi-rr-attribution-pencil"></i>
                {{ __('Edit') }}
              </a>
              
              <div class="flex z-menuc ml-2" data-max-width="600" data-handle=".--control">
                  <a class="rounded-full border-[1.5px] px-2 py-1 text-xs font-medium duration-200 bg-red-400 shadow-xl text-white flex items-center gap-1 --control cursor-pointer">
                    <i class="fi fi-rr-trash"></i>
                    {{ __('Delete') }}
                  </a>

                  <div class="z-menuc-content-temp">
                      <ul class="z-menu-ul w-40em max-w-full shadow-lg border border-solid border-gray-200 rounded-xl">
                          <div class="p-6">
                            <i class="fi fi-rr-triangle-warning text-lg text-red-500"></i>
                            <div class="mt-1 text-sm text-gray-600">
                              <div>
                                <span slot="description">{{ __('Are you sure you want to delete this user? This action is irreversible.') }}</span>
                              </div>
                            </div>

                              <div class="border-b border-solid border-gray-300 my-3"></div>
                              <div class="mt-4 flex justify-end gap-2">
                                <button type="button" class=" block appearance-none rounded-md border bg-white text-sm font-medium text-gray-600 shadow-sm duration-100 focus:ring-0
                                px-3 py-1.5 block appearance-none rounded-lg text-sm font-medium duration-100 focus:outline-transparent px-3 py-1.5 z-menu-close">{{ __('Cancel') }}</button>
                                
                                <form action="{{ route('console-admin-users-post', 'delete') }}" method="post">
                                  @csrf
                                  <input type="hidden" name="_user" value="{{ $_user->id }}">
                                  <button type="submit" class="first-letter: bg-red-500  text-white disabled:opacity-75 hover:bg-red-400
                                         block appearance-none rounded-lg text-sm font-medium duration-100 focus:outline-transparent px-3 py-1.5">
                                         <div class="relative flex items-center justify-center "><div class="duration-100">{{ __('Delete') }}</div>
                                    </div>
                                </button>
                                </form>
                                        
                              </div>
                          </div>
                      </ul>
                  </div>
              </div>
            </div>
          </div>
       </div>
       @endforeach
    </div>
    {{ $users->links() }}
  </div>
</div>

  @foreach ($users as $item)
    <div>
      <div x-cloak x-show="isShownModal('user-' + {{ $item->id }})" x-transition.opacity.duration.500ms class="alpine-dialog-modal">
        <div class="-alpine-modal-overflow" x-on:click="closeModal('user-' + {{ $item->id }})"></div>
          <div class="-alpine-dialog my-auto bg-white rounded-large shadow-xl transform transition-all w-[calc(100%_-_50px)] sm:w-full sm:max-w-xl sm:mx-auto" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-show="isShownModal('user-' + {{ $item->id }})">
            <div>
              @includeIf('admin.users.edit', ['_user' => $item])
            </div>
        </div>
      </div>
  </div>
  @endforeach
</div>
</x-layouts.app>