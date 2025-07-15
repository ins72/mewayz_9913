
<?php
   use App\Models\Site;
   use App\Livewire\Actions\ToastUp;
   use function Livewire\Volt\{state, mount, placeholder, updated, uses, on};

   uses([ToastUp::class]);
   mount(function(){
      
   });

   state([
      'query' => null,
   ]);

   state([
      'sites' => []
   ]);

   mount(function(){
      // $this->getSites();
   });

   updated([
      'query' => function(){
         $this->getSites();
      },
   ]);

   $getSites = function(){
      $_id = iam() ? iam()->id : null;
      $this->sites = Site::where('name', 'like', '%'.$this->query.'%')->where('user_id', $_id)->orderBy('updated_at', 'DESC')->limit(5)->get();
   };

   placeholder('
   <div class="p-0 w-[100%] mt-1">
      <div class="--placeholder-skeleton w-[100%] h-[60px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-[100%] h-[60px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-[100%] h-[60px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');
?>


<div class="w-[100%]">
   <div x-data>
      <div class="flex flex-col" x-init="$wire.getSites()">
         <form wire:submit="createFolder">
            <div class="flex items-start gap-4">
               <div class="w-[100%]">
                  <div class="yena-form-group">
                     <div class="--left-element !w-auto !h-auto !p-4">
                        {!! __i('interface-essential', 'search.1', 'w-5 h-5') !!}
                     </div>
   
                     <input type="text" wire:model.live.debounce.400ms="query" class="!rounded-none !border-none !h-auto !py-4 !pl-12" placeholder="{{ __('Jump to') }}">
                     <div class="--right-element !hidden" wire:loading.class.remove="!hidden" wire:target="query">
                        <div class="yena-spinner !w-4 !h-4 !border-2"></div>
                     </div>
                  </div>
               </div>
            </div>
         </form>
      </div>
      <div class="!px-6 !pb-5">

         <div class="!mt-5">
            <p class="text-[color:var(--yena-colors-gray-500)] text-sm font-semibold">
               {{ __('Sites') }}
            </p>

            <div class="flex flex-col gap-1 !mt-5 transition duration-500 ease-in-out {{ empty($sites) ? 'h-0' : 'h-[var(--yena-sizes-lg)] max-h-[var(--yena-sizes-lg)] md:h-[var(--yena-sizes-xl)] md:max-h-[var(--yena-sizes-xl)]' }}">
               @foreach ($sites as $item)
               <div class="yena-linkbox !shadow-none" {!! __key($item->_slug) !!}>
                  <div class="flex flex-row h-full">
                     <div class="w-1/4" @click="window.location.replace('{{ route('dashboard-builder-index', ['slug' => $item->_slug]) }}')">
                        <div class="-thumbnail">
                           <div class="--thumbnail-inner" x-intersect="$store.builder.rescaleDiv($root)">
            
                              <div class="page-type-options !p-0 !m-0">
                                 <div class="page-type-item !h-[130px] !h-zz[120px]">
                                    <div class="container-small edit-board overflow-hidden !origin-[0px_0px]">
                                       <div class="card">
                                          <div class="card-body pointer-event-none relative" wire:ignore>
                                             @if ($staticPreview = $item->staticSitePreview())
                                             <img src="{{ $staticPreview->thumbnail }}" class="object-cover object-top" alt="">
                                             @else
                                             <livewire:site.generate lazy :site="$item" :key="uukey('search-modal', 'sites-jump-modal-preview-' . $item->_slug)" />
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
            
                        <div class="flex flex-col mt-0 w-[100%]">
                           <div class="w-[100%] mb-2">
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
                           <div class="flex justify-between mt-1 w-[100%] items-start">
                              <div class="flex items-center">
                                 <div class="relative">
                                    <div class="--avatar">
                                       <img src="{{ $item->createdBy()->getAvatar() }}" class="w-[100%] h-full object-cover" alt="">
                                    </div>
                                 </div>
                                 <div class="mx-2 w-[100%]">
                                    <p class="meta-text truncate w-[100%] break-all text-xs">
                                       {{ __('Created by :who', [
                                          'who' => iam()->get_original_user()->id == $item->created_by ? __('you') : $item->createdBy()->name
                                       ]) }}
                                    </p>
                                    <p class="meta-text truncate w-[100%] break-all text-[11px] text-[color:var(--yena-colors-gray-400)]">{{ __('Last edited :date', ['date' => \Carbon\Carbon::parse($item->updated_at)->diffForHumans()]) }}</p>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               @endforeach
            </div>
         </div>
      </div>

      <div class="overflow-hidden hidden md:block opacity-100 h-auto">
         <hr class="opacity-60 [border-image:initial] [border-color:inherit] border-solid w-[100%]">
         <div class="flex items-center flex-row gap-2 p-4 px-6">
            <p class="text-sm text-[var(--yena-colors-gray-600)]">
               <span>👋</span>{{ __('Tip: You can open this anywhere by pressing') }} <kbd class="bg-[var(--yena-colors-gray-100)] rounded-[var(--yena-radii-md)] text-[0.8em] font-[var(--yena-fontWeights-bold)] leading-[var(--yena-lineHeights-normal)] whitespace-nowrap px-[0.4em] [border-width:1px_1px_3px]">{{ __('Ctrl+K') }}</kbd></p>
            </div>
         </div>
   </div>
</div>