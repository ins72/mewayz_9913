
<?php
   use App\Livewire\Actions\ToastUp;
   use App\Models\Audience;
   use App\Models\AudienceBroadcast;
   use App\Traits\AudienceTraits;
   use App\Models\AudienceFolder;
   use function Livewire\Volt\{state, mount, placeholder, on, with, rules, usesPagination, uses, updated};

   uses([AudienceTraits::class, ToastUp::class]);
   
   usesPagination();
   mount(function(){
      $this->loadData();
      $this->refreshBroadcast();
   });

   placeholder('placeholders.console.sites.page-placeholder');

   state([
      //'audience' => [],

      'folders' => [],
      'broadcast' => [],

      'query' => [
         'search' => '',
         'orderby' => 'DESC',
         'per_page' => 10,
         'search_by' => 'name',
         'created_by' => '-'
      ],
   ]);

   updated([
      'query.search' => fn() => $this->resetPage(),
   ]);

   on([
      'audienceRefresh' => function(){
         $this->resetPage();
      },
      'folderCreated' => function(){
         $this->loadData();
      },
      'broadcastRefresh' => function(){
         $this->refreshBroadcast();
      },
   ]);

   with(fn () => [
      'audience' => $this->getAudience(),
   ]);

   $loadData = function(){
     $this->folders = AudienceFolder::where('user_id', iam()->id)->orderBy('id', 'desc')->get();
   };

   $refreshBroadcast = function(){
      $this->broadcast = AudienceBroadcast::where('user_id', iam()->id)->orderBy('id', 'desc')->get();
   };

   $getAudience = function(){
      $paginate = 10;
      $audience = Audience::where('owner_id', iam()->id);

      $paginate = (int) $this->query['per_page'];
      if (!in_array($paginate, [10, 25, 50, 100, 250])) {
          $paginate = 10;
      }
      $searchBy = $this->query['search_by'];
      if (!in_array($searchBy, ['email', 'name'])) {
          $searchBy = 'name';
      }

      if (!empty($query = $this->query['search'])) {
         $audience = $audience->where("contact->$searchBy", 'LIKE','%'.$query.'%');
      }
      // Order Type
      $order_type = $this->query['orderby'];
      if (!in_array($order_type, ['ASC', 'DESC'])) {
         $order_type = 'DESC';
      }
        
      // Order Origin
      $created_by = $this->query['created_by'];
      switch ($created_by) {
          case 'me':
              $audience = $audience->where('extra->created_by', 1);
          break;
          case 'others':
              $audience = $audience->where('extra->created_by', 0);
          break;
      }


      $audience = $audience->orderBy('id', $order_type);
      // DO OTHER STUFF
      $audience = $audience->cursorPaginate(
            $paginate,
      );

      return $audience;
   };

   $deleteAudience = function($id){

      if(!$audience = Audience::where('id', $id)->where('owner_id', iam()->id)->first()) return;

      $au = $this->set($audience->id);
      storageDelete('media/audience/avatar', $au->info('avatar'));

      $audience->delete();

      $this->resetPage();
      $this->flashToast('success', __('Audience deleted'));
      $this->dispatch('audienceDeleted');
   };
   
   $getAnalytics = function(){
         $contacts = Audience::where('owner_id', iam()->id)->count();
         $audience = Audience::where('owner_id', iam()->id)->get();

         $countries = [];
         foreach ($audience as $item) {
               $au = $this->set($item);

               if(!empty($au->info('lastCountry'))) $countries[] = $au->info('lastCountry');
         }

         return [
               'contacts' => number_format($contacts),
               'country' => number_format(count($countries)),
         ];
    };
?>

