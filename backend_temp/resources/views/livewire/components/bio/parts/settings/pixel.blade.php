
<?php
    use App\Models\BioSitePixel;
    use App\Livewire\Actions\ToastUp;
    use function Livewire\Volt\{state, mount, placeholder, uses};
    uses([
        ToastUp::class,
    ]);

    state([
        'site'
    ]);

    mount(function() {
        
    });

    $getPixels = function(){
        $pixels = BioSitePixel::where('site_id', $this->site->id)->get();
        
        return $pixels->toArray();
    };

    $deletePixel = function($uuid){
        $this->skipRender();

        if(!$pixel = BioSitePixel::where('uuid', $uuid)->where('site_id', $this->site->id)->first()) return;
        
        $pixel->delete();
        $this->_f('success', __('Pixel deleted successfully'));
    };

    $editPixel = function ($item){
        $this->skipRender();

        if(empty(ao($item, 'name')) || empty(ao($item, 'pixel_id'))){
            return [
                'status' => 'error',
                'response' => __('Name & Pixel id is required')
            ];
        }

        if(!__o_feature('feature.pixel_codes')){
            return [
                'status' => 'error',
                'response' => __('Please upgrade to add pixel.')
            ];
        }

        if(!$pixel = BioSitePixel::where('uuid', ao($item, 'uuid'))->where('site_id', $this->site->id)->first()) return;

        $pixel->fill($item);
        $pixel->save();

        return [
            'status' => 'success',
            'response' => ''
        ];
    };

    $createPixel = function($item = []){
        $this->skipRender();

        if(empty(ao($item, 'name')) || empty(ao($item, 'pixel_id'))){
            return [
                'status' => 'error',
                'response' => __('Name & Pixel id is required')
            ];
        }

        if(!__o_feature('feature.pixel_codes')){
            return [
                'status' => 'error',
                'response' => __('Please upgrade to add pixel.')
            ];
        }

        $pixel = new BioSitePixel;
        $pixel->fill($item);
        $pixel->site_id = $this->site->id;
        $pixel->uuid = ao($item, 'uuid');
        $pixel->save();

        return [
            'status' => 'success',
            'response' => ''
        ];
    };
