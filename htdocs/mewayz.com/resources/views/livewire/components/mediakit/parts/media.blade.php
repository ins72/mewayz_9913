
<?php

use App\Models\SitesUpload;
use function Livewire\Volt\{state, mount, usesFileUploads, updated, on, placeholder};

usesFileUploads();

state(['site']);
on([
   'mediaEventToDispatch' => fn ($event) => $this->eventToDispatch = $event,
   'mediaEventDispatcher' => function($event, $sectionBack){

      dd($event);
      $this->eventToDispatch = $event;
      if($sectionBack) $this->sectionBack = $sectionBack;
   },
]);
state([
    'sectionBack' => 'page="pages"',
    'image' => null,
    'medias' => [],

    'selected' => null,
    'selectedMedias' => [],
    'mediaTotalSize' => fn() => $this->site->getUploadedSizesMB(),
    'eventToDispatch',
]);

updated([
    'image' => function(){
        $this->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:5048'
        ]);
        $filesystem = sandy_filesystem('media/site/images');
        $image = $this->image->storePublicly('media/site/images', $filesystem);
        $image = str_replace("media/site/images/", "", $image);
        

        $size = storageFileSize('media/site/images', $image);

        $upload = new SitesUpload;
        $upload->site_id = $this->site->id;
        $upload->size = $size;
        $upload->name = basename($image);
        $upload->path = $image;
        $upload->save();

        $this->getMedia();

        $this->mediaTotalSize = $this->site->getUploadedSizesMB();
    },
]);

mount(fn() => $this->getMedia());

