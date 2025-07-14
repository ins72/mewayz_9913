
<?php

   use App\Models\Site;
   use App\Models\Page;
   use App\Models\Foler;
   use function Livewire\Volt\{state, mount, placeholder, on};

   mount(function(){
      $this->getSites();
   });

   placeholder('placeholders.console.sites.page-placeholder');

   state([
      'folder' => [],
      'sites' => [],
      'slug',
   ]);

   on(['refreshSites' => function(){
      $this->getSites();
   }]);
   
   $getSites = function() {
      if(!$this->folder = Folder::where('slug', $this->slug)->first()) return;

      // Check if i belong to this folder later.



      $this->sites = $this->folder->sites;

      // Get folder sites


   };

   $createBlank = function(){
      // Check if plan allows it.

      // Create a blank site

      $randomSlug = str()->random(15);
      $addressSlug = str()->random(7);

      $_slug = "Untitled-$randomSlug";
      $name = "Untitled-$addressSlug";
      $address = "untitled-$addressSlug";

      $new = new Site;
      $new->address = $address;
      $new->_slug = $_slug;
      $new->name = $name;
      $new->user_id = iam()->id;
      $new->created_by = iam()->id;
      $new->save();

      // Create a default page;

      $page = new Page;
      $page->site_id = $new->id;
      $page->name = 'Home';
      $page->slug = 'home';
      $page->default = 1;
      $page->published = 1;
      $page->save();

      $route = route('console-builder-index', ['slug' => $_slug]);

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
   
   <div class=" mb-6">
      <div class="flex flex-col mb-4">
         
         <div class="flex items-center h-6">
            <h2 class=" font-semibold text-lg">
               {!! __icon('interface-essential', 'browser-internet-web-network-window-app-icon', 'w-6 h-6 inline-block') !!}

               <span class="ml-2">{{ __('All Sites') }}</span>
            </h2>
         </div>
         <div class="flex flex-col lg:flex-row mt-4 gap-4">
            
            <a href="" class="yena-button-stack --black">
               <div class="--icon">
                  {!! __icon('interface-essential', 'plus-add.3', 'w-6 h-6') !!}
               </div>

               {{ __('Create new') }}

               <div class="inline-flex self-center shrink-0 ml-2">
                  <div class="--badge">{{ __('AI') }}</div>
               </div>
            </a>
            
            <a wire:click="createBlank" class="yena-button-stack cursor-pointer">
               <div class="--icon">
                  {!! __icon('interface-essential', 'plus-add.3', 'w-6 h-6') !!}
               </div>

               {{ __('New from blank') }}
            </a>
         </div>
      </div>

      <div class="flex items-center">
         <div class="overflow-hidden">
            <ul class="yena-wrap-list">
               <li class="yena-wrap-list-item">
                  <a href="" class="yena-button-o">
                     <span class="--icon">
                        {!! __icon('interface-essential', 'media-library-folder.1', 'w-5 h-5') !!}
                     </span>
                     {{ __('All') }}
                  </a>
               </li>
               <li class="yena-wrap-list-item">
                  <a href="" class="yena-button-o -active">
                     <span class="--icon">
                        {!! __icon('interface-essential', 'alarm-clock-time-timer', 'w-5 h-5') !!}
                     </span>
   
                     {{ __('Recently viewed') }}
                  </a>
               </li>
               <li class="yena-wrap-list-item">
                  <a href="" class="yena-button-o">
                     <span class="--icon">
                        {!! __icon('interface-essential', 'user-circle', 'w-5 h-5') !!}
                     </span>
   
                     {{ __('Create by you') }}
                  </a>
               </li>
               <li class="yena-wrap-list-item">
                  <a href="" class="yena-button-o">
                     <span class="--icon">
                        {!! __icon('interface-essential', 'star-favorite', 'w-5 h-5') !!}
                     </span>
   
                     {{ __('Favorites') }}
                  </a>
               </li>
            </ul>
         </div>
      </div>
   </div>


   <div class="">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
         @foreach ($sites as $item)
            <livewire:components.console.sites.items :$item lazy :key="uukey()"/>
         @endforeach
      </div>
   </div>
</div>