?>
<div wire:ignore>
    <div x-data="builder__settings_pixel" class="website-section">

        <div class="design-navbar">
            <ul >
                <li class="close-header !flex">
                  <a @click="__page='-'">
                    <span>
                        {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                    </span>
                  </a>
               </li>
               <li class="!pl-0">{{ __('Pixels') }}</li>
               <li class="!flex">
                <div x-data="{ tippy: {
                    content: () => $refs.template.innerHTML,
                    allowHTML: true,
                    appendTo: $root,
                    maxWidth: 350,
                    interactive: true,
                    trigger: 'click',
                    animation: 'scale',
                    placement: 'bottom-start'
                 } }" x-show="__o_feature('feature.pixel_codes')">
     
            
                    <button type="button" class="button -smaller" x-tooltip="tippy">
                        {{ __('Add') }}
                    </button>
     
                    <template x-ref="template" class="hidden">
                       <div class="yena-menu-list !min-w-[initial] !w-[350px] !max-w-[100%] !p-5">
                        
            
                        <form @submit.prevent="_create" class="bg-white overflow-auto">
            
                            <div class="flex overflow-auto show-overflowing gap-2">
                                <template x-for="(item, index) in skeleton" :key="index">
                                    <label class="sandy-big-checkbox">
                                        <input type="radio" name="type[]" class="sandy-input-inner" :value="index" x-model="pixel_type">
                                        <div class="checkbox-inner !bg-gray-100 !py-1 !px-3 !h-full !rounded-full">
                                            <div class="checkbox-wrap">
                                                <div>
                                                    <div class="h-7 w-7 rounded-full flex items-center justify-center text-white" :style="{
                                                        'background': item.color,
                                                    }">
                                                        <i :class="item.icon"></i>
                                                        <template x-if="item.svg">
                                                            <div x-html="item.svg"></div>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="content ml-2 flex items-center whitespace-nowrap">
                                                    <h1 x-text="item.name" class="!m-0"></h1>
                                                </div>
                                                <div class="icon ml-3">
                                                    <div class="active-dot w-4 h-4 rounded-sm">
                                                    <i class="la la-check font-10"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </template>
                            </div>
                
                            <div class="custom-content-input !border-dashed !mb-5">
                                <label class="h-10 !flex items-center px-5">
                                    <select name="pixel_status" x-model="pixel_status" class="text-sm border-0">
                                        <option value="1">{{ __('Active') }}</option>
                                        <option value="0">{{ __('Hidden') }}</option>
                                    </select>
                                </label>
                                <input type="text" x-model="pixel_name" placeholder="{{ __('Pixel Name') }}" class="w-[100%]">
                            </div>
                            
                            <div class="yena-input mb-1">
                                <label>{{ __('Pixel Id') }}</label>
                                <input x-model="pixel_id">
                            </div>
            
                            <template x-if="backendError">
                                <div class="bg-red-200 text-[11px] p-1 px-2 mb-1 rounded-full">
                                 <div class="flex items-center">
                                    <div>
                                       <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                    </div>
                                    <div class="flex-grow ml-1 text-xs !text-black" x-text="backendError"></div>
                                 </div>
                                </div>
                             </template>
    
    
                             <button class="yena-button-stack w-[100%]">{{__('Save')}}</button>
                        </form>
                       </div>
                    </template>
                </div>
             </li>
            </ul>
         </div>
         <template x-if="!__o_feature('feature.pixel_codes')">
             <div class="flex flex-col justify-center items-center px-[20px] py-[60px]">
                 {!! __i('Social Media', 'google-analytics', 'w-14 h-14') !!}
                 <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                     {!! __t('Upgrade your site plan to use and add pixel') !!}
                 </p>
                 <button type="button" @click="$dispatch('open-modal', 'upgrade-modal')" class="btn btn-large mt-3 !h-[40px] !border-none !transition-none">{{ __('Upgrade') }}</button>
             </div>
         </template>

         <div class="container-small p-[var(--s-2)] pb-[150px]" x-show="__o_feature('feature.pixel_codes')">
            <div>

                <div class="flex flex-col gap-4">
                    <template x-for="(pixel, index) in pixels" :key="index">
                        <a class="leo-avatar-o !bg-white [box-shadow:var(--yena-shadows-md)] border border-solid border-[var(--yena-colors-gray-200)]">
                            <div class="-avatar-inner !bg-white">
                                <div class="--avatar p-1 !rounded-full flex items-center justify-center {{--!text-white [&>i]:text-black--}}" :style="{
                                    'background': skeleton[pixel.pixel_type].color,
                                }">
                                    <template x-if="skeleton[pixel.pixel_type].svg">
                                        <div x-html="skeleton[pixel.pixel_type].svg"></div>
                                    </template>
                                    <template x-if="!skeleton[pixel.pixel_type].svg">
                                        <i class="text-sm" :class="skeleton[pixel.pixel_type].icon" :style="{
                                            'color': $store.builder.getContrastColor(skeleton[pixel.pixel_type].icon)
                                        }"></i>
                                    </template>
                                </div>
                    
                                <div class="-content">
                                    <div>
                                        <div class="--name !text-black" x-text="pixel.name"></div>
                                        <div class="--subtitle !text-black" x-text="pixel.pixel_id"></div>
                                    </div>
                                </div>
                    
                                <div class="flex items-center gap-2">
                                    <div x-data="{ tippy: {
                                        content: () => $refs.editTootipTemplate.innerHTML,
                                        allowHTML: true,
                                        appendTo: $root,
                                        maxWidth: 350,
                                        interactive: true,
                                        trigger: 'click',
                                        animation: 'scale',
                                        placement: 'bottom-start'
                                    } }">
                                    
                                        <div class="menu--icon !bg-gray-100 !w-6 !h-6 roudned-full" x-tooltip="tippy">
                                            {!! __i('interface-essential', 'pen-edit.5', 'w-3 h-3') !!}
                                        </div>
                                    </div>
                                    
                                    <div class="menu--icon !w-7 !h-7 !bg-red-400 !rounded-full" @click="_delete_pixel(pixel)">
                                        {!! __i('interface-essential', 'trash-delete-remove', 'text-white w-3 h-3') !!}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>

                <template x-ref="editTootipTemplate">
                    <div class="yena-menu-list !min-w-[initial] !w-[350px] !max-w-[100%] !p-5">
                        <form @submit.prevent="_edit(pixel)" class="bg-white overflow-auto">
            
                            <div class="flex overflow-auto show-overflowing gap-2">
                                <template x-for="(item, index) in skeleton" :key="index">
                                    <label class="sandy-big-checkbox">
                                        <input type="radio" name="type[]" class="sandy-input-inner" :value="index" x-model="pixel.pixel_type">
                                        <div class="checkbox-inner !bg-gray-100 !py-1 !px-3 !h-full !rounded-full">
                                            <div class="checkbox-wrap">
                                                <div>
                                                    <div class="h-7 w-7 rounded-full flex items-center justify-center text-white" :style="{
                                                        'background': item.color,
                                                    }">
                                                        <i :class="item.icon"></i>
                                                        <template x-if="item.svg">
                                                            <div x-html="item.svg"></div>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="content ml-2 flex items-center whitespace-nowrap">
                                                    <h1 x-text="item.name" class="!m-0"></h1>
                                                </div>
                                                <div class="icon ml-3">
                                                    <div class="active-dot w-4 h-4 rounded-sm">
                                                    <i class="la la-check font-10"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </template>
                            </div>
                
                            <div class="custom-content-input !border-dashed !mb-5">
                                <label class="h-10 !flex items-center px-5">
                                    <select name="pixel_status" x-model="pixel.pixel_status" class="text-sm border-0">
                                        <option value="1">{{ __('Active') }}</option>
                                        <option value="0">{{ __('Hidden') }}</option>
                                    </select>
                                </label>
                                <input type="text" x-model="pixel.name" placeholder="{{ __('Pixel Name') }}" class="w-[100%]">
                            </div>
                            
                            <div class="yena-input mb-1">
                                <label>{{ __('Pixel Id') }}</label>
                                <input x-model="pixel.pixel_id">
                            </div>
            
                            <template x-if="backendError">
                                <div class="bg-red-200 text-[11px] p-1 px-2 mb-1 rounded-full">
                                <div class="flex items-center">
                                    <div>
                                    <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                    </div>
                                    <div class="flex-grow ml-1 text-xs !text-black" x-text="backendError"></div>
                                </div>
                                </div>
                            </template>

    
                            <button class="yena-button-stack w-[100%]">{{__('Save')}}</button>
                        </form>
                    </div>
                </template>
            </div>
         </div>
    </div>
     @script
     <script>
      Alpine.data('builder__settings_pixel', () => {
         return {
            autoSaveTimer: null,
            pixels: {},
            skeleton: {!! collect(config('yena.pixels'))->toJson() !!},


            // Utils
            buttonLoader: false,
            backendError: null,
            // buttonError: false,


            // Model
            pixel_status: '1',
            pixel_id: '',
            pixel_name: '',
            pixel_type: '',

            // Methods
            _create(){
                var $this = this;
                $this.buttonLoader = true;
                $this.backendError = null;

                if(!$this.pixel_name || $this.pixel_name == '' || $this.pixel_id == '' || !$this.pixel_id){
                    $this.backendError = '{{ __('Name & Pixel id is required') }}';
                    $this.buttonLoader = false;
                    return;
                }

                let item = {
                    uuid: $this.$store.builder.generateUUID(),
                    name: $this.pixel_name,
                    status: $this.pixel_status,
                    pixel_id: $this.pixel_id,
                    pixel_type: $this.pixel_type
                };

                $this.pixels.push(item);

                // Do livewire stuff
                $this.$wire.createPixel(item).then(r => {
                    $this.buttonLoader = false;

                    // If error exists
                    if(r.status == 'error'){
                        $this.backendError = r.response;
                        return;
                    }

                    $this.pixel_status = '1';
                    $this.pixel_name = '';
                    $this.pixel_type = '';
                    $this.pixel_id = '';

                    // $this.domain = r.response;
                    $this.$dispatch('hide_tippy');
                });
            },

            _edit(item){
                var $this = this;

                // Do livewire stuff
                $this.$wire.editPixel(item).then(r => {
                    $this.buttonLoader = false;

                    // If error exists
                    if(r.status == 'error'){
                        $this.backendError = r.response;
                        return;
                    }

                    // $this.domain = r.response;
                    $this.$dispatch('hide_tippy');
                });
            },

            _delete_pixel(item){
                var $this = this;
                
                $this.pixels.forEach((pixel, index) => {
                    if(item.uuid == pixel.uuid){
                        $this.pixels.splice(index, 1);
                    }
                });

                // Do livewire stuff
                $this.$wire.deletePixel(item.uuid).then(r => {
                    
                });
            },

            init(){
               var $this = this;
               
               $this.$wire.getPixels().then(r => {
                $this.pixels = r;
               });
            }
         }
      });
     </script>
     @endscript
</div>