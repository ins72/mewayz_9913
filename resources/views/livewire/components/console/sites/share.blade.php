
<?php
   use App\Livewire\Actions\ToastUp;

   use App\Models\FolderSite;
   use App\Models\SiteAccess;
   use App\Models\Site;
   use App\Yena\Teams;
   use App\Models\Folder;
   use function Livewire\Volt\{state, mount, placeholder, updated, uses, on};

   uses([ToastUp::class]);
   mount(function(){
      $this->_get();
   });

   on([
      'registerSite' => function($site_id){
         $this->site = Site::where('id', $site_id)->first();
         $this->site->user = $this->site->user()->first()->append('avatar_json')->toArray();
         $this->_get();
      },
   ]);

   state([
      'team' => [],
      'teamArray' => [],
   ]);

   state([
      'iam' => fn () => collect(iam()),
      'get_original_user' => [],
      'ce' => false,
      '_page' => '-'
   ]);

   // 
   state([
      'folders' => [],
      'people' => [],
      'siteAccess' => [],
   ]);

   state([
      'site' => [],
      'siteArray' => [],
   ]);

   updated([
      'query' => function(){
         $this->getFolders();
      }
   ]);

   $save_site = function(){
      $this->skipRender();
      $this->site->fill($this->siteArray);
      $this->site->save();
   };

   $_get = function(){
      if(!$this->site) return;
      $this->team = Teams::init();
      $this->teamArray = $this->team->toArray();
      // $this->site = Site::where('_slug', 'heyyo-I9LPmT85Nb3ZmGw')->first();
      $this->siteArray = $this->site ? $this->site->toArray() : [];


      $this->getFolders();
      $this->getAccess();

      $this->get_original_user = collect(iam()->get_original_user());
      $this->ce = Teams::permission('ce');
      if(!$this->site->fullAccess()) $this->_page = 'share';
      // $this->queryFolders();
   };

   $getAccess = function(){
      $access = SiteAccess::where('team_id', $this->team->id)->where('site_id', $this->site->id)->get();

      $users = [];

      foreach ($access as $key => $value) {
         $users[] = [
            ...$value->toArray(),
            'user' => $value->user()->first()->append('avatar_json')->toArray(),
         ];
      }
      $this->siteAccess = $users;
   };

   $createAccess = function($items, $permission = ''){
      if(!is_array($items)) return;
      foreach ($items as $item) {
         if(SiteAccess::where('team_id', $this->team->id)->where('site_id', $this->site->id)->where('user_id', ao($item, 'id'))->first()) continue;

         $i = new SiteAccess;
         $i->team_id = $this->team->id;
         $i->site_id = $this->site->id;
         $i->user_id = ao($item, 'id');
         $i->permission = $permission;
         $i->save();
      }

      $this->skipRender();
      $this->getAccess();
   };

   $removeAccess = function($item){
      $this->skipRender();
      SiteAccess::where('team_id', $this->team->id)->where('site_id', $this->site->id)->where('id', ao($item, 'id'))->delete();
   };

   $updatePermission = function($item, $permission = ''){
      if(!$access = SiteAccess::where('team_id', $this->team->id)->where('site_id', $this->site->id)->where('id', ao($item, 'id'))->first()) return;



      $access->permission = $permission;
      $access->save();

      $this->skipRender();
   };

   // Folders
   $queryFolders = function($query = ''){
      $this->skipRender();
      $folders = Folder::where('owner_id', iam()->id)->where('name', 'like', '%'.$query.'%')->get()->append('members_json')->toArray();

      return $folders;
   };

   $getFolders = function(){
      $folderSite = FolderSite::where('site_id', $this->site->id)->get();
      $folders = [];

      foreach ($folderSite as $key => $value) {
         $folders[] = [
            ...$value->toArray(),
            'folder' => $value->folder()->first()->toArray(),
         ];
      }

      $this->folders = $folders;
   };

   $removeFromFolder = function($item){
      $this->skipRender();
      FolderSite::where('site_id', $this->site->id)->where('id', ao($item, 'id'))->delete();
   };
   
   $addToFolders = function($item){
      $this->skipRender();
      
      $folderSite = new FolderSite;
      $folderSite->folder_id = ao($item, 'id');
      $folderSite->site_id = $this->site->id;
      $folderSite->save();

      $this->getFolders();
   };

   $queryTeam = function($query = ''){
      $this->skipRender();

      $teams = [];
      
      $team_users = \App\Models\YenaTeamsUserTable::where('team_id', $this->team->id)->get();
      foreach ($team_users as $item) {
         $teams[] = $item->user()->first()->append('avatar_json')->toArray();
      }

      $teams = collect($teams)->filter(function ($item) use ($query) {
         return false !== stripos($item['email'], $query);
      });
      return collect($teams);
   };


   placeholder('
   <div class="p-4 w-full mt-1">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');
?>


<div class="w-full">
   <div>
      <div x-data="site__share">
         <a wire:ignore x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-1 right-1 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
            <i class="fi fi-rr-cross-small"></i>
         </a>
         <header wire:ignore class="flex pt-4 px-6 flex-initial text-3xl font-black items-center gap-1">{!! __i('Travel', 'Travel, Earth, World, Direction', 'h-7 w-7 mr-1') !!} {{ __('Share') }} <span x-text="site.name"></span></header>
   
         <hr class="yena-divider my-4">
         
   
         <div class="px-6 pb-4 relative">

            <div class="flex items-center flex-row gap-2">
               <div class="mb-[var(--yena-space-4)]">
                  <ul class="flex flex-wrap list-none gap-2 p-0">
                     @if ($site && $site->fullAccess())
                     <li class="flex items-start" wire:ignore>
                        <button class="yena-button-o" type="button" :class="{
                           '!bg-[var(--yena-colors-trueblue-50)] !text-[var(--yena-colors-trueblue-500)]': _page=='-',
                        }" @click="_page='-'">
                           <span class="-icon mr-2">
                              {!! __i('Users', 'single-user-add-plus_1', 'h-4 w-4') !!}
                           </span>
                           {{ __('Collaborate') }}
                        </button>
                     </li>
                     @endif
                     <li class="flex items-start" wire:ignore>
                        <button class="yena-button-o" :class="{
                           '!bg-[var(--yena-colors-trueblue-50)] !text-[var(--yena-colors-trueblue-500)]': _page=='share',
                        }" type="button" @click="_page='share'">
                           <span class="-icon mr-2">
                              {!! __i('Travel', 'Travel, Earth, World, Direction', 'h-4 w-4') !!}
                           </span>
                           {{ __('Share') }}
                        </button>
                     </li>
                     <li class="flex items-start !hidden" wire:ignore>
                        <button class="yena-button-o" :class="{
                           '!bg-[var(--yena-colors-trueblue-50)] !text-[var(--yena-colors-trueblue-500)]': _page=='embed',
                        }" type="button" @click="_page='embed'">
                           <span class="-icon mr-2">
                              {!! __i('interface-essential', 'code-text.1', 'h-4 w-4') !!}
                           </span>
                           {{ __('Embed') }}
                        </button>
                     </li>
                  </ul>
               </div>
            </div>

            @if ($site && $site->fullAccess())
            <div x-show="_page=='-'" wire:ignore>

               <div class="flex items-start gap-2 relative">
                  <div class="w-full">
                     <div class="yena-form-group w-full">
                        <div class="w-full">
                           <div class="--left-element flex-col !h-full">
                              <div class="flex flex-1"></div>
                              <div class="flex h-[var(--yena-sizes-10)] pt-[var(--yena-space-4)] pb-[var(--yena-space-4)] items-center">
                                 {!! __i('interface-essential', 'search.1', 'w-5 h-5') !!}
                              </div>
                           </div>

                           <div class="--wrap">
                              <div class="--wrap-list">
                                 <div class="search-bag flex flex-wrap gap-2" :class="{
                                    '!hidden': selectedTeam.length == 0,
                                 }">
                                    <template x-for="(item, index) in selectedTeam" :key="index">
                                       <div class="--badge">
                                          <div class="-content">
                                             <div class="-avatar">
                                                <img :src="item.avatar_json" alt="">
                                             </div>
                                             <p class="-text" x-text="item.name"></p>
                                          </div>
                                          <div class="-close" @click="removeSelectedTeam(item)">
                                             <i class="ph ph-x"></i>
                                          </div>
                                       </div>
                                    </template>
                                 </div>
                                 <div class="-wrap-input-group">
                                    <input type="text" x-model="people_field" @input.debounce.500ms="queryTeam" placeholder="{{ __('Add emails of your team members') }}">
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div>
                           <div class="--right-element !w-auto !pr-1 !pointer-events-auto" :class="{
                              '!hidden': selectedTeam.length == 0,
                           }">
                           
                              <button class="yena-button-o !text-[var(--yena-colors-trueblue-600)]" type="button" x-tooltip="tippyAddTeam" >
                                 <span x-text="permissionText[selectedPermission].title" class="capitalize"></span>
                                 <span class="--icon ml-2 !mr-0">
                                    <i class="ph ph-caret-down"></i>
                                 </span>
                              </button>
                           </div>
                        </div>
      
                     </div>
                  </div>
            
                  <button class="yena-button-stack w-[300px]" :class="{
                     '!hidden': selectedTeam.length == 0,
                  }" @click="createAccess()" type="button" {{-- :class="{
                     '!hidden': !addPeople,
                     'opacity-40 cursor-not-allowed [box-shadow:var(--yena-shadows-none)] pointer-events-none': !isValidEmail && !inviteError
                  }" --}}>{{ __('Add') }}</button>
               </div>
   
               <div class="absolute z-[2] w-full left-0 px-6" :class="{
                  '!hidden': !people_field && teamQuery.length == 0 || teamQuery.length == 0,
               }">
                  <div class="mt-2">
                     <div class="relative flex-col w-full [box-shadow:var(--yena-shadows-base)] py-2 px-2 max-h-[350px] z-[1500] overflow-y-auto mt-0 flex rounded-md bg-[rgb(255,255,255)] border-none gap-2">
                        <template x-for="(item, index) in teamQuery" :key="index">
                           <div @click="selectTeam(item)" class="cursor-pointer">
                              <div class="flex items-center">
                                 <div class="yena-avatar !w-[2rem] !h-[2rem]">
                                    <img :src="item.avatar_json" class="w-full h-full object-cover rounded-[var(--yena-radii-full)]">
                                 </div>
                                 <div class="ml-[var(--yena-space-3)]">
                                    <p x-text="item.name"></p>
                                    <div class="flex items-center">
                                       <p class="text-xs text-[var(--yena-colors-gray-500)]" x-text="item.email"></p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </template>
                     </div>
                  </div>
               </div>
   
               <div class="flex items-stretch flex-col gap-[var(--yena-space-1)] pt-[var(--yena-space-4)] pb-[var(--yena-space-4)]">
                  <div class="flex items-center justify-between flex-row gap-2 w-full pt-[var(--yena-space-2)]">
                     <div>
                        <div class="flex items-center">
                           <div class="yena-avatar !w-[2rem] !h-[2rem] !flex !items-center !justify-center !bg-gray-200" :class="{
                              '!opacity-50': site.workspace_permission == 'no_access'
                           }">
                              {!! __i('Building, Construction', 'hotel-modern-building', 'w-5 h-5') !!}
                              <span x-show="site.workspace_permission == 'no_access'">
                                 <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="slash" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="bottom-[0] left-[0] m-auto absolute right-[0] top-[0] z-[var(--fa-stack-z-index, auto)] w-5 h-5"><path fill="currentColor" d="M5.1 9.2C13.3-1.2 28.4-3.1 38.8 5.1l592 464c10.4 8.2 12.3 23.3 4.1 33.7s-23.3 12.3-33.7 4.1L9.2 42.9C-1.2 34.7-3.1 19.6 5.1 9.2z"></path></svg>
                              </span>
                           </div>
                           <div class="ml-[var(--yena-space-3)]">
                              <p>{{ __('Workspace members') }}</p>
                              <div class="flex items-center">
                                 <p class="text-xs text-[var(--yena-colors-gray-500)]">{{ __('Everyone in ') }} <span x-text="team.name"></span></p>
                              </div>
                           </div>
                        </div>
                     </div>
                     <button class="yena-button-o !text-[var(--yena-colors-trueblue-600)]" type="button" x-tooltip="tippyWorkspace" >
                        <span x-text="site.workspace_permission ? permissionText[site.workspace_permission].title : permissionText.no_access.title" class="capitalize"></span>
                        <span class="--icon ml-2 !mr-0">
                           <i class="ph ph-caret-down"></i>
                        </span>
                     </button>
   
                  </div>
               </div>
               <div class="flex flex-col gap-0">
                  <template x-for="(item, index) in folders" :key="index">
                     <div class="flex items-stretch flex-col gap-[var(--yena-space-1)]">
                        <div class="flex items-center justify-between flex-row gap-2 w-full pt-[var(--yena-space-2)] pb-[var(--yena-space-2)]">
                           <div>
                              <div class="flex items-center">
                                 <div class="yena-avatar !w-[2rem] !h-[2rem] !flex !items-center !justify-center !bg-gray-200" :class="{
                                    '!opacity-50': site.workspace_permission == 'no_access'
                                 }">
                                    {!! __i('Folders', 'folder-open', 'w-5 h-5') !!}
                                    <span x-show="site.workspace_permission == 'no_access'">
                                       <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="slash" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="bottom-[0] left-[0] m-auto absolute right-[0] top-[0] z-[var(--fa-stack-z-index, auto)] w-5 h-5"><path fill="currentColor" d="M5.1 9.2C13.3-1.2 28.4-3.1 38.8 5.1l592 464c10.4 8.2 12.3 23.3 4.1 33.7s-23.3 12.3-33.7 4.1L9.2 42.9C-1.2 34.7-3.1 19.6 5.1 9.2z"></path></svg>
                                    </span>
                                 </div>
                                 <div class="ml-[var(--yena-space-3)]" :class="{
                                    '!opacity-50': site.workspace_permission == 'no_access'
                                 }">
                                    <p x-text="item.folder.name"></p>
                                 </div>
                              </div>
                           </div>
                           <button type="button" @click="removeFromFolder(item)" class="yena-button-o !text-[var(--yena-colors-red-600)]">{{ __('Remove') }}</button>
                        </div>
                     </div>
                  </template>
               </div>

               <template x-if="site.workspace_permission == 'no_access'">
                  <div class="w-full flex items-center relative overflow-hidden bg-[#feebc8] px-4 pt-[var(--yena-space-3)] pb-[var(--yena-space-3)] mt-[var(--yena-space-2)] text-[var(--yena-fontSizes-sm)] rounded-[4px]">
                     <span class="text-[#c05621] flex-shrink-0 w-[var(--yena-sizes-5)] h-[var(--yena-sizes-6)] self-start mr-3 flex items-center justify-center">
                        <i class="fi fi-rr-triangle-warning"></i>
                     </span>
                     
                     <div class="flex flex-col gap-2">
                        <p class="yena-text">{{ __('Your site will be hidden from these folders until you provide workspace access') }} </p>
                     </div>
                  </div>
               </template>
   
               <template x-if="site.workspace_permission !== 'no_access'">
               <div class="flex items-start gap-4 relative pt-2">
                  <div class="w-full">
                     <div class="yena-form-group">
                        <div class="--left-element">
                           {!! __i('interface-essential', 'search.1', 'w-5 h-5') !!}
                        </div>
      
                        <input type="text" x-model="folders_field" @input.debounce.500ms="queryFolders" placeholder="{{ __('Add to folder') }}">
                     </div>
                  </div>
               </div>
               </template>
   
               <div class="absolute z-[2] w-full left-0 px-6" :class="{
                  '!hidden': !folders_field && foldersQuery.length == 0 || foldersQuery.length == 0,
               }">
                  <div class="mt-2">
                     <div class="relative flex-col w-full [box-shadow:var(--yena-shadows-base)] py-2 px-2 max-h-[350px] z-[1500] overflow-y-auto mt-0 flex rounded-md bg-[rgb(255,255,255)] border-none">
                        <template x-for="(item, index) in foldersQuery" :key="index">
                           <div class="flex items-center flex-row pt-[var(--yena-space-2)] pb-[var(--yena-space-2)] rounded-[var(--yena-radii-md)] [transition-property:var(--yena-transition-property-common)] hover:bg-[var(--yena-colors-gray-100)] px-4 cursor-pointer" @click="addToFolder(item)">
                              <div class="p-2">
                                 {!! __i('Folders', 'folder-open', 'w-5 h-5') !!}
                              </div>
            
                              
                              <div class="flex items-center flex-row flex-1 ml-4">
                                 <div class="flex flex-col">
                                    <p x-text="item.name"></p>
                                    <p class="text-sm text-[color:var(--yena-colors-gray-400)]" x-text="folderItemText(item)"></p>
                                 </div>
            
                                 <div class="flex-[1] justify-self-stretch self-stretch ml-2"></div>
            
                                 <div class="flex items-center justify-end flex-row-reverse">
                                    <div class="relative">
                                       <template x-for="(member, index) in item.members_json" :key="member.id">
                                          <div class="yena-avatar !w-6 !h-6 [box-shadow:var(--yena-shadows-md)] border-2 border-white !-mr-3">
                                             <img :src="member.user_json.avatar_json" alt=" " class="w-full h-full object-cover">
                                          </div>
                                       </template>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </template>
                     </div>
                  </div>
               </div>
   

               <div class="flex items-stretch flex-col gap-[var(--yena-space-1)] pt-[var(--yena-space-4)] pb-[var(--yena-space-4)]" :class="{
                  '!hidden': !site.id
               }">
                  <div class="flex items-center justify-between flex-row gap-2 w-full pt-[var(--yena-space-2)] pb-[var(--yena-space-2)]">
                     <div>
                        <div class="flex items-center">
                           <div class="yena-avatar !w-[2rem] !h-[2rem]">
                              <img :src="site.user ? site.user.avatar_json : ''" alt="Jeff Jola" class="w-full h-full object-cover rounded-[var(--yena-radii-full)]">
                           </div>
                           <div class="ml-[var(--yena-space-3)]">
                              <p x-html="site.user ? site.user.name + (get_original_user.id == site.user.id ? '({{ __('You') }})' : '') : ''"></p>
                              <div class="flex items-center">
                                 <p class="text-xs text-[var(--yena-colors-gray-500)]" x-text="site.user ? site.user.email : ''"></p>
                              </div>
                           </div>
                        </div>
                     </div>
                     <button class="yena-button-o !opacity-50" type="button" disabled >
                        <span class="capitalize">{{ __('Full access') }}</span>
                        <span class="--icon ml-2 !mr-0">
                           <i class="ph ph-caret-down"></i>
                        </span>
                     </button>
                  </div>
                  <template x-for="(item, index) in siteAccess" :key="index">
                     <div class="flex items-center justify-between flex-row gap-2 w-full pt-[var(--yena-space-2)] pb-[var(--yena-space-2)]">
                        <div>
                           <div class="flex items-center">
                              <div class="yena-avatar !w-[2rem] !h-[2rem]">
                                 <img :src="item.user.avatar_json" alt="Jeff Jola" class="w-full h-full object-cover rounded-[var(--yena-radii-full)]">
                              </div>
                              <div class="ml-[var(--yena-space-3)]">
                                 <p x-text="item.user.name"></p>
                                 <div class="flex items-center">
                                    <p class="text-xs text-[var(--yena-colors-gray-500)]" x-text="item.user.email"></p>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <button class="yena-button-o !text-[var(--yena-colors-trueblue-600)]" type="button" x-tooltip="tippyEditTeam" >
                           <span x-text="permissionText[item.permission].title" class="capitalize"></span>
                           <span class="--icon ml-2 !mr-0">
                              <i class="ph ph-caret-down"></i>
                           </span>
                        </button>
                     </div>
                  </template>
               </div>
            </div>
            @endif
            <div x-show="_page=='share'">
               @if ($site && $site->fullAccess())
               <div class="flex items-stretch flex-col gap-[var(--yena-space-1)] pt-[var(--yena-space-4)] pb-[var(--yena-space-4)]">
                  <div class="flex items-center justify-between flex-row gap-2 w-[100%] pt-[var(--yena-space-2)] pb-[var(--yena-space-2)]">
                     <div>
                        <div class="flex items-center">
                           <div class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-[var(--yena-sizes-8)] h-[var(--yena-sizes-8)] rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100">
                              {!! __i('Maps, Navigation', 'Earth, Home, World.3', 'w-5 h-5') !!}
                           </div>
                           <div class="ml-3">
                              <p>{{ __('Public Access') }}</p>
                              <div class="flex items-center">
                                 <div class="flex items-center flex-row gap-1">
                                    <p class="text-[#8f8b8b] text-xs">{{ __('Anyone with a link can view') }}</p>
                                    <div class="text-[#8f8b8b] leading-none">
                                       {!! __i('--ie', 'warning', 'w-2 h-2') !!}
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div>
                        <button class="yena-button-o" type="button" x-tooltip="tippyShareLink" >
                           <span x-text="site.published ? permissionText.view.title : permissionText.no_access.title" class="capitalize"></span>
                           <span class="--icon ml-2 !mr-0">
                               <i class="ph ph-caret-down"></i>
                           </span>
                       </button>
                     </div>
                  </div>

               </div>
               @endif

               <div wire:ignore>
                  <div class="relative flex w-[100%] isolate  gap-[var(--yena-space-2)] [grid-template-areas:"paste_generate_import"] grid-cols-[1fr_1fr_1fr]">
                     <input type="text" class="w-[100%] h-[3rem] text-[1rem] pl-4 pr-10 rounded-md min-w-0 outline-[transparent_solid_2px] outline-offset-[2px] relative appearance-none [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 border-2 border-solid [border-image:initial] border-transparent bg-[#f7f3f2]" placeholder="{{ __('link goes here...') }}" readonly :value="$store.builder.generateSiteLink(site)">
   
                     <div class="right-0 h-[3rem] text-[1rem] flex items-center justify-center absolute top-0 z-[1]">
                        <button type="button" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[2rem] [2.5rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] m-2" @click="$clipboard($store.builder.generateSiteLink(site)); $el.innerText = window.builderObject.copiedText;">{{ __('Copy Link') }}</button>
                     </div>
                  </div>
   
                  <ul class="[list-style:none] flex justify-center w-[100%] px-[var(--s-1)] py-[0] gap-[20px] mt-4">
                     <li class="flex flex-col items-center text-[var(--t-s)] text-[var(--c-mix-3)]">
                        <a :href="'https://www.facebook.com/sharer/sharer.php?u='+$store.builder.generateSiteLink(site)" target="_blank" class="w-[calc(var(--unit)_*_6)] h-[calc(var(--unit)_*_6)] flex justify-center items-center border-[1px] border-solid border-[var(--c-mix-1)] mb-[8px] rounded-full">
                           <i class="ph ph-facebook-logo text-lg"></i>
                        </a>
                     </li>
                     <li>
                        <a :href="'https://twitter.com/intent/tweet?url='+$store.builder.generateSiteLink(site)+'/&text='" class="w-[calc(var(--unit)_*_6)] h-[calc(var(--unit)_*_6)] flex justify-center items-center border-[1px] border-solid border-[var(--c-mix-1)] mb-[8px] rounded-full">
                           <i class="ph ph-x-logo text-lg"></i>
                        </a>
                     </li>
                     <li>
                        <a :href="'https://api.whatsapp.com/send?text='+$store.builder.generateSiteLink(site)" class="w-[calc(var(--unit)_*_6)] h-[calc(var(--unit)_*_6)] flex justify-center items-center border-[1px] border-solid border-[var(--c-mix-1)] mb-[8px] rounded-full">
                           <i class="ph ph-whatsapp-logo text-lg"></i>
                        </a>
                     </li>
                     <li>
                        <a :href="'https://www.linkedin.com/shareArticle?mini=true&url='+$store.builder.generateSiteLink(site)" class="w-[calc(var(--unit)_*_6)] h-[calc(var(--unit)_*_6)] flex justify-center items-center border-[1px] border-solid border-[var(--c-mix-1)] mb-[8px] rounded-full">
                           <i class="ph ph-linkedin-logo text-lg"></i>
                        </a>
                     </li>
                  </ul>
   
                  <a type="button" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[3rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] m-2" :href="$store.builder.generateSiteLink(site)" target="_blank">{{ __('View site') }}</a>
               </div>
            </div>
            <div x-show="_page=='embed'"></div>


            <div wire:ignore>
               <template x-ref="tippy_workspace">
                  <div class="yena-menu-list !w-[380px] !max-w-full">
                     <p class="mx-4 mt-[var(--yena-space-2)] mb-[var(--yena-space-2)] font-semibold text-[var(--yena-fontSizes-sm)] font-[var(--yena-fonts-heading)] normal-case text-[var(--yena-colors-gray-500)] tracking-[0px] ml-[var(--yena-space-2)]">{{ __('Permissions') }}</p>

                     <template x-for="(item, index) in permissionText" :key="index">
                        <a @click="site.workspace_permission = index; $dispatch('hideTippy');" :class="{
                            '!bg-[var(--yena-colors-gray-100)] !border-[var(--yena-colors-gray-200)]': site.workspace_permission == index,
                        }" class="yena-menu-list-item !items-start">
                           <div class="--icon !mt-1" :class="{
                              'opacity-0': site.workspace_permission != index
                           }">
                              <i class="ph ph-check"></i>
                           </div>
                           <div>
                              <div class="flex flex-col">
                                 <span class="" x-text="item.title"></span>
                                 <span class="text-sm text-[var(--yena-colors-gray-500)] mt-1" x-text="item.text"></span>
                              </div>
                           </div>
                        </a>
                     </template>
                 </div>
              </template>
              <template x-ref="tippy_edit_team">
                 <div class="yena-menu-list !w-[380px] !max-w-full">
                    <p class="mx-4 mt-[var(--yena-space-2)] mb-[var(--yena-space-2)] font-semibold text-[var(--yena-fontSizes-sm)] font-[var(--yena-fonts-heading)] normal-case text-[var(--yena-colors-gray-500)] tracking-[0px] ml-[var(--yena-space-2)]">{{ __('Permissions') }}</p>

                    <template x-for="(_item, index) in permissionText" :key="index">
                       <a @click="updatePermission(item, index); $dispatch('hideTippy');" :class="{
                           '!bg-[var(--yena-colors-gray-100)] !border-[var(--yena-colors-gray-200)]': item.permission == index,
                       }" class="yena-menu-list-item !items-start">
                          <div class="--icon !mt-1" :class="{
                             'opacity-0': item.permission != index
                          }">
                             <i class="ph ph-check"></i>
                          </div>
                          <div>
                             <div class="flex flex-col">
                                <span class="" x-text="_item.title"></span>
                                <span class="text-sm text-[var(--yena-colors-gray-500)] mt-1" x-text="_item.text"></span>
                             </div>
                          </div>
                       </a>
                    </template>
                    <hr class="--divider">
                    <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="removeAccess(item)">
                       <div class="--icon">
                          {!! __icon('interface-essential', 'trash-bin-delete', 'w-5 h-5') !!}
                       </div>
                       <span>{{ __('Remove') }}</span>
                    </a>
                </div>
             </template>
              <template x-ref="tippy_add_team">
                 <div class="yena-menu-list !w-[380px] !max-w-full">
                    <p class="mx-4 mt-[var(--yena-space-2)] mb-[var(--yena-space-2)] font-semibold text-[var(--yena-fontSizes-sm)] font-[var(--yena-fonts-heading)] normal-case text-[var(--yena-colors-gray-500)] tracking-[0px] ml-[var(--yena-space-2)]">{{ __('Permissions') }}</p>

                    <template x-for="(item, index) in permissionText" :key="index">
                       <a @click="selectedPermission = index; $dispatch('hideTippy');" :class="{
                           '!bg-[var(--yena-colors-gray-100)] !border-[var(--yena-colors-gray-200)]': selectedPermission == index,
                       }" class="yena-menu-list-item !items-start">
                          <div class="--icon !mt-1" :class="{
                             'opacity-0': selectedPermission != index
                          }">
                             <i class="ph ph-check"></i>
                          </div>
                          <div>
                             <div class="flex flex-col">
                                <span class="" x-text="item.title"></span>
                                <span class="text-sm text-[var(--yena-colors-gray-500)] mt-1" x-text="item.text"></span>
                             </div>
                          </div>
                       </a>
                    </template>
                </div>
             </template>
               <template x-ref="tippy_share_link">
                  <div class="yena-menu-list !w-[380px] !max-w-full">
                     <p class="mx-4 mt-2 mb-2 font-semibold text-sm font-[var(--yena-fonts-heading)] normal-case text-[var(--yena-colors-gray-500)] tracking-[0px] ml-2">{{ __('Permissions') }}</p>
                     <a @click="site.published=true; $dispatch('hideTippy');" :class="{
                         '!bg-[var(--yena-colors-gray-100)] !border-[var(--yena-colors-gray-200)]': site.published,
                     }" class="yena-menu-list-item !items-start">
                        <div class="--icon !mt-1" :class="{
                           'opacity-0': !site.published
                        }">
                           <i class="ph ph-check"></i>
                        </div>
                        <div>
                           <div class="flex flex-col">
                              <span class="" x-text="permissionText.view.title"></span>
                              <span class="text-sm text-[var(--yena-colors-gray-500)] mt-1" x-text="permissionText.view.text"></span>
                           </div>
                        </div>
                     </a>
                     <a @click="site.published=false; $dispatch('hideTippy');" :class="{
                         '!bg-[var(--yena-colors-gray-100)] !border-[var(--yena-colors-gray-200)]': !site.published,
                     }" class="yena-menu-list-item !items-start">
                        <div class="--icon !mt-1" :class="{
                           'opacity-0': site.published
                        }">
                           <i class="ph ph-check"></i>
                        </div>
                        <div>
                           <div class="flex flex-col">
                              <span class="" x-text="permissionText.no_access.title"></span>
                              <span class="text-sm text-[var(--yena-colors-gray-500)] mt-1" x-text="permissionText.no_access.text"></span>
                           </div>
                        </div>
                     </a>
                 </div>
              </template>
            </div>
         </div>
      </div>
   </div>

   
   @script
   <script>
       Alpine.data('site__share', () => {
          return {
            site: @entangle('siteArray').live,
            team: @entangle('teamArray').live,
            iam: @entangle('iam').live,
            get_original_user: @entangle('get_original_user'),
            folders: @entangle('folders').live,
            siteAccess: @entangle('siteAccess').live,
            foldersQuery: [],

            membersItemText: {
               text: '{{ __('members') }}',
               including: '{{ __('members, including you') }}',
            },
            permissionText: {
               full_access: {
                  title: '{{ __('Full Access') }}',
                  text: '{{ __('Can view, edit, and share with others.') }}',
               },
               edit: {
                  title: '{{ __('Edit') }}',
                  text: '{{ __('Can view, edit, but not share with others.') }}',
               },
               view: {
                  title: '{{ __('View') }}',
                  text: '{{ __('Can view, but not edit.') }}',
               },
               no_access: {
                  title: '{{ __('No access') }}',
                  text: '{{ __('Cannot view or edit.') }}',
               },
            },

            _page: @entangle('_page'),
            tippy: {
                allowHTML: true,
                maxWidth: 360,
                interactive: true,
                trigger: 'click',
                animation: 'scale',
            },
            tippyShareLink: {},
            tippyAddTeam: {},
            tippyWorkspace: {},
            tippyEditTeam: {},

            folders_field: null,


            people_field: null,
            teamQuery: [],
            selectedTeam: [],
            selectedPermission: 'full_access',
            queryTeam(){
               let $this = this;

               if(!this.people_field) return;

               $this.$wire.queryTeam(this.people_field).then(r => {
                  let data = r;

                  $this.siteAccess.forEach(e => {
                     data.forEach((user, index) => {
                        if(user.id === e.user_id){
                           data.splice(index, 1);
                        }
                     });
                  });

                  $this.teamQuery = data;
               });
            },
            removeSelectedTeam(item){
               let $this = this;
               this.selectedTeam.forEach((e, index) => {
                  if(e.id == item.id) {
                     $this.selectedTeam.splice(index, 1);
                  }
               });
            },

            selectTeam(item){
               this.people_field = null;
               this.teamQuery = [];
               this.selectedTeam.push(item);
            },

            updatePermission(item, permission){
               let $this = this;
               this.siteAccess.forEach((e, index) => {
                  if(e.id == item.id){
                     e.permission = permission;
                  }
               });

               
               $this.$wire.updatePermission(item, permission);
            },

            removeAccess(item){
               let $this = this;
               this.siteAccess.forEach((e, index) => {
                  if(e.id == item.id){
                     $this.siteAccess.splice(index, 1);
                  }
               });


               $this.$wire.removeAccess(item);
               $this.queryTeam();
            },

            createAccess(){
               let $this = this;

               if($this.selectedTeam.length == 0) return;
               
               $this.$wire.createAccess(this.selectedTeam, this.selectedPermission).then(r => {
                  $this.selectedTeam = [];
                  $this.people_field = null;
               });
            },

            queryFolders(){
               let $this = this;
               $this.foldersQuery = [];
               if(this.folders_field && this.folders_field.length < 2) {
                  return;
               }

               $this.$wire.queryFolders(this.folders_field).then(r => {
                  let data = r;

                  data.forEach((folder, index) => {

                     $this.folders.forEach(e => {
                        if(e.folder_id == folder.id){
                           data.splice(index, 1);
                        }
                     });
                  });

                  $this.foldersQuery = data;
               });
            },

            folderIsMember(item){
               let $this = this;
               let response = false;
               item.members_json.forEach(e => {
                  if($this.iam.id == e.user_id){
                     response = true;
                  }
               });

               return response;
            },

            folderItemText(item){
               let $this = this;
               let $members = item.members_json.length;
               let text = $this.membersItemText.text;
               if(this.folderIsMember(item)){
                  text = $this.membersItemText.including;
               }

               text = $members +' '+ text; 

               return text;
            },

            addToFolder(item){
               let $this = this;
               let canCreate = true;
               
               this.folders.forEach(e => {
                  if(e.folder_id == item.id){
                     canCreate = false;
                  }
               });

               if(!canCreate) return;

               $this.folders_field = null;
               $this.foldersQuery = [];
               $this.$wire.addToFolders(item);
            },

            removeFromFolder(item){
               let $this = this;
               this.folders.forEach((e, index) => {
                  if(e.id == item.id){
                     $this.folders.splice(index, 1);
                  }
               });

               $this.$wire.removeFromFolder(item).then(r => {
                  $this.queryFolders();
               });
            },

            init(){
               var $this = this;
               // this.tippy.appendTo = this.$root;

               this.tippyShareLink = {
                   ...this.tippy,
                   content: this.$refs.tippy_share_link.innerHTML,
               }
               this.tippyAddTeam = {
                   ...this.tippy,
                   content: this.$refs.tippy_add_team.innerHTML,
               }
               this.tippyWorkspace = {
                   ...this.tippy,
                   content: this.$refs.tippy_workspace.innerHTML,
               }
               this.tippyEditTeam = {
                   ...this.tippy,
                   content: this.$refs.tippy_edit_team.innerHTML,
               }

               this.$watch('site', (site) => {
                  $this.$wire.save_site();
               });


               // console.log(window.moment().format("MMM d, Y HH:mm A"))
            }
          }
       });
   </script>
   @endscript
</div>