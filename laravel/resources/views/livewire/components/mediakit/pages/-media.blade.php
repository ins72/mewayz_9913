
<?php

use App\Models\MediakitSitesUpload;
use function Livewire\Volt\{state, mount, usesFileUploads, updated, on, placeholder};
use MarkSitko\LaravelUnsplash\Facades\Unsplash;
use MarkSitko\LaravelUnsplash\Models\UnsplashAsset;

usesFileUploads();

state(['site']);
on([
   'mediaEventToDispatch' => fn ($event) => $this->eventToDispatch = $event,
   'mediaEventDispatcher' => function($event, $sectionBack){
      $this->eventToDispatch = $event;
      if($sectionBack) $this->sectionBack = $sectionBack;
   },
]);
state([
    'sectionBack' => "closePage()",
    'image' => null,
    'medias' => [],

    'selected' => null,
    'selectedMedias' => [],
    'mediaTotalSize' => fn() => $this->site->getUploadedSizesMB(),
    'eventToDispatch',
    'multiSelect' => false,
    'diskSize' => fn() => get_plan_storage_bytes($this->site, true),
    'diskSizePercent' => 0,
]);

state([
   'unsplashMedia' => [],
   'unsplashQuery' => '',
]);

state([
   'aiMedia' => [],
]);

updated([
    'image' => function(){
        $this->validate([
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5048'
        ]);
        
        $imageSizes = 0;
        
        foreach ($this->image as $photo) {
         $imageSizes += $photo->getSize();
        }

        $imageSizes += get_used_storage($this->site);
        if($imageSizes >= get_plan_storage_bytes($this->site)){
            session()->flash('error._error', __('Your current upload size has passed your plan storage quota, upload an image with lower filesize.'));
            return;
        }
        
        if(get_used_storage($this->site) > get_plan_storage_bytes($this->site)){
            session()->flash('error._error', __('You have passed your plan storage quota'));
            return;
        }

        $filesystem = sandy_filesystem('media/mediakit/images');
        $images = [];
        
        foreach ($this->image as $photo) {
            $image = $photo->storePublicly('media/mediakit/images', $filesystem);
            $image = str_replace("media/mediakit/images/", "", $image);
            $size = storageFileSize('media/mediakit/images', $image);
            $upload = new MediakitSitesUpload;
            $upload->site_id = $this->site->id;
            $upload->size = $size;
            $upload->name = basename($image);
            $upload->path = $image;
            $upload->save();

            
            $images[] = [
               'id' => $upload->id,
               'public' => $upload->getMedia(),
               'path' => $upload->path,
            ];
        }

        if($this->eventToDispatch && !empty($up = $images[0])){
            $this->selected = ao($up, 'id');
            $this->dispatch($this->eventToDispatch, public: ao($up, 'public'), image: ao($up, 'path'));
        }
        
        $this->getMedia();
        $this->mediaTotalSize = $this->site->getUploadedSizesMB();
    },
]);

mount(function(){
   $this->getMedia();
   $this->getAiMedia();

   // $this->getDiskSize();
});