placeholder('
   <div class="p-5 w-[100%] mt-1">
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');

// Methods

$getMedia = function(){
    $this->medias = SitesUpload::where('site_id', $this->site->id)->get();
};

?>

<div x-data="media__section">
    <div class="media-section">
        <div class="header-navbar">
           <ul >
                <li class="close-header !flex">
                    <a @click="{{ $sectionBack }}">
                        <span>
                            {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                        </span>
                    </a>
                </li>
                <li class="!pl-0">{{ __('Media') }}</li>
                <li class="header-navbar-options">
                    <button class="btn select-media">{{ __('Select') }}</button>
                    <button class="btn btn-save close-edit-settings">{{ __('Done') }}</button>
                </li>
           </ul>
        </div>
        <div class="container-small sticky">
            <div class="tab-link">
                <ul class="tabs">
                <li class="tab !w-[100%]" @click="__tab = 'uploads'" :class="{'active': __tab == 'uploads'}">{{ __('Uploads') }}</li>
                <li class="tab !w-[100%]" @click="__tab = 'unsplash'" :class="{'active': __tab == 'unsplash'}">{{ __('Unsplash') }}</li>
                </ul>
            </div>
        </div>
        <div class="container-small tab-content-box">
            <div class="tab-content">
                <div x-cloak :class="{'active':__tab == 'uploads'}" data-tab-content>
                    <div class="device-library">
                       <div class="upload-manager" x-data="{ uploading: false, progress: 0 }"
                       x-on:livewire-upload-start="uploading = true"
                       x-on:livewire-upload-finish="uploading = false"
                       x-on:livewire-upload-cancel="uploading = false"
                       x-on:livewire-upload-error="uploading = false"
                       x-on:livewire-upload-progress="progress = $event.detail.progress">
                          <div class="upload-card relative">
                            
                            <input type="file" wire:model="image" name="image" class="absolute right-0 top-0 w-[100%] h-full opacity-0">

                             <div class="upload-box mb-1">
                                {!! __icon('interface-essential', 'image-picture-upload-arrow', 'w-5 h-5') !!}
                             </div>

                             <p x-cloak x-show="uploading" x-text="'{{ __('Uploading') }}' + ' · ' + progress + '%'"></p>

                             <p x-cloak x-show="!uploading">{{ __('Add image  · 5MB max') }} </p>
                          </div>
                          <button class="btn !hidden" x-cloak :class="{'!hidden': !uploading}">{{ __('Cancel') }}</button>

                          <button class="btn" x-cloak :class="{'!hidden': uploading}">{{ $mediaTotalSize }}MB of 100MB used  · Upgrade</button>
                        
                          <div class="files">
                            @foreach ($medias as $item)
                            <div class="file-card" @click="$wire.set('selected', {{ $item->id }}); $dispatch('{{ $eventToDispatch }}', {
                                public: '{{ $item->getMedia() }}',
                                image: '{{ $item->path }}',
                            })">
                                <label >
                                    @if ($selected == $item->id)
                                    <span class="checkmark">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20 6L9 17L4 12" stroke="var(--background)" stroke-linecap="square"></path>
                                        </svg>
                                    </span>
                                    @endif
                                    <span class="media-cover {{ $selected == $item->id ? 'active' : '' }}"></span>
                                    <img src="{{ $item->getMedia() }}" alt="">
                                   <div class="image-options"></div>
                                </label>
                             </div>
                            @endforeach
                          </div>
                          
                          <template>
                            <div class="files">
                                @foreach ($medias as $item)
                                <div class="file-card" @click="$wire.set('selected', {{ $item->id }});">
                                    <label >
                                        @if ($selected)
                                        <span class="checkmark">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20 6L9 17L4 12" stroke="var(--background)" stroke-linecap="square"></path>
                                            </svg>
                                        </span>
                                        @endif
                                        <span class="media-cover {{ $selected == $item->id ? 'active' : '' }}"></span>
                                        <img src="{{ $item->getMedia() }}" alt="">
                                       <div class="image-options"></div>
                                    </label>
                                 </div>
                                @endforeach
                            </div>
                          </template>
                          <div class="files mt-2" style="">
                             <div class="file-card">
                                <label >
                                   <span class="checkmark">
                                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                         <path d="M20 6L9 17L4 12" stroke="var(--background)" stroke-linecap="square"></path>
                                      </svg>
                                   </span>
                                   <span class="active media-cover"></span><img src="https://sitefile.co/65bc05d6f89980ab8a1f06fa/1706920624387_pexels-pat-whelen-5579634.jpg" alt="" loading="lazy">
                                   <div class="image-options"></div>
                                </label>
                             </div>
                             <div class="file-card">
                                <label >
                                   <!----><span class="media-cover"></span><img src="https://sitefile.co/65bc05d6f89980ab8a1f06fa/1706919535192_55cc616fc0d13935c9a65f524f425c0a~c5_100x100.jpeg" alt="" loading="lazy">
                                   <div class="image-options"></div>
                                </label>
                             </div>
                             <div class="file-card">
                                <label >
                                   <!----><span class="media-cover"></span><img src="https://sitefile.co/65bc05d6f89980ab8a1f06fa/1706919546886_WhatsApp_Image_2024-02-02_at_16.52.03_3d30bc60.jpg" alt="" loading="lazy">
                                   <div class="image-options"></div>
                                </label>
                             </div>
                          </div>
                       </div>
                    </div>
                </div>
                <div x-cloak :class="{'active':__tab == 'unsplash'}" data-tab-content>
                    <div class="unsplash-library">
                       <div class="upload-manager">
                          <form action="">
                             <div class="input-box">
                                <input type="text" placeholder="Search free photos" class="input-small search-input">
                                <div class="input-icon zoom-icon">
                                   <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M21.5067 21.5067L16 16M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18Z" stroke="var(--c-mix-2)" stroke-linecap="square"></path>
                                   </svg>
                                </div>
                             </div>
                          </form>
                          <div class="files mt-2" style="display: none;"></div>
                          <div class="files mt-2"></div>
                          <span class="loading-state-indicator">
                             <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 80 80" >
                                <circle cx="30" cy="50" r="8" fill="var(--foreground)" >
                                   <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0s" repeatCount="indefinite" ></animate>
                                </circle>
                                <circle cx="50" cy="50" r="8" fill="var(--foreground)" >
                                   <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.2s" repeatCount="indefinite" ></animate>
                                </circle>
                                <circle cx="70" cy="50" r="8" fill="var(--foreground)" >
                                   <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.4s" repeatCount="indefinite" ></animate>
                                </circle>
                             </svg>
                          </span>
                       </div>
                    </div>
                </div>
            </div>
        </div>
     </div>

     @script
        <script>
            Alpine.data('media__section', () => {
               return {
                  __tab: 'uploads',
                  media__page: 'main'
               }
            });
        </script>
     @endscript
</div>