<div>
   
   <style>
      .yena-app-wrapper > .yena-root-main > .yena-container, .yena-app-wrapper > .yena-root-main{
         padding: 0 !important;
      }
   </style>
   <div x-data="app_audience">
      <div
         class="flex border-b [border-bottom-color:var(--yena-colors-blackAlpha-100)] z-[5] w-full bg-white fixed bottom-0 lg:!sticky lg:!top-0 lg:![bottom:initial] h-[60px] !pl-[6px] pr-5">
         <div class="flex flex-row items-center">
            <div class="md:block">
               <button type="button" class="yena-button-o !text-[12px] md:!text-sm"
                  @click="page='-'">
                  <span class="--icon">
                     {!! __i('emails', 'email-hand', 'w-5 h-5') !!}
                  </span>

                  {{ __('Contacts') }}
               </button>
            </div>
            <div class="h-10 w-[1px] bg-[var(--yena-colors-blackAlpha-100)] inline-flex mx-[5px] md:mx-[20px]">
            </div>
            <div class="md:block">
               <button type="button" class="yena-button-o !text-[12px] md:!text-sm"
                  @click="page='folders'">
                  <span class="--icon">
                     {!! __i('emails', 'folder-email-mail-letter', 'w-5 h-5') !!}
                  </span>
                  {{ __('Folders') }}
               </button>
            </div>
            <div class="h-10 w-[1px] bg-[var(--yena-colors-blackAlpha-100)] inline-flex mx-[5px] md:mx-[20px]">
            </div>
            <div class="md:block">
               <button type="button" class="yena-button-o !text-[12px] md:!text-sm"
                  @click="page='broadcast'">
                  <span class="--icon">
                     {!! __i('emails', 'laptop-email-mail-letter', 'w-5 h-5') !!}
                  </span>
                  {{ __('Broadcast') }}
               </button>
            </div>
         </div>
         <div class="flex-1 pointer-events-none place-self-stretch"></div>
      </div>

      
      <div class="yena-root-main !w-full">
         <div class="banner">
            <div class="banner__container !bg-white">
              <div class="banner__preview">
               <template x-if="page=='-'">
                  {!! __icon('emails', 'email-mail-letter') !!}
               </template>
               <template x-if="page=='folders'">
                  {!! __i('emails', 'folder-email-mail-letter') !!}
               </template>
               <template x-if="page=='broadcast'">
                  {!! __i('emails', 'laptop-email-mail-letter') !!}
               </template>
              </div>
              <div class="banner__wrap">
               <template x-if="page=='-'">
                  <div>
                     <div class="banner__title h3 !text-black">{{ __('Leads') }}</div>
                     <div class="banner__text !text-black">{{ __('Create & manage your audience') }}</div>
                  </div>
               </template>
               <template x-if="page=='folders'">
                  <div class="banner__title h3 !text-black">{{ __('Folders') }}</div>
               </template>
               <template x-if="page=='broadcast'">
                  <div class="banner__title h3 !text-black">{{ __('Broadcast') }}</div>
               </template>
                <template x-if="page=='folders'">
                   <div class="mt-7 grid grid-cols-1 gap-1">
                     <div>
                       <div class="rounded-8 p-2 text-center col-span-2 lg:col-span-1 bg-[#f7f3f2] text-black">
                          <div class="detail text-gray-600">{{ __('Total folders') }}</div>
                          <div class="number-secondary">{{ count($folders) }}</div>
                       </div>
                      </div>
                   </div>
                </template>
                
               <template x-if="page=='-'">
                  <div class="mt-7 grid grid-cols-2 gap-1">
                    <div>
                      <div class="rounded-8 p-2 text-center col-span-2 lg:col-span-1 bg-[#f7f3f2] text-black">
                         <div class="detail text-gray-600">{{ __('Total contacts') }}</div>
                         <template x-if="analyticsLoading">
                             <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                         </template>
                         <template x-if="!analyticsLoading">
                             <div class="number-secondary" x-html="analytics.contacts"></div>
                         </template>
                      </div>
                     </div>
                     <div>
                       <div class="rounded-8 p-2 text-center bg-[#f7f3f2] text-black">
                          <div class="detail text-gray-600">{{ __('Total country') }}</div>
                          <template x-if="analyticsLoading">
                              <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                          </template>
                          <template x-if="!analyticsLoading">
                              <div class="number-secondary" x-text="analytics.country"></div>
                          </template>
                       </div>
                     </div>
                  </div>
               </template>
                <div class="mt-3 flex gap-2">
                  <template x-if="page=='-'">
                     <button class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'create-audience-modal');">{{ __('Create Contact') }}</button>
                  </template>
                    <template x-if="page=='folders'">
                        <button class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'create-folder-modal');">{{ __('Create Folder') }}</button>
                    </template>
                    <template x-if="page=='broadcast'">
                        <button class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'create-broadcast-modal');">{{ __('Create Broadcast') }}</button>
                    </template>
                </div>
              </div>
            </div>
          </div>


        

         <div x-cloak x-show="page=='broadcast'">
            <div>
               @if ($broadcast->isEmpty())
               <div>
                  <div class="flex flex-col justify-center items-start px-0 py-[60px]">
                     {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
                     <p class="mt-3 text-base text-left md:!text-2xl font-bold">
                        {!! __t('You have no audience folder. <br> Create a folder to get started.') !!}
                     </p>
                  </div>
               </div>
               @endif
               
               
               <div class="flex-table mt-0 flex flex-col gap-3">
                   <div class="flex-table-header !mb-0">
                       <span class="is-grow">{{ __('Info') }}</span>
                       <span>{{ __('Subject') }}</span>
                       <span>{{ __('Status') }}</span>
                       <span>{{ __('Audience') }}</span>
                       <span>{{ __('Date') }}</span>
                       <span class="cell-end">{{ __('Action') }}</span>
                   </div>
                   @foreach ($broadcast as $item)
                   <div class="flex-table-item rounded-2xl !bg-white !shadow-none !mb-0">
                       <div class="flex-table-cell is-media is-grow" data-th="">
                           <div class="flex relative cursor-pointer">
                           <div class="w-[45px] h-[45px]  social bg-[#f3f3f3] rounded-xl flex items-center justify-center">
                              {!! __i('shopping-ecommerce', 'email-shopping-bag', 'text-black w-7 h-7') !!}
                           </div>
                           </div>
                           <div>
                               <span class="item-name mb-2">{{ $item->name }}</span>
                               <span class="item-meta">
                                   <span>#{{ $item->id }} {{ $item->schedule ? __('Schedule on') : __('Schedule off') }} {{ $item->schedule ? \Carbon\Carbon::parse($item->schedule_on)->toFormattedDateString() : '' }}</span>
                               </span>
                           </div>
                       </div>
                       <div class="flex-table-cell" data-th="{{ __('Subject') }}">
                           <span>
                              {{ $item->subject }}
                           </span>
                       </div>
                       <div class="flex-table-cell" data-th="{{ __('Status') }}">
                           <span class="my-0">{{ $item->sent()->where('status', 1)->count() .'/'. $item->users()->count() .' '. __('Sent') }}</span>
                       </div>
                       <div class="flex-table-cell" data-th="{{ __('Audience') }}">
                           <span class="my-0">{{ __(':users Audience', ['users' => $item->users()->count()]) }}</span>
                       </div>
                       <div class="flex-table-cell" data-th="{{ __('Created On') }}">
                           <span class="my-0">{{ \Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}</span>
                       </div>
                       <div class="flex-table-cell cell-end" data-th="{{ __('Action') }}">
                           {{-- <a @click="openSingle('{{ $item->id }}')" class="yena-button-stack ml-auto"><span>{{ __('Manage') }}</span></a> --}}
                       </div>
                   </div>
                   @endforeach
               </div>
            </div>
         </div>

         <div x-cloak x-show="page=='folders'">
            <div>
               @if ($folders->isEmpty())
               <div>
                  <div class="flex flex-col justify-center items-start px-0 py-[60px]">
                     {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
                     <p class="mt-3 text-base text-left md:!text-2xl font-bold">
                        {!! __t('You have no audience folder. <br> Create a folder to get started.') !!}
                     </p>
                  </div>
               </div>
               @endif
               <div class="grid grid-cols-2 gap-4">
                  @foreach ($folders as $item)
                  <div class="flex items-center pt-[47px] pb-[47px] [border-bottom:1px_solid_#F9F7F7] bg-white rounded-xl px-10">
                     <div class="w-20 social bg-[#f3f3f3] h-16 rounded-xl flex items-center justify-center">
                        {!! __i('shopping-ecommerce', 'email-shopping-bag', 'text-black w-8 h-8') !!}
                     </div>
                     
                     <div class="w-6/12 ml-2">
                       <h6 class="font-medium text-lg leading-[24px] tracking-[-.014em] text-[#000] cursor-pointer block align-middle">{{ $item->name }}</h6>
   
                       <span class="block text-[#000] text-center font-medium text-[11px] leading-[16px] tracking-[.01em] no-underline px-[10px] py-[4px] rounded-[350px] capitalize bg-[#E7E9EB] align-middle">{{ __(':users Users', ['users' => $item->users()->count()]) }}</span>
                     </div>
                     
                     {{-- <div class="w-2/12 options">
                       <p>Basic Search</p>
                     </div>
                     
                     <div class="w-2/12 options">
                       <p class="nb-selected">{{ __(':users Users', ['users' => $item->users()->count()]) }}</p>
                     </div> --}}
                     
                     <div class="ml-auto">
                        <div class="flex justify-end gap-2 !hidden">
                            <div>
                                <a class="bg-[#f3f3f3] w-9 h-9 flex items-center justify-center rounded-lg" @click="$dispatch('open-modal', 'edit-audience-modal'); $dispatch('registerAudience', {id: '{{ $item->id }}'})">
                                {!! __i('interface-essential', 'pen-edit.5', 'w-4 h-4') !!}
                                </a>
                            </div>
                            <div>
                              <button class="bg-[#f3f3f3] w-9 h-9 flex items-center justify-center rounded-lg" type="button" @click="$event.stopPropagation(); is_delete=!is_delete;">
                                 {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                             </button>
                            </div>
                        </div>
                     </div>
                   </div>
                  @endforeach
               </div>
            </div>
         </div>


       
         <div x-cloak x-show="page=='-'">
            <div>
               <form class="w-full flex items-centers mb-5 gap-4">
                  <div class="search-filter w-full">
                      <input class="-search-input" type="text" name="query" value="{{ request()->get('query') }}" wire:model.live="query.search" placeholder="{{ __('Search') }}">
            
                      <div class="-filter-btn" @click="$dispatch('open-modal', 'filter-modal');">
                          {!! __i('interface-essential', 'settings-filter.3', 'w-5 h-5') !!}
                      </div>
                  </div>
              </form>
               <div class="grid grid-cols-1 md:!grid-cols-2 lg:!grid-cols-3 gap-4 a-card-grid">
         
                  @foreach($audience as $item)
                  @php
                      $set = $this->set($item->id);
                  @endphp
                  <div class="-card rounded-xl -shadow-lg" x-data="{is_delete:false}">
                     <div class="card-button p-3 mb-4 flex gap-2 bg-[var(--yena-colors-gray-100)] w-full rounded-lg !hidden" x-cloak @click="$event.stopPropagation();" :class="{
                        '!hidden': !is_delete
                        }">
                        <button class="btn btn-medium neutral !h-8 !flex !items-center !justify-center !rounded-full !text-black !bg-white" type="button" @click="is_delete=!is_delete">{{ __('Cancel') }}</button>
         
                        <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-8 !flex !items-center !justify-center !rounded-full w-full" @click="$wire.deleteAudience('{{ $item->id }}'); is_delete=false;">{{ __('Yes, Delete') }}</button>
                     </div>
                      <div class="flex items-center justify-center">
                          <div>
                           <div class="rounded-full w-10 h-10 !bg-[#f3f3f3] flex items-center justify-center">
                                 @if (!$set->info('avatar'))
                                    {!! __i('--ie', 'image-picture', 'text-gray-300 w-5 h-5') !!}
                                    @else
                                    <img src="{{ $set->avatar() }}" class="rounded-full w-full h-full object-cover" alt="">
                                 @endif
                           </div>
                          </div>
         
                          <div class="mr-auto ml-4 truncate">
                              <h2 class="flex items-center text-sm font-semibold">
                                  <div class="truncate">{{ $set->info('name') }}</div>
                              </h2>
                              <div class="text-xs text-gray-500">
                                  <div class="truncate">
                                      <span class="flex gap-3">
                                       {{ $set->info('email') }}
                                       </span>
                                    </div>
                              </div>
                          </div>
         
                          <div class="flex justify-end gap-2">
                              <div>
                                  <a class="bg-[#f3f3f3] w-8 h-8 flex items-center justify-center rounded-full" @click="$dispatch('open-modal', 'edit-audience-modal'); $dispatch('registerAudience', {id: '{{ $item->id }}'})">
                                  {!! __i('interface-essential', 'pen-edit.5', 'w-4 h-4') !!}
                                  </a>
                              </div>
                              <button class="bg-[#f3f3f3] w-8 h-8 flex items-center justify-center rounded-full" type="button" @click="$event.stopPropagation(); is_delete=!is_delete;">
                                  {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                              </button>
                          </div>
                      </div>
                  </div>
                  @endforeach
              </div>
   
              {!! $audience->links() !!}
            </div>
         </div>
         
         <template x-teleport="body">
            <div class="[&_.x-modal-body]:m-0 [&_.x-modal-body]:ml-auto [&_.x-modal-body]:mr-0 [&_.x-modal-body]:h-full [&_.fixed]:!p-0 [&_.x-modal-body]:!rounded-none">
               <x-modal name="edit-audience-modal" :show="false" removeoverflow="true" maxWidth="xl" >
                  <livewire:components.console.audience.edit-modal :key="uukey('app', 'console.audience.edit-modal')">
               </x-modal>
            </div>
         </template>
         
         <template x-teleport="body">
            <div class="[&_.x-modal-body]:m-0 [&_.x-modal-body]:ml-auto [&_.x-modal-body]:mr-0 [&_.x-modal-body]:h-full [&_.fixed]:!p-0 [&_.x-modal-body]:!rounded-none">
               <x-modal name="create-broadcast-modal" :show="false" removeoverflow="true" maxWidth="xl" >
                  <livewire:components.console.audience.broadcast.create lazy :key="uukey('app', 'audience.braodcast.create')">
               </x-modal>
            </div>
         </template>
   
         <template x-teleport="body">
            <div class="[&_.x-modal-body]:m-0 [&_.x-modal-body]:ml-auto [&_.x-modal-body]:mr-0 [&_.x-modal-body]:h-full [&_.fixed]:!p-0 [&_.x-modal-body]:!rounded-none">
               <x-modal name="create-folder-modal" :show="false" removeoverflow="true" maxWidth="xl" >
                  <livewire:components.console.audience.folder.create lazy :key="uukey('app', 'audience.folder.create')">
               </x-modal>
            </div>
         </template>
   
         <template x-teleport="body">
            <x-modal name="create-audience-modal" :show="false" removeoverflow="true" maxWidth="xl" >
               <livewire:components.console.audience.create-modal lazy :key="uukey('app', 'audience.create-modal')">
            </x-modal>
         </template>
         
         <template x-teleport="body">
            <x-modal name="filter-modal" :show="false" removeoverflow="true" maxWidth="xl" >
               <livewire:components.console.audience.filter-modal :key="uukey('app', 'crm-page-filter')">
            </x-modal>
         </template>
      </div>

   </div>
   @script
   <script>
       Alpine.data('app_audience', () => {
          return {
            page: '-',
            analyticsLoading: true,
            analytics: {
               contacts: '0',
               country: '0',
            },

            init(){
              let $this = this;
              $this.$wire.getAnalytics().then(r => {
                $this.analytics = r;
                $this.analyticsLoading = false;
              });
            },
          }
       });
   </script>
   @endscript
</div>