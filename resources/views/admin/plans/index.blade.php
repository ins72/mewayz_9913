<x-layouts.app>
  <x-slot:title>{{ __('Plans') }}</x-slot>


  <script>
    function plan_pine(){
  
      return {
        create_modal: false,
        add_user: false,
        modals: [],
        initSortable() {
            const element = this.$root.querySelector('.sortable-wrapper');
            const delay = parseInt(element.dataset.delay);

                let sort = Sortable.create(element, {
                    animation: 150,
                    forceFallback: true,
                    sort: true,
                    scroll: true,
                    scrollSensitivity: 100,
                    delay: delay,
                    delayOnTouchOnly: true,
                    group: false,
                    handle: '.handle',
                    swapThreshold: 5,
                    filter: ".disabled",
                    preventOnFilter: true,
                    containment: "parent",
                    onUpdate: (e) => {
                        const sortableItems = element.querySelectorAll('.sortable-item');
                        let data = Array.from(sortableItems).map((elm, i) => ({
                            id: elm.dataset.id,
                            position: i
                        }));

                            fetch(element.dataset.route, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ data })
                            });
                    },
                    onMove: function(e) {
                        // You can add logic for movement here if needed
                    }
                });
            },
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
          this.initSortable();
        }
      }
    }
  </script>
  
  <div class="h-full pb-16" x-data="plan_pine">
    <div class="mb-6 ">
      <div class="flex flex-col mb-4">
        <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center">
          <span class="whitespace-nowrap">{{ __('Admin') }}</span>
        </div>
        <div class="border-b border-solid border-gray-300 w-[100%] my-4 flex"></div>
         
         <div class="flex items-center h-6">
            <h2 class="text-lg font-semibold ">
                {!! __icon('Delivery', 'Delivery, Shipment, Packages.4', 'w-6 h-6 inline-block') !!}
                <span class="ml-2">{{ __('Plans') }}</span>
            </h2>
         </div>
         <div class="flex flex-col gap-4 mt-4 lg:flex-row">
            
            <a @click="create_modal=true" class="yena-button-stack --black">
               {{ __('Create new') }}
            </a>
            
            <a @click="add_user=true" class="cursor-pointer yena-button-stack">
              {{ __('Attach user to plan') }}
            </a>
         </div>
      </div>
   </div>
   
    <div x-show="add_user" x-cloak x-transition.opacity.duration.500ms="" class="alpine-dialog-modal">
  
      <div class="-alpine-modal-overflow" x-on:click="add_user = false"></div>
      <div class="-alpine-dialog my-auto bg-white rounded-large shadow-xl transform transition-all w-[calc(100%_-_50px)] sm:w-full sm:max-w-xl sm:mx-auto" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-show="add_user !== false">
         <div>
            @includeIf('admin.plans.add-user')
         </div>
      </div>
    </div>
  
    <div x-show="create_modal" x-cloak x-transition.opacity.duration.500ms="" class="alpine-dialog-modal">
  
      <div class="-alpine-modal-overflow" x-on:click="create_modal = false"></div>
      <div class="-alpine-dialog my-auto bg-white rounded-large shadow-xl transform transition-all w-[calc(100%_-_50px)] sm:w-full sm:max-w-xl sm:mx-auto" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-show="create_modal !== false">
         <div>
            @includeIf('admin.plans.new')
         </div>
      </div>
    </div>
  
    <div class="mx-auto w-[100%] max-w-screen-xl pb-10 pt-0">
      @if ($plans->isEmpty())
        
      <div>
        <p class="mt-2 text-xs text-gray-600 flex items-center gap-1"><i class="fi fi-rr-triangle-warning"></i> {{ __('No available plan(s)') }}</p>
      </div>
    
      @endif
    
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-2 sortable-wrapper" data-delay="150" data-route="{{ route('dashboard-admin-plans-post', 'sort') }}">
        
      @foreach ($plans as $item)
      <div class="block rounded-sm hover:bg-gray-100 bg-white sortable-item" data-id="{{ $item->id }}" style="">
        <div class="group flex items-center gap-4 px-5 py-3">
           <a class="flex flex-grow items-center gap-4 overflow-x-hidden cursor-pointer">
              <div class="rounded-md px-0.5 py-1">
                 <div class="">
                    <i class="fi fi-rr-box"></i>
                 </div>
              </div>
    
              <div>
                <div class="flex w-[100%]">
                   <div class="truncate text-sm font-medium">{{ $item->name }}</div>
                </div>
                <div class="flex items-center gap-1 truncate text-sm text-gray-500">{{ number_format($item->subscribers()->paid()->notCancelled()->count()) }} {{ __('users') }}</div>
             </div>
           </a>
           <div class="flex items-center text-gray-500">
              <a class="w-[50px] h-[calc(var(--unit)*_2.4)] rounded-[var(--r-full)] text-[var(--background)] bg-[var(--foreground)] text-[12px] flex justify-center items-center cursor-pointer mr-2 handle">
                <i class="ph ph-arrows-out-cardinal"></i>
              </a>
    
              <a x-on:click="openModal('plan-' + {{ $item->id }})" class="w-[50px] h-[calc(var(--unit)*_2.4)] rounded-[var(--r-full)] text-[var(--background)] bg-[var(--foreground)] text-[12px] flex justify-center items-center cursor-pointer mr-2">
                {{ __('Edit') }}
              </a>
    
              
              <div>
                <div x-cloak x-show="isShownModal('plan-' + {{ $item->id }})" x-transition.opacity.duration.500ms class="alpine-dialog-modal">
                  <div class="-alpine-modal-overflow" x-on:click="closeModal('plan-' + {{ $item->id }})"></div>

                    <div class="-alpine-dialog my-auto bg-white rounded-large shadow-xl transform transition-all w-[calc(100%_-_50px)] sm:w-full sm:max-w-xl sm:mx-auto" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-show="isShownModal('plan-' + {{ $item->id }})">


                      <div>
                        @includeIf('admin.plans.edit')
                      </div>
                  </div>
                </div>
            </div>
            
              
              
              <x-parts.--delete>
                <x-slot name="content">{{ __('Are you sure you want to delete this plan? This action is irreversible.') }}</x-slot>
    
                <x-slot name="handle">
                  <a class="h-[calc(var(--unit)*_2.4)] rounded-[var(--r-full)] text-[12px] flex justify-center items-center cursor-pointer bg-red-400 text-white
                   px-2 --open-delete cursor-pointer">
                      <i class="fi fi-rr-trash"></i>
                      {{ __('Delete') }}
                  </a>
                </x-slot>
    
                <x-slot name="form">
                  <form action="{{ route('dashboard-admin-plans-post', 'delete') }}" method="post">
                    @csrf
                    <input type="hidden" name="_id" value="{{ $item->id }}">
                    <button type="submit" class="first-letter: bg-red-500  text-white disabled:opacity-75 hover:bg-red-400
                           block appearance-none rounded-lg text-sm font-medium duration-100 focus:outline-transparent px-3 py-1.5">
                           <div class="relative flex items-center justify-center ">
                            <div class="duration-100">{{ __('Delete') }}</div>
                          </div>
                    </button>
                  </form>
                </x-slot>
              </x-parts.--delete>
           </div>
        </div>
     </div>
      @endforeach
    
      </div>
    </div>
  </div>
</x-layouts.app>