placeholder('
   <div class="p-5 w-[100%] mt-1">
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');

// Methods

$getDiskSize = function(){
   $size = 5;

   if($plan_size = __o_feature('consume.upload_storage', $this->site->user)){
      $size = $plan_size;
   }

   $size = (int) $size;

   $this->diskSize = $size;
};

$getMedia = function(){
   $this->diskSizePercent = get_used_storage_percent($this->site);
    $this->medias = MediakitSitesUpload::where('site_id', $this->site->id)->where('is_ai', 0)->where('trashed', 0)->get()->map(function($item){
      $item->get_media = $item->getMedia();
      return $item;
    })->toArray();
};

$deleteMedias = function(){
   foreach ($this->selectedMedias as $item) {
      if(!$media = MediakitSitesUpload::where('site_id', $this->site->id)->where('id', $item)->first()) continue;
      $media->trashed = 1;
      $media->save();

      storageDelete('media/mediakit/images', $media->path);

      $media->delete();
   }

   $this->selectedMedias = [];
   $this->multiSelect = false;
   $this->getMedia();
   $this->getAiMedia();
   $this->mediaTotalSize = $this->site->getUploadedSizesMB();
};

$getUnsplashImages = function() {
   $assets = Unsplash::randomPhoto()->count(30)->toJson();
   return $assets;

   // $assets = UnsplashAsset::get();

   // if(!UnsplashAsset::first()){
   //    $assets = Unsplash::randomPhoto()
   //    ->count(30)
   //    ->toJson();
   // }

   // $this->unsplashMedia = $assets;
};

$searchUnsplash = function($query){
      $validator = Validator::make([
         'query' => $query
      ], [
         'query' => 'required|string|min:2'
      ]);

      if($validator->fails()){
         return [
            'status' => 'error',
            'response' => $validator->errors()->first('query'),
         ];
      }
      
      $response = Unsplash::randomPhoto()->search()->term($query)->count(30)->toJson();


      return [
         'status' => 'success',
         'response' => $response->results,
      ];
};


$getAiMedia = function(){
    $this->aiMedia = MediakitSitesUpload::where('site_id', $this->site->id)->where('is_ai', 1)->where('trashed', 0)->get()->map(function($item){

      $media = $item->getMedia();

      $item->get_media = $media;
      return $item;
    })->toArray();
};

$saveAiMedia = function($image, $url, $upload_id){
   $filesystem = sandy_filesystem('media/mediakit/images');

   \Storage::disk($filesystem)->put("media/mediakit/images/$image", file_get_contents($url));
   $size = storageFileSize('media/mediakit/images', $image);

   $upload = MediakitSitesUpload::find($upload_id);
   $upload->size = $size;
   $upload->name = basename($image);
   $upload->path = $image;
   $upload->ai_uploaded = 1;
   $upload->save();
   $this->getAiMedia();

   $this->mediaTotalSize = $this->site->getUploadedSizesMB();
};

$generateAiMedia = function($query){
   if(!__o_feature('feature.ai_images', $this->site->user)) return;
   $validator = Validator::make([
      'query' => $query
   ], [
      'query' => 'required|string|min:4'
   ]);

   if($validator->fails()){
      return [
         'status' => 'error',
         'response' => $validator->errors()->first('query'),
      ];
   }

   $client = \OpenAI::client(config('app.openai_key'));
   $response = $client->images()->create([
       'model' => 'dall-e-3',
       'n' => 1,
       'prompt' => $query,
       'size' => '1024x1024',
       'response_format' => 'url',
   ]);
   
   foreach ($response->data as $data) {
     $file = 'base64_'. md5(\Carbon\Carbon::now()->toDateTimeString());
     $image = "$file.png";


      $upload = new MediakitSitesUpload;
      $upload->site_id = $this->site->id;
      $upload->is_ai = 1;
      $upload->size = 1000;
      $upload->name = basename($image);
      $upload->path = null;
      $upload->temp_ai_url = $data->url;
      $upload->save();
      // $this->aiMedia->push($upload);
      $this->js('$wire.saveAiMedia("'. $image .'", "'. $data->url .'", "'. $upload->id .'")');



      // if($this->eventToDispatch){
      //  $this->selected = $upload->id;
      //  $this->dispatch($this->eventToDispatch, public: $upload->getMedia(), image: $upload->path);
      // }
   }
    
   
   $this->getAiMedia();
   return [
      'status' => 'success',
      'response' => '',
   ];
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
                <li class="!pl-0" x-text="multiSelect && selectedMedias.length > 0 ? selectedMedias.length + ' ' + '{{ __('Selected') }}' : '{{ __('Media') }}'"></li>
                <li class="header-navbar-options">
                    <template x-if="__tab!=='unsplash'">
                     <button class="btn select-media" :class="{'!hidden':multiSelect}" @click="multiSelect=true">{{ __('Select') }}</button>
                    </template>

                    <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0" :class="{'!hidden':!multiSelect || selectedMedias.length == 0}" @click="$wire.deleteMedias">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>

                    <button class="btn btn-save close-edit-settings" @click="multiSelect ? multiSelect=false : {{ $sectionBack }}">{{ __('Done') }}</button>
                </li>
           </ul>
        </div>
        <div class="container-small sticky">
            <div class="tab-link">
                <ul class="tabs">
                  <li class="tab !w-[100%] !mr-0" @click="__tab = 'uploads'" :class="{'active': __tab == 'uploads'}">{{ __('Uploads') }}</li>

                  <li class="tab !w-[100%] !mr-0" @click="__tab = 'unsplash'; getUnsplashMedia()" :class="{'active': __tab == 'unsplash', '!hidden': multiSelect}">{{ __('Unsplash') }}</li>

                  <li class="tab !w-[100%]" @click="__tab = 'ai'" :class="{'active': __tab == 'ai', '!hidden': multiSelect}">
                     {!! __i('Photo Edit', 'Magic Wand, Photo, Edit', 'w-5 h-5') !!}
                     {{ __('Ai') }}
                  </li>
                </ul>
            </div>
        </div>
        <div class="container-small tab-content-box">
            <div class="tab-content">
                <div x-cloak :class="{'active':__tab == 'uploads'}" data-tab-content>
                    <div class="device-library">
                       <div class="upload-manager"
                       x-on:livewire-upload-start="uploading = true"
                       x-on:livewire-upload-finish="uploading = false"
                       x-on:livewire-upload-cancel="uploading = false"
                       x-on:livewire-upload-error="uploading = false"
                       x-on:livewire-upload-progress="progress = $event.detail.progress">
                          <div class="upload-card relative" :class="{'!hidden':multiSelect}">
                            
                            <input type="file" wire:model="image" multiple name="image" class="absolute right-0 top-0 w-[100%] h-full opacity-0">

                             <div class="upload-box mb-1">
                                {!! __icon('interface-essential', 'image-picture-upload-arrow', 'w-5 h-5') !!}
                             </div>

                             <p x-cloak x-show="uploading" x-text="'{{ __('Uploading') }}' + ' · ' + progress + '%'"></p>

                             <p x-cloak x-show="!uploading">{{ __('Add image  · 5MB max') }} </p>
                          </div>
                          <button class="btn !hidden" x-cloak :class="{'!hidden': !uploading}">{{ __('Cancel') }}</button>

                          <button class="btn storage-media-before" :style="{
                           '--storage-width': diskSizePercent + '%'
                          }" x-cloak @click="$dispatch('open-modal', 'upgrade-modal')" :class="{'!hidden': uploading}">{{ $mediaTotalSize }} {{ __('of') }} {{ $diskSize }} {{ __('used') }}  · {{ __('Upgrade') }}</button>

                           @php
                              $error = false;

                              if(!$errors->isEmpty()){
                                    $error = $errors->first();
                              }
                              if(Session::get('error._error')){
                                    $error = Session::get('error._error');
                              }
                           @endphp

                           @if ($error)
                              <div class="mb-1 bg-red-200 text-[11px] p-1 px-2 rounded-md">
                                 <div class="flex items-center">
                                    <div>
                                       <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                    </div>
                                    <div class="flex-grow ml-1 text-xs">{{ $error }}</div>
                                 </div>
                              </div>
                           @endif
                        
                          <div class="files">

                           <template x-for="media in medias" :key="media.id">
                              <div class="file-card" @click="clickMedia(media)">
                                 
                                <label x-data="{m: media.get_media}" x-intersect="m">
                                    <span class="checkmark" :class="{'!hidden': !isSelected(media.id)}">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20 6L9 17L4 12" stroke="var(--background)" stroke-linecap="square"></path>
                                        </svg>
                                    </span>
                                    <span class="media-cover" :class="{'active': isSelected(media.id)}"></span>
                                    <img :src="m" loading="lazy" alt="">
                                   <div class="image-options"></div>
                                </label>
                              </div>
                          </template>
                          </div>
                       </div>
                    </div>
                </div>
                <div x-cloak :class="{'active':__tab == 'unsplash'}" data-tab-content>
                    <div class="device-library zzunsplash-library">
                       <div class="upload-manager">
                          <form>
                             <div class="input-box">
                                <input type="text" placeholder="{{ __('Search free photos') }}" class="input-small search-input" @input="queryUnsplash($event.target.value)">
                                <div class="input-icon zoom-icon">
                                   <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M21.5067 21.5067L16 16M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18Z" stroke="var(--c-mix-2)" stroke-linecap="square"></path>
                                   </svg>
                                </div>
                             </div>
                             <template x-if="unsplashError">
                                <div class="bg-red-200 text-[11px] p-1 px-2 rounded-md mt-1">
                                   <div class="flex items-center">
                                      <div>
                                         <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                      </div>
                                      <div class="flex-grow ml-1 text-xs" x-text="unsplashError"></div>
                                   </div>
                                </div>
                             </template>
                           </form>

                          <span class="loading-state-indicator mt-2 block" :class="{
                           '!hidden': !unsplashLoading
                          }">
                              <span class="loader-o20 !text-[9px] !text-black mx-auto block"></span>
                          </span>

                          
                          <div class="files mt-2">

                              <template x-for="media in unsplashMedia" :key="media.id">
                                 <div class="file-card" @click="clickUnsplash(media)">
                                    
                                 <label x-data="{m: media.urls.small}" x-intersect="m">
                                       <span class="checkmark" :class="{'!hidden': !unsplashSelected(media.id)}">
                                          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                             <path d="M20 6L9 17L4 12" stroke="var(--background)" stroke-linecap="square"></path>
                                          </svg>
                                       </span>
                                       <span class="media-cover" :class="{'active': unsplashSelected(media.id)}"></span>
                                       <img :src="m" loading="lazy" alt="">
                                    <div class="image-options"></div>
                                 </label>
                                 </div>
                           </template>
                          </div>
                       </div>
                    </div>
                </div>
                <div x-cloak :class="{'active':__tab == 'ai'}" data-tab-content>
                    <div class="device-library">
                       <div class="upload-manager">
                        <template x-if="!__o_feature('feature.ai_images')">
                           <div class="flex flex-col justify-center items-center px-[20px] pt-[60px]">
                              {!! __i('Design Tools', 'magic-wand-circle', 'w-14 h-14') !!}
                              <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                                    {!! __t('Upgrade your plan to create AI images.') !!}
                              </p>
                              <button type="button" @click="$dispatch('open-modal', 'upgrade-modal')" class="btn btn-large mt-3 !h-[40px] !border-none !transition-none !mb-0">{{ __('Upgrade') }}</button>
                           </div>
                        </template>
                          <form :class="{
                           '!hidden': !__o_feature('feature.ai_images')
                          }">
                             <div class="input-box">
                                <input type="text" placeholder="{{ __('photo of an extremely cute alien fish swimming an alien habitable underwater planet') }}" class="input-small search-input" x-model="aiQuery">
                                <div class="input-icon zoom-icon !w-[80px]" @click="queryAi">
                                    <a class="button -smaller !h-[23px] !bg-white shadow-lg !text-black mr-1">
                                       <span :class="{
                                          '!hidden': aiLoading
                                         }">{{ __('Generate') }}</span>

                                       <span class="block" :class="{
                                        '!hidden': !aiLoading
                                       }">
                                           <span class="loader-o20 !text-[9px] !text-black mx-auto block"></span>
                                       </span>
                                    </a>
                                </div>
                             </div>
                             <template x-if="aiError">
                                <div class="bg-red-200 text-[11px] p-1 px-2 rounded-md mt-1">
                                   <div class="flex items-center">
                                      <div>
                                         <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                      </div>
                                      <div class="flex-grow ml-1 text-xs" x-text="aiError"></div>
                                   </div>
                                </div>
                             </template>
                           </form>

                          <span class="loading-state-indicator mt-2 block" :class="{
                           '!hidden': !unsplashLoading
                          }">
                              <span class="loader-o20 !text-[9px] !text-black mx-auto block"></span>
                          </span>

                          
                          <div class="files mt-2">

                              <template x-for="media in aiMedias" :key="media.id">
                                 <div class="file-card" @click="clickMedia(media)">
                                    
                                 <label x-data="{m: media.get_media}" x-intersect="m">
                                       <span class="checkmark" :class="{'!hidden': !isSelected(media.id)}">
                                          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                             <path d="M20 6L9 17L4 12" stroke="var(--background)" stroke-linecap="square"></path>
                                          </svg>
                                       </span>
                                       <span class="media-cover" :class="{'active': isSelected(media.id)}"></span>
                                       <img :src="m" loading="lazy" alt="">
                                    <div class="image-options"></div>
                                 </label>
                                 </div>
                              </template>
                          </div>
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
                  media__page: 'main',
                  uploading: false,
                  progress: 0,
                  selected: @entangle('selected'),
                  medias: @entangle('medias').live,
                  eventToDispatch: @entangle('eventToDispatch').live,
                  multiSelect: @entangle('multiSelect'),
                  selectedMedias: @entangle('selectedMedias'),
                  titleText: '{{ __('Media') }}',
                  sectionBack: @entangle('sectionBack').live,
                  diskSizePercent: @entangle('diskSizePercent').live,
                  unsplashMedia: {},
                  unsplashLoading: false,

                  unsplashError: false,
                  unspashSaveTimer: null,
                  unsplash_selected: null,
                  
                  
                  aiMedias: @entangle('aiMedia').live,
                  aiError: false,
                  aiLoading: false,
                  aiQuery: '',
                  unsplashQuality: '{{ config('app.unsplashQuality') }}',
                  queryAi(){
                     let $this = this;
                     let query = $this.aiQuery;

                     if(!$this.__o_feature('feature.ai_images')) return;

                     $this.aiLoading = true;
                     $this.aiError = false;
                     $this.$wire.generateAiMedia(query).then(r => {
                        $this.aiLoading = false;
                        if(r.status === 'error'){
                           $this.aiError = r.response;
                        }
                        if(r.status === 'success'){
                           $this.aiError = false;
                        }
                     });
                  },


                  queryUnsplash(query){
                     let $this = this;
                     $this.unsplashLoading = true;

                     $this.unsplashError = false;
                     clearTimeout($this.unspashSaveTimer);

                     $this.unspashSaveTimer = setTimeout(function(){
                        $this.$wire.searchUnsplash(query).then(r => {
                           $this.unsplashLoading = false;
                           if(r.status === 'error'){
                              $this.unsplashError = r.response;
                           }
                           if(r.status === 'success'){
                              $this.unsplashError = false;

                              $this.unsplashMedia = r.response;
                           }
                        });
                     }, 1000);
                  },

                  getUnsplashMedia(){
                     let $this = this;
                     if($this.unsplashMedia.length > 0) return;


                     $this.unsplashLoading = true;

                     $this.$wire.getUnsplashImages().then(r => {
                        $this.unsplashLoading = false;

                        $this.unsplashMedia = r;
                     });
                  },

                  clickUnsplash(media){
                     let _id = 'unsplash:' + media.id;

                     this.unsplash_selected = _id;
                     this.$dispatch(this.eventToDispatch, {
                        public: media.urls[this.unsplashQuality],
                        image: media.urls[this.unsplashQuality],
                     });
                  },

                  unsplashSelected(_id){
                     _id = 'unsplash:' + _id;
                     if(this.unsplash_selected === _id) return true;

                     return false;
                  },

                  getTitle(){
                     //this.titleText = 

                     //if(this.multiSelect && this.selectedMedias.length > 0)
                  },

                  isSelected(media){
                     if(this.multiSelect){
                        const index = this.selectedMedias.indexOf(media);
                        if (index > -1) return true;
                     }

                     if(!this.multiSelect){
                        if(this.selected == media) return true;
                     }

                     return false
                  },


                  clickMedia(media){

                     if(this.multiSelect){

                        const index = this.selectedMedias.indexOf(media.id);
                        if (index > -1) {
                           this.selectedMedias.splice(index, 1);
                        }else{
                           this.selectedMedias.push(media.id);
                        }
                     }

                     if(!this.multiSelect){
                        this.selected = media.id;
                        this.$dispatch(this.eventToDispatch, {
                           public: media.get_media,
                           image: media.path,
                        });
                     }
                  },
                  
                  init(){
                     let $this = this;
                     
                     window.addEventListener("mediaEventDispatcher", (event) => {
                        var $e = event.detail;
                        
                        $this.eventToDispatch = $e.event;
                        $this.sectionBack = $e.sectionBack;
                     });
                  }
               }
            });
        </script>
     @endscript
</div>
