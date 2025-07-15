
<?php

   use App\Models\Site;
   use App\Models\Page;
   use App\Models\Folder;
   use App\Models\FolderSite;
   use App\Models\YenaFavorite;
   use function Livewire\Volt\{state, mount, placeholder, on};

   mount(function(){
      $this->getSites();


      // Check folder

      if($this->isFolder){
         //

         $error = false;

         if($this->folder->owner_id !== iam()->id){
            $error = true;
         }

         if(!$this->folder->isMember(iam()->get_original_user()->id)){
            $error = true;
         }

         if($error){
            $this->sites = [];
            $route = route('dashboard-index');
            $this->js(
               '
                  window.runToast("success", "'. __('You dont belong in this folder') .'")
                  setTimeout(function() {
                        Livewire.navigate("'.$route.'");
                  }, 2000);
               '
            );
         }
      }
   });

   placeholder('placeholders.console.sites.page-placeholder');

   state([
      'folder' => [],
      'slug',
      'isFolder',
   ]);

   state([
      'sites' => [],
      'allSites' => [],
      'favoriteSites' => [],
      'createdBySites' => [],
   ]);

   on(['refreshSites' => function(){
      $this->getSites();
   },
   'duplicateSite' => function($id){
      $site = Site::where('user_id', iam()->id)->first();
      
      $_c = Site::where('user_id', iam()->id)->count();
      if(__o_feature('consume.sites', iam()) != -1 && $_c >= __o_feature('consume.sites', iam())){
         $this->js('window.runToast("error", "'. __('You have reached your site creation limit. Please upgrade your plan.') .'");');
         return;
      }

      $site = $site->duplicateSite();

      $this->getSites();
      

      $route = route('dashboard-builder-index', ['slug' => $site->_slug]);

      $this->js(
          '
              window.runToast("success", "'. __('Site created successfully') .'")
              setTimeout(function() {
                  Livewire.navigate("'.$route.'");
              }, 2000);
          '
      );
   },
   'setFavourite' => function($id){
      if($fav = YenaFavorite::where('owner_id', iam()->id)->where('user_id', iam()->get_original_user()->id)->where('site_id', $id)->first()){
         $fav->delete();
         $this->dispatch('hideTippy');
         $this->getSites();
         return false;
      }

      $favorite = new YenaFavorite;
      $favorite->owner_id = iam()->id;
      $favorite->user_id = iam()->get_original_user()->id;
      $favorite->site_id = $id;
      $favorite->save();

      $this->dispatch('hideTippy');
      $this->getSites();
   }]);

   $isFavorite = function($id){
      return YenaFavorite::where('owner_id', iam()->id)->where('user_id', iam()->get_original_user()->id)->where('site_id', $id)->first();
   };

   $filterSites = function(){
      $this->sites = [];
      $this->favoriteSites = [];
      $this->createdBySites = [];
      foreach ($this->allSites as $item) {
         if($item->updated_at >= now()->subDays(2)){
            $this->sites[] = $item;
         }

         // Check favourite
         // if($this->isFavorite($item->id)){
         //    $this->favoriteSites[] = $item;
         // }

         // Check created by
         if(iam()->get_original_user()->id == $item->created_by){
            $this->createdBySites[] = $item;
         }
      }

   };
   
   $getSites = function() {
      if(!$this->isFolder){
         $this->allSites = iam()->getSites();

         $this->filterSites();
         return;
      }

      if(!$this->folder = Folder::where('slug', $this->slug)->first()) return;


      foreach ($this->folder->folderSites as $item) {
         $site = $item->site;
         if(!$site) continue;
         if($site->trashed()) continue;
         if(!$site->canAccess()) continue;

         $this->allSites[] = $site;
         $this->filterSites();
      }
   };

   $createBlank = function(){
      // Check if plan allows it.

      if($this->isFolder){
         if(!$this->folder->isMember(iam()->id)) return;
      }
      // Create a blank site
      $_c = Site::where('user_id', iam()->id)->count();
      if(__o_feature('consume.sites', iam()) != -1 && $_c >= __o_feature('consume.sites', iam())){
         $this->js('window.runToast("error", "'. __('You have reached your site creation limit. Please upgrade your plan.') .'");');
         return;
      }

      // Check if has folder
      if($this->isFolder){
         $folderSite = new FolderSite;
         $folderSite->folder_id = $this->folder->id;
         $folderSite->site_id = $new->id;
         $folderSite->save();
      }

      $route = route('dashboard-builder-index', ['slug' => $_slug]);

      $this->js(
          '
              window.runToast("success", "'. __('Site created successfully') .'")
              setTimeout(function() {
                  Livewire.navigate("'.$route.'");
              }, 2000);
          '
      );

      $this->getSites();

      return true;

      return $this->redirect($route, navigate: true);
   };
