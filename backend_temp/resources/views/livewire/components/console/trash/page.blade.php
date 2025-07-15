
<?php

   use App\Models\Site;
   use App\Models\Page;
   use function Livewire\Volt\{state, mount, placeholder, on};

   mount(function(){
      $this->getSites();
   });

   placeholder('placeholders.console.sites.page-placeholder');

   state([
      'sites' => [],
   ]);

   on(['refreshSites' => function(){
      $this->getSites();
   }]);
   
   $getSites = function() {
      $this->sites = Site::where('user_id', iam()->id)->orderBy('id', 'DESC')->onlyTrashed()->get();
   };
?>

<div>
   
   <div class=" mb-6">
      <div class="flex flex-col mb-4">
         
         <div class="flex items-center h-6">
            <h2 class=" font-semibold text-lg">
               {!! __icon('interface-essential', 'trash-bin-delete', 'w-6 h-6 inline-block') !!}

               <span class="ml-2">{{ __('Trash') }}</span>
            </h2>
         </div>
      </div>
   </div>


   <div class="">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
         @foreach ($sites as $item)
            <livewire:components.console.sites.items :$item lazy :key="uukey('sites', 'trashSitescomponent-' . $item->id)"/>
         @endforeach
      </div>
   </div>
</div>