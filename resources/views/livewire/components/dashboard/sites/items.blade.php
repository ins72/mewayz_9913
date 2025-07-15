
<?php

   use App\Models\YenaFavorite;
   use App\Models\Site;
   use App\Models\Page;
   use function Livewire\Volt\{state, mount, placeholder, on};

   on(['trashSite' => function ($id) {
      if(!$site = Site::where('id', $id)->first()) return false;

      // Check if the site belongs to the current user
      if($site->user_id !== iam()->id) return false;


      $site->delete();
      
      $this->dispatch('refreshSites');
      $this->dispatch('hideTippy');
   },
   
   'permanentlyTrashSite' => function($id) {
      if(!$site = Site::withTrashed()->where('id', $id)->first()) return false;
      // Check if the site belongs to the current user
      if($site->user_id !== iam()->id) return false;
      $site->deleteCompletely();
      // foreach ($site->sections()->get() as $section) {
         
      //    $section->getItems()->delete();

      //    $section->delete();
      // }

      // $site->forceDelete();

      $this->dispatch('refreshSites');
      $this->dispatch('hideTippy');
   },

   'restoreSite' => function($id){
      if(!$site = Site::withTrashed()->where('id', $id)->first()) return false;

      // Check if the site belongs to the current user
      if($site->user_id !== iam()->id) return false;
      
      $site->restore();

      $this->dispatch('refreshSites');
      $this->dispatch('hideTippy');
   },
   ]);

   mount(function(){
      
   });

   placeholder('<div class="--placeholder-skeleton w-[230px] h-[240px] rounded-[var(--yena-radii-sm)]"></div>');

   state([
      'item',
   ]);
?>

