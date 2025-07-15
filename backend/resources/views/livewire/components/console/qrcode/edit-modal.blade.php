
<?php
   use App\Livewire\Actions\ToastUp;
   use App\Models\Qrcode;
   use function Livewire\Volt\{state, mount, placeholder, uses, rules, updated, usesFileUploads, on};

   usesFileUploads();

   on([
      'registerQr' => function($qr_id){
         $this->code = Qrcode::where('id', $qr_id)->where('user_id', iam()->id)->first();

         $this->generate();
      },
   ]);

   state([
      'code' => null,
      'qr' => null,
   ]);

   state([
      'background' => null,
      'logo' => null,
   ]);

   updated([
      'code' => function(){
         $this->code->save();

         $this->generate();

         $this->savedEvent();
      },
      'background' => function(){
         
        if(!empty($this->background)){
            $this->validate([
                'background' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5048',
            ]);
            storageDelete('media/qrcode/background', $this->code->background);


            $this->code->background = str_replace('media/qrcode/background/', "", $this->background->storePublicly('media/qrcode/background', sandy_filesystem('media/qrcode/background')));
            
            $this->background = '';
        }

        $this->code->save();
        $this->generate();
        $this->savedEvent();
      },
      'logo' => function(){
         
        if(!empty($this->logo)){
            $this->validate([
                'logo' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5048',
            ]);
            storageDelete('media/qrcode/logo', $this->code->logo);


            $this->code->logo = str_replace('media/qrcode/logo/', "", $this->logo->storePublicly('media/qrcode/logo', sandy_filesystem('media/qrcode/logo')));
            
            $this->logo = '';
        }

        $this->code->save();
        $this->generate();
        $this->savedEvent();
      },
   ]);

   rules(fn () => [
      'code.text' => '',
      'code.extra.pattern' => '',

      // Eye Frame
      'code.extra.eye_frame.shape' => '',
      'code.extra.eye_frame.color' => '',
      
      // Eye Ball
      'code.extra.eye_ball.shape' => '',
      'code.extra.eye_ball.color' => '',

      // Dots
      'code.extra.dots.color' => '',
      'code.extra.dots.type' => '',
      'code.extra.dots.radial' => '',
      'code.extra.dots.angle' => '',
      'code.extra.dots.color_1' => '',
      'code.extra.dots.color_2' => '',

      'code.extra.frames.enable' => '',
      'code.extra.frames.preset' => '',

      'code.extra.frames.mask' => '',

      'code.extra.logos.preset' => '',
      'code.extra.logos.enable' => '',
   ]);
   uses([ToastUp::class]);
   mount(function(){
      // $this->get();


      // $this->generate();
   });

   placeholder('
   <div class="p-5 w-full mt-1">
      <div class="flex mb-2 gap-4">
         <div>
            <div class="--placeholder-skeleton w-[200px] h-[200px] rounded-3xl"></div>
         </div>
         <div class="flex flex-col gap-2 w-full">
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-[150px] h-[40px] rounded-full mt-5"></div>
         </div>
      </div>
      
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');


   $_get = function(){
      $this->code = Qrcode::where('id', 1)->first();
   };

   $generate = function(){
      $this->qr = $this->code->generate();
   };

   $savedEvent = function(){
     
      $this->dispatch("qrUpdated.{$this->code->id}");
   };
   $removeBackground = function(){
      storageDelete('media/qrcode/background', $this->code->background);
      $this->code->background = null;
      $this->background = null;
      $this->code->save();
   }
?>


<div class="w-full">
   <div x-data="qrcode_editing">
      @if ($code)
      <div class="flex flex-col">
         <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
            <i class="fi fi-rr-cross text-sm"></i>
         </a>
   
         <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('QrCode') }}</header>
   
         <hr class="yena-divider">
         
   
         <div class="px-8 pt-2 pb-6">
            <div class="qrcode-img-wrapper !h-64 !w-full">
                <div class="qrcode-img-content h-full w-full relative right-0 left-0 top-0 bottom-0">
                    <div wire:loading.class="hidden" class="w-full h-full p-3" wire:target="code.extra">
                        {!! $qr !!}
                    </div>
                    
                    <div wire:loading="" wire:target="page.qr">
                        <img src="{{ gs('assets/image/others/qr-code-animation.gif') }}" alt="">
                    </div>
                </div>
            </div>
            
            <div class="flex items-center flex-row gap-2 mt-4">
               <div class="mb-[var(--yena-space-4)]">
                  <ul class="flex flex-wrap list-none gap-2 p-0">
                     <li class="flex items-start" wire:ignore>
                        <button class="yena-button-o" :class="{
                           '!bg-[var(--yena-colors-trueblue-50)] !text-[var(--yena-colors-trueblue-500)]': _page=='text',
                        }" type="button" @click="_page='text'">
                           <span class="-icon mr-2">
                              {!! __i('Content Edit', 'notes-pen', 'h-4 w-4') !!}
                           </span>
                           {{ __('Text') }}
                        </button>
                     </li>
                     <li class="flex items-start" wire:ignore>
                        <button class="yena-button-o" :class="{
                           '!bg-[var(--yena-colors-trueblue-50)] !text-[var(--yena-colors-trueblue-500)]': _page=='logo',
                        }" type="button" @click="_page='logo'">
                           <span class="-icon mr-2">
                              {!! __i('3d shapes', '3D, Shape, Object.15', 'h-4 w-4') !!}
                           </span>
                           {{ __('Logo') }}
                        </button>
                     </li>
                     <li class="flex items-start" wire:ignore>
                        <button class="yena-button-o" :class="{
                           '!bg-[var(--yena-colors-trueblue-50)] !text-[var(--yena-colors-trueblue-500)]': _page=='frame',
                        }" type="button" @click="_page='frame'">
                           <span class="-icon mr-2">
                              {!! __i('Type, Paragraph, Character', 'shape-mask-bottom', 'h-4 w-4') !!}
                           </span>
                           {{ __('Frame') }}
                        </button>
                     </li>
                     <li class="flex items-start" wire:ignore>
                        <button class="yena-button-o" :class="{
                           '!bg-[var(--yena-colors-trueblue-50)] !text-[var(--yena-colors-trueblue-500)]': _page=='eyes',
                        }" type="button" @click="_page='eyes'">
                           <span class="-icon mr-2">
                              {!! __i('interface-essential', 'eye-simple', 'h-4 w-4') !!}
                           </span>
                           {{ __('Eyes') }}
                        </button>
                     </li>
                     <li class="flex items-start" wire:ignore>
                        <button class="yena-button-o" :class="{
                           '!bg-[var(--yena-colors-trueblue-50)] !text-[var(--yena-colors-trueblue-500)]': _page=='dots',
                        }" type="button" @click="_page='dots'">
                           <span class="-icon mr-2">
                              {!! __i('interface-essential', 'border-clear', 'h-4 w-4') !!}
                           </span>
                           {{ __('Dots') }}
                        </button>
                     </li>
                  </ul>
               </div>
            </div>

            <div>
               <div x-show="_page == 'text'" x-cloak>
                  <div class="yena-form-group" x-data="{scrollHeight:5}">
                     <textarea type="text" :style="{
                         'height': scrollHeight + 'px'
                     }" @input="scrollHeight-0;scrollHeight=$event.target.scrollHeight" x-model.debounce.800ms="code.text" placeholder="{{ __('Qrcode text... (e.g., link, text, etc)') }}" class="!px-[1rem] !rounded-lg !shadow-lg md:!text-[var(--yena-fontSizes-lg)] focus:!shadow-lg bg-white w-[100%] resize-none min-h-[60px] max-h-[300px]" style="height: 5px;"></textarea>
                 </div>
               </div>
               <div x-show="_page=='logo'" x-cloak>
                  <div class="input-box !mt-0 !border border-solid border-[var(--c-mix-1)]">
                     <div class="input-group !border-0">
                        <div class="switchWrapper">
                           <input class="switchInput !border-0" id="codeLogoEnable" type="checkbox" x-model="code.extra.logos.enable">
  
                           <label for="codeLogoEnable" class="switchLabel">{{ __('Enable') }}</label>
                           <div class="slider"></div>
                        </div>
                     </div>
                  </div>
                  @php
                     $thumb = gs('media/qrcode/logo', $code->logo);
                     $ht = false;

                     if($logo){
                        $thumb = $logo->temporaryUrl();
                        $ht = true;
                     }else{
                        if(!empty($code->logo) && mediaExists('media/qrcode/logo', $code->logo)){
                           $ht = true;
                        }
                     }
                  @endphp
                  <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed {{ !$ht ? 'border-gray-200' : 'border-transparent' }} text-center hover:border-solid hover:border-yellow-600 relative">
                     @if ($ht)
                     <div class="group-hover:flex hidden w-full h-full items-center justify-center absolute right-0 top-0 left-0 bottom-0 z-50">
                           <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center icon-shadow" @click="$wire.removeBackground()">
                              <i class="fi fi-rr-trash"></i>
                           </div>
                     </div>
                     @endif
                     @if (!$ht)
                     <input type="file" wire:model="logo" class="opacity-0 h-full w-full absolute right-0 top-0 cursor-pointer">

                     <div class="w-full h-full flex items-center justify-center">
                        <div wire:loading wire:target="logo">
                           <span class="loader-line-dot-dot !text-black !text-[2px] -mt-2 m-0"></span>
                        </div>
                        <i class="fi fi-ss-plus" wire:loading.class="!hidden" wire:target="logo"></i>
                     </div>
                     @endif
                     @if ($ht)
                        <div class="h-full w-full">
                           <img src="{{ $thumb }}" class="h-full w-full !object-contain rounded-md" alt="">
                        </div>
                     @endif
                  </div>
                  

                  <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                     <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                     <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Or') }}</span>
                     <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                  </div>

                  <div class="flex flex-wrap gap-1">
                     <div class="">
                        <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-start !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm" type="button" @click="code.extra.logos.preset = ''" :class="{
                           'border-[var(--yena-colors-purple-400)]': code.extra.logos.preset == '',
                          }">
               
                           <p class="text-[var(--yena-fontSizes-sm)] text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)] overflow-hidden overflow-ellipsis whitespace-normal break-words [--yena-line-clamp:2] md:[--yena-line-clamp:3] [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:var(--yena-line-clamp)] flex-1">
                              <img src="{{ gs('assets/image/others', 'empty-state-image.png') }}" class="w-[38px] h-[38px] object-contain rounded-xl" alt="">
                           </p>
                        </button>
                     </div>
                     @foreach (Storage::disk('local')->listContents('assets/image/logos') as $key => $item)
                     <div class="">
                          <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-start !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm" type="button" @click="code.extra.logos.preset = '{{ basename(ao($item, 'path')) }}'" :class="{
                             'border-[var(--yena-colors-purple-400)]': code.extra.logos.preset == '{{ basename(ao($item, 'path')) }}',
                            }">
                 
                             <p class="text-[var(--yena-fontSizes-sm)] text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)] overflow-hidden overflow-ellipsis whitespace-normal break-words [--yena-line-clamp:2] md:[--yena-line-clamp:3] [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:var(--yena-line-clamp)] flex-1">
                                <img src="{{ url(ao($item, 'path')) }}" class="w-[38px] h-[38px] object-contain rounded-xl" alt="">
                             </p>
                          </button>
                     </div>
                     @endforeach
                  </div>
               </div>
               <div x-show="_page=='frame'" x-cloak>

                  <div class="input-box !mt-0 !border border-solid border-[var(--c-mix-1)]">
                     <div class="input-group !border-0">
                        <div class="switchWrapper">
                           <input class="switchInput !border-0" id="codeBgEnable" type="checkbox" x-model="code.extra.frames.enable">
  
                           <label for="codeBgEnable" class="switchLabel">{{ __('Enable') }}</label>
                           <div class="slider"></div>
                        </div>
                     </div>
                  </div>

                  <div class="input-box !mt-0 !border border-solid border-[var(--c-mix-1)]">
                     <div class="input-group !border-0">
                        <div class="switchWrapper">
                           <input class="switchInput !border-0" id="codeBgMask" type="checkbox" x-model="code.extra.frames.mask">
  
                           <label for="codeBgMask" class="switchLabel">{{ __('Mask') }}</label>
                           <div class="slider"></div>
                        </div>
                     </div>
                  </div>
                  @php
                     $thumb = gs('media/qrcode/background', $code->background);
                     $ht = false;

                     if($background){
                           $thumb = $background->temporaryUrl();
                           $ht = true;
                     }else{
                           if(!empty($code->background) && mediaExists('media/qrcode/background', $code->background)){
                              $ht = true;
                           }
                     }
                  @endphp
                  <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed {{ !$ht ? 'border-gray-200' : 'border-transparent' }} text-center hover:border-solid hover:border-yellow-600 relative">
                     @if ($ht)
                     <div class="group-hover:flex hidden w-full h-full items-center justify-center absolute right-0 top-0 left-0 bottom-0 z-50">
                           <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center icon-shadow" @click="$wire.removeBackground()">
                              <i class="fi fi-rr-trash"></i>
                           </div>
                     </div>
                     @endif
                     @if (!$ht)
                     <input type="file" wire:model="background" class="opacity-0 h-full w-full absolute right-0 top-0 cursor-pointer">

                     <div class="w-full h-full flex items-center justify-center">
                        <div wire:loading wire:target="background">
                           <span class="loader-line-dot-dot !text-black !text-[2px] -mt-2 m-0"></span>
                        </div>
                        <i class="fi fi-ss-plus" wire:loading.class="!hidden" wire:target="background"></i>
                     </div>
                     @endif
                     @if ($ht)
                           <div class="h-full w-full">
                              <img src="{{ $thumb }}" class="h-full w-full object-cover rounded-md" alt="">
                           </div>
                     @endif
                  </div>

                  <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                     <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                     <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Or') }}</span>
                     <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                  </div>
                  <div class="grid grid-cols-3 lg:grid-cols-3 gap-2">
                      <div class="h-full">
                        <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-start !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !w-[100%]" type="button" @click="code.extra.frames.preset = ''" :class="{
                           'border-[var(--yena-colors-purple-400)]': code.extra.frames.preset == '',
                          }">
               
                           <p class="text-[var(--yena-fontSizes-sm)] text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)] overflow-hidden overflow-ellipsis whitespace-normal break-words [--yena-line-clamp:2] md:[--yena-line-clamp:3] [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:var(--yena-line-clamp)] flex-1">
                              <img src="{{ gs('assets/image/others', 'empty-state-image.png') }}" class="w-full h-full object-contain rounded-xl" alt="">
                           </p>
                        </button>
                      </div>
                      @foreach (Storage::disk('local')->listContents('assets/image/others/qrframes') as $key => $item)
                      <div class="">
                           <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-start !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm" type="button" @click="code.extra.frames.preset = '{{ basename(ao($item, 'path')) }}'" :class="{
                              'border-[var(--yena-colors-purple-400)]': code.extra.frames.preset == '{{ basename(ao($item, 'path')) }}',
                             }">
                  
                              <p class="text-[var(--yena-fontSizes-sm)] text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)] overflow-hidden overflow-ellipsis whitespace-normal break-words [--yena-line-clamp:2] md:[--yena-line-clamp:3] [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:var(--yena-line-clamp)] flex-1">
                                 <img src="{{ url(ao($item, 'path')) }}" class="w-full h-full object-contain rounded-xl" alt="">
                              </p>
                           </button>
                      </div>
                      @endforeach
                  </div>
               </div>
               <div x-show="_page == 'eyes'" x-cloak>
                   <div x-data="{eyeBall: false}">
                       <div class="eyes-tab mb-5">
                           <div class="tab-wrapper">
                               <div class="tab-item" @click="eyeBall=false" :class="{'active': !eyeBall}">{{ __('Eye Frame') }}</div>
                               <div class="tab-item" @click="eyeBall=true" :class="{'active': eyeBall}">{{ __('Eye Ball') }}</div>
                           </div>
                       </div>
   
                       <div x-cloak x-show="!eyeBall">
                           <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)]">
                              <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                              <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Shape') }}</span>
                              <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                           </div>
                           <div class="flex flex-wrap gap-2">
                               @foreach (config('qrcode.markers') as $key => $item)
                               <div class="">
                                      <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-start !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm" type="button" @click="code.extra.eye_frame.shape = '{{ $key }}'" :class="{
                                       'border-[var(--yena-colors-purple-400)]': code.extra.eye_frame.shape == '{{ $key }}',
                                      }">
                             
                                         <p class="text-[var(--yena-fontSizes-sm)] text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)] overflow-hidden overflow-ellipsis whitespace-normal break-words [--yena-line-clamp:2] md:[--yena-line-clamp:3] [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:var(--yena-line-clamp)] flex-1">
                                            
                                          <svg width="38" height="38" class="stroke-current text-black stroke-0" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">{!! ao($item, 'path') !!}</svg>
                                         </p>
                                   </button>
                               </div>
                               @endforeach
                           </div>
                           <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                              <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                              <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Color') }}</span>
                              <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                           </div>
   
                           <div wire:ignore>
                              <div class="colors-container">
                                 <div class="input-box !pb-1">
                                    <div class="input-group">
                                       <div class="color-selector">
                                         <template x-for="item in colors" :key="item">
                                             <div class="color-box mod" @click="code.extra.eye_frame.color=item" :style="{
                                                '--c': item,
                                             }"><span :style="{
                                                '--c': item,
                                                'background': item,
                                             }"></span></div>
                                         </template>
                                       </div>
                                       <div class="custom-color !block">
                                          <form onsubmit="return false;">
                                             <div class="input-box !pb-0">
                                                <div class="input-group">
                                                   <input type="color" class="input-small input-color" x-model.debounce.800ms="code.extra.eye_frame.color" :style="{
                                                      'background-color': code.extra.eye_frame.color,
                                                      'color': $store.builder.getContrastColor(code.extra.eye_frame.color)
                                                      }" maxlength="6">
                                                      
                                                   <span class="color-generator" :style="{
                                                      'background-color': code.extra.eye_frame.color,
                                                      'color': $store.builder.getContrastColor(code.extra.eye_frame.color)
                                                      }"></span>
                                                </div>
                                             </div>
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                                             
                                 <div class="flex mb-1">
                                    <div class="menu--icon !bg-black !text-[10px] !text-white !w-auto !px-2 !h-5 !cursor-pointer !flex !items-center !justify-center !ml-auto !gap-1 !rounded-full" @click="code.extra.eye_frame.color=$store.builder.getRandomHexColor()">
                                       <i class="fi fi-br-dice-alt"></i>
                                       {{ __('Random') }}
                                    </div>
                                 </div>
                              </div>
                           </div>
                       </div>
   
                       <div x-cloak x-show="eyeBall">
                           <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)]">
                              <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                              <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Shape') }}</span>
                              <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                           </div>
                           <div class="flex flex-wrap gap-2">
                               @foreach (config('qrcode.markersIn') as $key => $item)
                               @php
                                   if(!ao($item, 'marker')) continue;
                               @endphp
                               <div class="">
                                      <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-start !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm" type="button" @click="code.extra.eye_ball.shape = '{{ $key }}'" :class="{
                                       'border-[var(--yena-colors-purple-400)]': code.extra.eye_ball.shape == '{{ $key }}',
                                      }">
                             
                                         <p class="text-[var(--yena-fontSizes-sm)] text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)] overflow-hidden overflow-ellipsis whitespace-normal break-words [--yena-line-clamp:2] md:[--yena-line-clamp:3] [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:var(--yena-line-clamp)] flex-1">
                                            
                                             <svg width="38" height="38" class="stroke-current text-black stroke-0" viewBox="0 0 6 6" xmlns="http://www.w3.org/2000/svg" fill="currentColor">{!! ao($item, 'path') !!}</svg>
                                         </p>
                                   </button>
                               </div>
                               @endforeach
                           </div>
                           <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)]">
                              <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                              <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Color') }}</span>
                              <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                           </div>
                           
                           <div wire:ignore>
                              <div class="colors-container">
                                 <div class="input-box !pb-1">
                                    <div class="input-group">
                                       <div class="color-selector">
                                         <template x-for="item in colors" :key="item">
                                             <div class="color-box mod" @click="code.extra.eye_ball.color=item" :style="{
                                                '--c': item,
                                             }"><span :style="{
                                                '--c': item,
                                                'background': item,
                                             }"></span></div>
                                         </template>
                                       </div>
                                       <div class="custom-color !block">
                                          <form onsubmit="return false;">
                                             <div class="input-box !pb-0">
                                                <div class="input-group">
                                                   <input type="color" class="input-small input-color" x-model.debounce.800ms="code.extra.eye_ball.color" :style="{
                                                      'background-color': code.extra.eye_ball.color,
                                                      'color': $store.builder.getContrastColor(code.extra.eye_ball.color)
                                                      }" maxlength="6">
                                                      
                                                   <span class="color-generator" :style="{
                                                      'background-color': code.extra.eye_ball.color,
                                                      'color': $store.builder.getContrastColor(code.extra.eye_ball.color)
                                                      }"></span>
                                                </div>
                                             </div>
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                                             
                                 <div class="flex mb-1">
                                    <div class="menu--icon !bg-black !text-[10px] !text-white !w-auto !px-2 !h-5 !cursor-pointer !flex !items-center !justify-center !ml-auto !gap-1 !rounded-full" @click="code.extra.eye_ball.color=$store.builder.getRandomHexColor()">
                                       <i class="fi fi-br-dice-alt"></i>
                                       {{ __('Random') }}
                                    </div>
                                 </div>
                              </div>
                           </div>
                       </div>
                   </div>
               </div>

               <div x-show="_page=='dots'" x-cloak>
                  
                  <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)]">
                     <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                     <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Pattern') }}</span>
                     <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                  </div>
               
                  <div class="flex flex-wrap gap-2">
                    @foreach (config('qrcode.markersIn') as $key => $item)
                    @php
                        if(!ao($item, 'preview')) continue;
                    @endphp
                    <div class="">
                           <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-start !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm" type="button" @click="code.extra.pattern = '{{ $key }}'" :class="{
                              'border-[var(--yena-colors-purple-400)]': code.extra.pattern == '{{ $key }}',
                             }">
                  
                              <p class="text-[var(--yena-fontSizes-sm)] text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)] overflow-hidden overflow-ellipsis whitespace-normal break-words [--yena-line-clamp:2] md:[--yena-line-clamp:3] [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:var(--yena-line-clamp)] flex-1">
                                 
                                 <svg width="38" height="38" class="stroke-current text-black stroke-0" viewBox="0 0 6 6" xmlns="http://www.w3.org/2000/svg">{!! ao($item, 'preview') !!}</svg>
                              </p>

                              {{-- <span class=" ml-2 !mr-0 items-center">
                                 <i class="ph ph-plus text-sm text-[color:#0000003d]"></i>
                              </span> --}}
                        </button>
                    </div>
                    @endforeach
                  </div>
                        
                  <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                        <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                        <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Colors') }}</span>
                        <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                  </div>

                     
                  <div>
                     <div class="eyes-tab mb-5">
                        <div class="tab-wrapper">
                           <div class="tab-item" @click="code.extra.dots.type=false" :class="{'active': !code.extra.dots.type}">{{ __('Color') }}</div>
                           <div class="tab-item" @click="code.extra.dots.type=true" :class="{'active': code.extra.dots.type}">{{ __('Gradient') }}</div>
                        </div>
                     </div>

                     <div x-cloak x-show="!code.extra.dots.type">
                        <div>
                           <div wire:ignore>
                              <div class="colors-container">
                                 <div class="input-box !pb-1">
                                    <div class="input-group">
                                       <div class="color-selector">
                                         <template x-for="item in colors" :key="item">
                                             <div class="color-box mod" @click="code.extra.dots.color=item" :style="{
                                                '--c': item,
                                             }"><span :style="{
                                                '--c': item,
                                                'background': item,
                                             }"></span></div>
                                         </template>
                                       </div>
                                       <div class="custom-color !block">
                                          <form onsubmit="return false;">
                                             <div class="input-box !pb-0">
                                                <div class="input-group">
                                                   <input type="color" class="input-small input-color" x-model.debounce.800ms="code.extra.dots.color" :style="{
                                                      'background-color': code.extra.dots.color,
                                                      'color': $store.builder.getContrastColor(code.extra.dots.color)
                                                      }" maxlength="6">
                                                      
                                                   <span class="color-generator" :style="{
                                                      'background-color': code.extra.dots.color,
                                                      'color': $store.builder.getContrastColor(code.extra.dots.color)
                                                      }"></span>
                                                </div>
                                             </div>
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                                             
                                 <div class="flex mb-1">
                                    <div class="menu--icon !bg-black !text-[10px] !text-white !w-auto !px-2 !h-5 !cursor-pointer !flex !items-center !justify-center !ml-auto !gap-1 !rounded-full" @click="code.extra.dots.color=$store.builder.getRandomHexColor()">
                                       <i class="fi fi-br-dice-alt"></i>
                                       {{ __('Random') }}
                                    </div>
                                 </div>
                              </div>
                           </div>
                           
                        </div>
                     </div>

                     <div x-cloak x-show="code.extra.dots.type" wire:ignore>
                        
                        <div>
                           <div wire:ignore>
                              <div class="colors-container">
                                 <div class="input-box !pb-1">
                                    <div class="input-group">
                                       <div class="color-selector !h-[5rem]" :style="`background: ${code.extra.dots.radial ? 'radial' : 'linear'}-gradient(${code.extra.dots.color_1}, ${code.extra.dots.color_2})`;"></div>
                                       <div class="custom-color !block">
                                          <form onsubmit="return false;">
                                             <div class="input-box !pb-0">
                                                <div class="p-1 flex items-center gap-2">
                                                   <div class="input-group">
                                                      <input type="color" class="input-small input-color !rounded-lg" x-model.debounce.200ms="code.extra.dots.color_1" :style="{
                                                         'background-color': code.extra.dots.color_1,
                                                         }" maxlength="6">
                                                   </div>
                                                   <div class="input-group">
                                                      <input type="color" class="input-small input-color !rounded-lg" x-model.debounce.800ms="code.extra.dots.color_2" :style="{
                                                         'background-color': code.extra.dots.color_2,
                                                         }" maxlength="6">

                                                   </div>
                                                </div>
                                             </div>
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                                 
                              </div>
                           </div>
                           
                           <div class="flex items-center justify-between mt-5">
                                 <div class="line-title-g text-xs w-full m-0">
                                    <span>{{ __('Radial Gradient') }}</span>
                                    <div class="-line"></div>
                                 </div>
                                 <label class="sandy-switch">
                                    <input type="checkbox" class="sandy-switch-input" x-model="code.extra.dots.radial" value="1">
                                    <span class="sandy-switch-in"><span class="sandy-switch-box is-white"></span></span>
                                 </label>
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
               <div class="mt-5 bg-red-200 font--11 p-1 px-2 rounded-md">
                     <div class="flex items-center">
                        <div>
                           <i class="fi fi-rr-cross-circle flex text-xs"></i>
                        </div>
                        <div class="flex-grow ml-1 text-xs">{{ str_replace('create.', '', $error) }}</div>
                     </div>
               </div>
            @endif
         </div>
      </div>
      @endif
   </div>
      

   @script
   <script>
       Alpine.data('qrcode_editing', () => {
          return {
            _page: 'text',
            code: @entangle('code').live,
            colors: [],

            _render(){
               if(!this.code) return;
               const items = ['eye_ball', 'eye_frame', 'dots', 'frames', 'logos'];

               items.forEach(item => {
                  if (!this.code.extra[item]) {
                     this.code.extra[item] = {
                        silence: 'golden'
                     };
                  }
               });
               for (let i = 1; i <= 8; i++) {
                  this.colors.push(this.$store.builder.getRandomHexColor());
               }
            },
            
            randomHex(){
               let $hex = this.$store.builder.getRandomHexColor();

               // $hex = $hex.replace(new RegExp('#', 'g'), '');
               site._button.color = $hex;
            },
            _color(color){
               return color.replace(/#/g, '');
            },
            init(){
               var $this = this;

                window.addEventListener('registerQr', (e) => {
                  $this._render();
                });
                // document.addEventListener('alpine:navigated', (e) => {
                //    $this.$wire._get();
                // });
            }
          }
       });
   </script>
   @endscript
</div>