?>

<div>
   
   <template xx-teleport=".header-center">
      <div class="mt-1">
         <div class="!relative">
            <div class="absolute left-[21px] top-[-3px]"></div>
            <div class="flex items-center justify-between rounded-8 w-full">
               <div class="flex items-center cursor-pointer items-center group">
                  <div role="button">
                     {!! __i('interface-essential', 'browser-internet-web-network-window-app-icon', 'w-6 h-6 border-2 rounded-xl text-[rgb(40,_72,_240)] p-1 bg-[rgb(216,_222,_255)] ') !!}
                  </div>
                  <div class="ml-3">
                     <div class="flex flex-col items-center justify-between whitespace-nowrap">
                        <div class="font-bold whitespace-nowrap">{{ __('Sites') }}</div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </template>

   <div class="mb-6 " x-data="sites__page">

      @if (!empty($allSites))
      <div class="flex flex-col mb-4">
         
         <div class="flex items-center h-6">
            <h2 class="text-lg font-semibold ">
               @if ($isFolder)
                  {!! __icon('Folders', 'folder-open', 'w-6 h-6 inline-block') !!}

                  @else
                  {!! __icon('interface-essential', 'browser-internet-web-network-window-app-icon', 'w-6 h-6 inline-block') !!}
               @endif

               <span class="ml-2">{{ $isFolder ? $folder->name : __('All Sites') }}</span>
            </h2>
         </div>
         @if ($isFolder)
         <div class="flex items-center gap-6 mt-3">
            <div class="flex items-center justify-end flex-row-reverse">
               <div class="relative">
                  @foreach ($folder->members()->orderBy('id', 'DESC')->get() as $member)
                  <div class="yena-avatar !w-6 !h-6 [box-shadow:var(--yena-shadows-md)] border-2 border-white !-mr-3">
                     <img src="{{ $member->user->getAvatar() }}" alt="{{ $member->user->name }}" class="w-full h-full object-cover">
                  </div>
                  @endforeach
               </div>
            </div>

            @php
               $membersText = __(":members member(s)", [
                  'members' => $folder->members()->count(),
               ]);
            @endphp
            <p class="text-sm text-[color:var(--yena-colors-gray-400)]">{{ $membersText }}</p>
         </div>
         @endif
         <div class="flex flex-col gap-4 mt-4 lg:flex-row">
            
            <a href="{{ route('dashboard-create-index') }}" @navigate class="yena-button-stack --black">
               <div class="--icon">
                  {!! __icon('interface-essential', 'plus-add.3', 'w-6 h-6') !!}
               </div>

               {{ __('Create new') }}

               <div class="inline-flex self-center ml-2 shrink-0">
                  <div class="--badge">{{ __('AI') }}</div>
               </div>
            </a>
            
            @if ($isFolder)
            <a @click="$dispatch('open-modal', 'create-site-modal')" class="cursor-pointer yena-button-stack">
               <div class="--icon">
                  {!! __icon('interface-essential', 'plus-add.3', 'w-6 h-6') !!}
               </div>

               {{ __('New from blank') }}
            </a>
            @endif
         </div>
      </div>

      <div class="flex items-center">
         <div class="overflow-hidden">
            <ul class="yena-wrap-list">
               <li class="yena-wrap-list-item">
                  <a class="yena-button-o cursor-pointer" @click="_page='all'" :class="{
                     '-active': _page == 'all'
                  }">
                     <span class="--icon">
                        {!! __icon('interface-essential', 'media-library-folder.1', 'w-5 h-5') !!}
                     </span>
                     {{ __('All') }}
                  </a>
               </li>
               <li class="yena-wrap-list-item !hidden">
                  <a class="yena-button-o cursor-pointer" @click="_page='recent'" :class="{
                     '-active': _page == 'recent'
                  }">
                     <span class="--icon">
                        {!! __icon('interface-essential', 'alarm-clock-time-timer', 'w-5 h-5') !!}
                     </span>
   
                     {{ __('Recently edited') }}
                  </a>
               </li>
               <li class="yena-wrap-list-item">
                  <a class="yena-button-o cursor-pointer" @click="_page='created_by_you'" :class="{
                     '-active': _page == 'created_by_you'
                  }">
                     <span class="--icon">
                        {!! __icon('interface-essential', 'user-circle', 'w-5 h-5') !!}
                     </span>
   
                     {{ __('Created by you') }}
                  </a>
               </li>
               {{-- <li class="yena-wrap-list-item">
                  <a class="yena-button-o cursor-pointer" @click="_page='favorites'" :class="{
                     '-active': _page == 'favorites'
                  }">
                     <span class="--icon">
                        {!! __icon('interface-essential', 'star-favorite', 'w-5 h-5') !!}
                     </span>
   
                     {{ __('Favorites') }}
                  </a>
               </li> --}}
            </ul>
         </div>
      </div>
      @endif

      @if (empty($allSites))
         <x-empty-state :title="__('Websites that don\'t stress you out, in seconds
         ')" :desc="__('It\'s a little quiet in here. Create, craft, and publish your first website in minutes.')">
         
         <div class="flex flex-row gap-4 mt-4 lg:flex-row">
            
            <a href="{{ route('dashboard-create-index') }}" @navigate class="yena-button-stack --primary">
               <div class="--icon">
                  {!! __icon('interface-essential', 'plus-add.3', 'w-6 h-6') !!}
               </div>

               {{ __('Create new') }}

               <div class="inline-flex self-center ml-2 shrink-0">
                  <div class="--badge">{{ __('AI') }}</div>
               </div>
            </a>
            
            <a href="{{ route('dashboard-templates-index') }}" @navigate class="cursor-pointer yena-button-stack">
               <div class="--icon">
                  {!! __icon('interface-essential', 'thunder-lightning-notifications', 'w-6 h-6') !!}
               </div>

               {{ __('Template') }}
            </a>
         </div>
      </x-empty-state>
      @endif
      <div class="mt-4">
         <div x-show="_page=='all'">
            <div class="grid grid-cols-1 gap-4 md:!grid-cols-2 lg:!grid-cols-3">
               @foreach ($allSites as $item)
                  <livewire:components.console.sites.items :$item :key="uukey('sites', 'allSitescomponent-' . $item->id)"/>
               @endforeach
            </div>
         </div>
         {{-- <div x-show="_page=='recent'">
            <div class="grid grid-cols-1 gap-4 md:!grid-cols-2 lg:!grid-cols-3 xl:!grid-cols-3">
               @foreach ($sites as $item)
               <livewire:components.console.sites.items :$item :key="uukey('sites', 'sitescomponent-' . $item->id)"/>
               @endforeach
            </div>
         </div> --}}
         <div x-show="_page=='created_by_you'">
            <div class="grid grid-cols-1 gap-4 md:!grid-cols-2 lg:!grid-cols-3">
               @foreach ($createdBySites as $item)
               <livewire:components.console.sites.items :$item :key="uukey('sites', 'createdBySitescomponent-' . $item->id)"/>
               @endforeach
            </div>
         </div>
         {{-- <div x-show="_page=='favorites'">
            <div class="grid grid-cols-1 gap-4 md:!grid-cols-2 lg:!grid-cols-3">
               @foreach ($favoriteSites as $item)
               <livewire:components.console.sites.items :$item :key="uukey('sites', 'favoriteSitescomponent-' . $item->id)"/>
               @endforeach
            </div>
         </div> --}}
      </div>
   </div>


   

   <template x-teleport="body">
      <x-modal name="site-share-modal" :show="false" removeoverflow="true" maxWidth="max-w-[var(--yena-sizes-3xl)]" >
         <livewire:components.console.sites.share :key="uukey('sites', 'share-modal-')"/>
      </x-modal>
   </template>

   
   <template x-teleport="body">
      <x-modal name="create-site-modal" :show="false" removeoverflow="true" maxWidth="xl" >
         <livewire:components.console.sites.create-modal :$isFolder :$folder lazy :key="uukey('sites', 'create-site-modal-')"/>
      </x-modal>
   </template>


   @script
   <script>
       Alpine.data('sites__page', () => {
          return {
            _page: 'all',

            init(){
               
            }
          }
       });
   </script>
   @endscript

</div>