<div>
   
   <div class="yena-linkbox" x-data="{ tippy: {
      content: () => $refs.template.innerHTML,
      allowHTML: true,
      appendTo: document.body,
      maxWidth: 360,
      interactive: true,
      trigger: 'click',
      animation: 'scale',
   } }">
     <template x-ref="template">
         <div class="yena-menu-list !w-full">
            <div class="px-4">
               <p class="yena-text">{{ $item->name }}</p>
   
               <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">{{ __('Created') }} {{ \Carbon\Carbon::parse($item->created_at)->format('F d\t\h, Y') }}</p>
               <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">{{ __('by :who', [
                  'who' => $item->createdBy()->name
               ]) }}</p>
            </div>
   
            <hr class="--divider">
   
            <a @click="$dispatch('open-modal', 'site-share-modal'); $dispatch('registerSite', {site_id: '{{ $item->id }}'})" class="yena-menu-list-item">
               <div class="--icon">
                  {!! __icon('interface-essential', 'share-arrow.2', 'w-5 h-5') !!}
               </div>
               <span>{{ __('Share') }}</span>
            </a>
   
            <a href="" class="yena-menu-list-item !hidden">
               <div class="--icon">
                  {!! __icon('interface-essential', 'document-text-edit', 'w-5 h-5') !!}
               </div>
               <span>{{ __('Rename') }}</span>
            </a>
            <hr class="--divider">
            {{-- <a @click="$dispatch('setFavourite', {id: {{ $item->id }}}); $dispatch('hideTippy')" class="yena-menu-list-item cursor-pointer">
               <div class="--icon">
                  {!! __icon('interface-essential', 'star-favorite', 'w-5 h-5') !!}
               </div>
               <span>{{ $item->getFavorite($item->id) ? __('Remove from favorites') : __('Add to favorites') }}</span>
            </a> --}}
            <a @click="$dispatch('duplicateSite', {id: {{ $item->id }}}); $dispatch('hideTippy');" class="yena-menu-list-item cursor-pointer">
               <div class="--icon">
                  {!! __icon('interface-essential', 'copy-duplicate-object-add-plus', 'w-5 h-5') !!}
               </div>
               <span>{{ __('Duplicate') }}</span>
            </a>
            <a x-data="{
               __text:'{{ __('Copy Link') }}',
               site:@entangle('item')
            }" @click="$clipboard($store.builder.generateSiteLink(site)); __text = window.builderObject.copiedText;" class="yena-menu-list-item !hidden">
               <div class="--icon">
                  {!! __icon('interface-essential', 'share-arrow.1', 'w-5 h-5') !!}
               </div>
               <span x-text="__text">{{ __('Copy link') }}</span>
            </a>
            @if ($item->trashed())
            <hr class="--divider">
            <a class="yena-menu-list-item cursor-pointer" @click="$dispatch('restoreSite', {id: {{ $item->id }}})">
               <div class="--icon">
                  {!! __icon('interface-essential', 'backward-rearward-back.1', 'w-5 h-5') !!}
               </div>
               <span>{{ __('Restore') }}</span>
            </a>
            @endif
            <hr class="--divider">
            @if ($item->trashed())
            <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="$dispatch('permanentlyTrashSite', {id: {{ $item->id }}})">
               <div class="--icon">
                  {!! __icon('interface-essential', 'delete-disabled.2', 'w-5 h-5') !!}
               </div>
               <span>{{ __('Permanently delete') }}</span>
            </a>
            @else
            <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="$dispatch('trashSite', {id: {{ $item->id }}})">
               <div class="--icon">
                  {!! __icon('interface-essential', 'trash-bin-delete', 'w-5 h-5') !!}
               </div>
               <span>{{ __('Send to trash') }}</span>
            </a>
            @endif
        </div>
     </template>

      <div class="flex flex-col h-full">
         <div @click="window.location.replace('{{ route('dashboard-builder-index', ['slug' => $item->_slug]) }}')">


            <div class="-thumbnail">
               <div class="--thumbnail-inner" x-intersect="$store.builder.rescaleDiv($root)">

                  <div class="page-type-options !p-0 !m-0" x-data="templateIframeResize">
                     <div class="page-type-item iframe-container" x-intersect="_re">
                        <iframe src="{{ $item->getAddress() }}" frameborder="0" class="origin-[0_0]"></iframe>
                     </div>
                     <div class="!p-0"></div>
                  </div>
               </div>
            </div>
         </div>

         <div class="-content">
            <a claas="--over-lay" href="{{ route('dashboard-builder-index', ['slug' => $item->_slug]) }}">
               <p class="--title">{{ $item->name }}</p>
            </a>

            <div class="flex flex-col mt-0 w-full">
               <div class="w-full mb-2">
                  <div class="flex flex-wrap list-none gap-2 p-0">
                     @if ($item->published)
                     <span class="[transition-property:var(--yena-transition-property-common)] inline-flex align-top items-center max-w-[20ch] font-normal leading-[1.2] outline-offset-[2px] text-[color:var(--yena-colors-gray-500)] [box-shadow:var(--tag-shadow)] min-h-[1.25rem] min-w-[1.25rem] text-xs bg-[var(--yena-colors-gray-200)] no-underline outline-[transparent_solid_2px] rounded-md px-2">
                        {!! __i('--ie', 'globe-americas', 'h-[12px] inline-block leading-[1em] flex-shrink-0 align-top mr-1') !!}
                        {{ __('Public') }}
                     </span>
                     @endif

                     @foreach ($item->folderSites()->orderBy('id', 'desc')->get() as $folder)
                        <a href="{{ route('dashboard-folders-index', ['slug' => $folder->folder->slug]) }}" @navigate {{ _k() }} class="[transition-property:var(--yena-transition-property-common)] cursor-pointer inline-flex align-top items-center max-w-[20ch] font-normal leading-[1.2] outline-offset-[2px] text-[color:var(--tag-color)] [box-shadow:var(--tag-shadow)] min-h-[1.25rem] min-w-[1.25rem] text-xs bg-[var(--yena-colors-gray-200)] no-underline outline-[transparent_solid_2px] rounded-md px-2 hover:bg-[var(--yena-colors-trueblue-100)] hover:text-[var(--yena-colors-blackAlpha-900)] hover:no-underline">
                           {!! __i('Folders', 'folder-open', 'h-[12px] inline-block leading-[1em] flex-shrink-0 align-top mr-1') !!}
                           {{ $folder->folder->name }}
                        </a>
                     @endforeach
                  </div>
               </div>
               <div class="flex justify-between mt-1 w-full items-start">
                  <div class="flex items-center">
                     <div class="relative">
                        <div class="--avatar">
                           <img src="{{ $item->createdBy()->getAvatar() }}" class="w-full h-full object-cover" alt="">
                        </div>
                     </div>
                     <div class="mx-2 w-full">
                        <p class="meta-text truncate w-full break-all text-xs">
                           {{ __('Created by :who', [
                              'who' => iam()->get_original_user()->id == $item->created_by ? __('you') : $item->createdBy()->name
                           ]) }}
                        </p>
                        <p class="meta-text truncate w-full break-all text-[11px] text-[color:var(--yena-colors-gray-400)]">{{ __('Last edited :date', ['date' => \Carbon\Carbon::parse($item->updated_at)->diffForHumans()]) }}</p>
                     </div>
                  </div>

                  <div class="relative">
                     <button class="yena-button h-6 w-6 rounded-full transition-all hover:text-[color:#3c3838] hover:bg-[var(--yena-colors-gray-200)] flex items-center justify-center" x-tooltip="{...tippy}">
                        {!! __icon('interface-essential', 'dots-menu', 'w-5 h-5') !!}
                     </button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>