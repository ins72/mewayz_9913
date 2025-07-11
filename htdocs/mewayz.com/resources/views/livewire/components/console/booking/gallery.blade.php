<?php
    use App\Models\BookingService;
    use function Livewire\Volt\{state, mount, placeholder, updated, usesFileUploads};

    placeholder('
        <div class="p-0 w-full mt-0">
            <div class="--placeholder-skeleton w-full h-[100px] rounded-sm"></div>
            <div class="grid grid-cols-3 gap-3 mt-2">
                <div class="--placeholder-skeleton w-full h-[50px] rounded-sm"></div>
                <div class="--placeholder-skeleton w-full h-[50px] rounded-sm"></div>
                <div class="--placeholder-skeleton w-full h-[50px] rounded-sm"></div>
                <div class="--placeholder-skeleton w-full h-[50px] rounded-sm"></div>
                <div class="--placeholder-skeleton w-full h-[50px] rounded-sm"></div>
                <div class="--placeholder-skeleton w-full h-[50px] rounded-sm"></div>
            </div>
        </div>
    ');

    usesFileUploads();

    state([
        'user' => fn() => iam(),
    ]);
    state([
        'image' => null,
        'userArray' => [],
    ]);

    updated([
        'image' => function(){
            $this->skipRender();
            $this->validate([
                'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5048'
            ]);

            $filesystem = sandy_filesystem('media/booking/image');
            $images = $this->user->booking_gallery ?: [];
            
            foreach ($this->image as $photo) {
                $image = $photo->storePublicly('media/booking/image', $filesystem);
                $image = str_replace("media/booking/image/", "", $image);
                
                $images[] = $image;
            }

            $this->user->booking_gallery = $images;
            $this->user->save();
            $this->refresh();
        },
    ]);

    mount(function(){
        $this->refresh();
    });

    $refresh = function() {
        $this->userArray = $this->user->toArray();
    };

    $deleteMedia = function($index){
        $media = $this->user->booking_gallery;
        if(!is_array($media)) return;

        if(!in_array($index, $media)) return;

        
        storageDelete('media/booking/image', $index);
        
        foreach ($media as $key => $value) {
            if($value == $index) unset($media[$key]);
        }

        $media = array_values($media);

        $this->user->booking_gallery = $media;
        $this->user->save();
        $this->refresh();
    };
?>

<div>
    <div x-data="console__booking_gallery">
        <div class="media-section ![height:initial] !relative ![top:initial] !w-full ![z-index:initial] !border-0 ![background:transparent]" wire:ignore>
            <div class="container-small tab-content-box !p-0 !m-0 ![background:transparent]">
               <div class="tab-content">
                     <div class="active" data-tab-content>
                        <div class="device-library ![overflow:initial] !relative ![height:initial] !p-0 ![background:transparent]">
                           <div class="upload-manager"
                           x-on:livewire-upload-start="uploading = true"
                           x-on:livewire-upload-finish="uploading = false"
                           x-on:livewire-upload-cancel="uploading = false"
                           x-on:livewire-upload-error="uploading = false"
                           x-on:livewire-upload-progress="progress = $event.detail.progress">
                              <div class="upload-card relative !bg-white rounded-lg ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)]">
                                 
                                 <input type="file" wire:model="image" multiple name="image" class="absolute right-0 top-0 w-full h-full opacity-0">
    
                                 <div class="upload-box mb-1">
                                    {!! __icon('interface-essential', 'image-picture-upload-arrow', 'w-5 h-5') !!}
                                 </div>
    
                                 <p x-cloak x-show="uploading" x-text="'{{ __('Uploading') }}' + ' · ' + progress + '%'"></p>
    
                                 <p x-cloak x-show="!uploading">{{ __('Add image  · 5MB max') }} </p>
                              </div>
                           
                              <div class="files !p-0" :class="{
                                 '!hidden': !user.booking_gallery || user.booking_gallery && user.booking_gallery.length == 0
                              }">
    
                              <template x-for="(media, index) in user.booking_gallery" :key="index">
                                 <div class="file-card">
                                    
                                    <label x-data="{m: getMedia(media)}" x-intersect="m">
                                       <span class="checkmark !bg-white shadow shadow-xl" @click="$wire.deleteMedia(media)">
                                             {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                                       </span>
                                       <span class="media-cover"></span>
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
    </div>

    @script
        <script>
            Alpine.data('console__booking_gallery', () => {
                return {
                    __page: '-',
                    uploading: false,
                    progress: 0,
                    gs: '{{ gs('media/booking/image') }}',
                    user: @entangle('userArray'),

                    getMedia(media){
                        return this.gs +'/'+ media;
                    },

                    init(){
                        // console.log(this.user)
                    }
                }
            });
        </script>
    @endscript
</div>