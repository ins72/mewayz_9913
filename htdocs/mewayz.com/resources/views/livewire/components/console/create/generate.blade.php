
<div>
    <main class="relative flex flex-col gap-3 bg-center bg-no-repeat bg-cover lg:bg-contain transition-all duration-300 [height:100dvh]" style="background-image:url({{ gs('assets/image/others/pattern.png') }})">
        <section class="lg:fixed top-0 flex items-center justify-between w-full py-6 px-6 lg:px-8 translate-y-0 transition-all duration-300">
            <div class="flex flex-row gap-3 px-0 relative z-10">
                <div class="flex flex-1">
                    <a @click="page='-'" class="yena-button-o !h-10 !w-10 !rounded-full !bg-white">
                        <span class="--icon !m-0">
                            {!! __i('Arrows, Diagrams', 'Arrow.8', 'h-8') !!}
                        </span>
                        {{-- {{ __('Back') }} --}}
                    </a>
                </div>
            </div>
           <div class="flex items-center justify-center gap-2 lg:gap-6 opacity-0 -translate-y-full transition-all duration-300 !opacity-100 !translate-y-0">
              <div class="flex items-center gap-2">
                 <div class="flex items-center justify-center w-4 h-4 rounded-full transition-all duration-500" :class="{
                    'bg-white ring-4 ring-black ring-opacity-20': __page=='business' || __page=='language' ||  __page=='-',
                    'bg-black bg-opacity-20': __page=='-',
                 }">
                    <span :class="{
                        'flex': __page=='business' || __page=='language',
                        'hidden': __page=='-',
                    }" x-cloak>
                        <i class="ph ph-check text-xs"></i>
                    </span>
                    <div x-show="__page=='-'" x-cloak>
                        <span class="block w-1.5 h-1.5 bg-black rounded-full"></span>
                    </div>
                </div>
                 <div class="transition-all duration-300 text-white" :class="{
                    'font-semibold': __page=='-',
                    'hidden lg:block': __page!=='-',
                 }">{{ __('Industry') }}</div>
              </div>
              <div class="flex items-center gap-2">
                 <div class="w-4 lg:w-[4.5rem] h-0.5 lg:mr-4 rounded-lg transition-all duration-500 bg-black bg-opacity-20"></div>



                 <div class="flex items-center justify-center w-4 h-4 rounded-full transition-all duration-500" :class="{
                    'bg-white ring-4 ring-black ring-opacity-20': __page=='business' || __page=='language',
                    'bg-black bg-opacity-20': __page=='language' || __page=='-',
                 }">
                    <span :class="{
                        'flex': __page=='business',
                        'hidden': __page=='language' || __page=='-',
                    }" x-cloak>
                        <i class="ph ph-check text-xs"></i>
                    </span>
                    <div x-show="__page=='language' || __page=='-'" x-cloak>
                        <span class="block w-1.5 h-1.5 bg-black rounded-full"></span>
                    </div>
                </div>
                 <div class="transition-all duration-300 text-white" :class="{
                    'font-semibold': __page=='language',
                    'hidden lg:block': __page!=='language',
                 }">{{ __('Language') }}</div>
              </div>
              <div class="flex items-center gap-2">
                 <div class="w-4 lg:w-[4.5rem] h-0.5 lg:mr-4 rounded-lg transition-all duration-500 bg-black bg-opacity-20"></div>

                    <div class="flex items-center justify-center w-4 h-4 rounded-full transition-all duration-500" :class="{
                        'bg-white ring-4 ring-black ring-opacity-20': __page=='business',
                        'bg-black bg-opacity-20': __page!='business',
                    }">
                        <span class="block w-1.5 h-1.5 bg-black rounded-full" :class="{
                            'bg-black': __page=='business',
                            'bg-white': __page!='business',
                        }"></span>
                    </div>
                    <div class="transition-all duration-300 hidden lg:block text-white" :class="{
                        'font-semibold': __page=='business',
                        'hidden lg:block': __page!=='business',
                     }">{{ __('Business') }}</div>
              </div>
           </div>
           <div class="w-6 lg:w-9"></div>
        </section>
        <section class="flex-1 flex flex-col items-center justify-end lg:justify-center text-gray-900">
           <div class="w-full max-w-[480px] h-full lg:h-auto bg-white rounded-t-3xl lg:rounded-2xl shadow-xl translate-y-0 transition-all duration-300">

            <div class="relative w-full h-[180px] lg:h-[10rem] rounded-t-3xl lg:rounded-2xl overflow-hidden transition-all duration-500">
                <div class="absolute inset-0 z-20 bg-[radial-gradient(transparent_50%,_rgb(255,_255,_255)_90%),_linear-gradient(transparent_70%,_rgb(255,_255,_255)_90%)]"></div>
               <div class="absolute inset-0 w-full h-90 bg-indigo-50 animate-translate-blocks opacity-0 transition-opacity duration-500 z-5 !opacity-100">
                   <span class="block overflow-hidden [width:initial] [height:initial] bg-none opacity-100 border-[0] m-0 p-0 absolute top-[0] left-[0] bottom-[0] right-[0]">
                       <img alt=" " src="{{gs('assets/__icons/others/blocks.svg')}}" class="absolute top-[0] left-[0] bottom-[0] right-[0] box-border p-0 border-none m-auto block w-[0] h-[0] min-w-full max-w-full min-h-full max-h-full object-cover">
                   </span>
               </div>
               <div class="absolute top-4 opacity-0 left-1/2 w-56 h-80 -translate-x-1/2 transition-all translate-y-16 duration-300 z-10 !translate-y-0 !opacity-100">
                   <span class="block overflow-hidden [width:initial] [height:initial] bg-none opacity-100 border-[0] m-0 p-0 absolute top-[0] left-[0] bottom-[0] right-[0]">
                    <div class="w-full h-full flex bg-white p-3 rounded-xl shadow-xl">
                        <div class="w-[100%]">
                            <div class="flex flex-col py-2">
                                <div class="flex flex-col items-start">
                                    <div class="flex justify-between items-center w-[100%]">
                                        <div class="bg-[#cccccc] flex w-[100%] h-1 w-4 rounded-md"></div>
                                        <div class="flex items-center gap-2">
                                            <div class="bg-[#cccccc] flex h-2 w-4 rounded-md"></div>
                                            <div class="bg-[#cccccc] flex h-2 w-4 rounded-md"></div>
                                            <div class="bg-[#cccccc] flex h-2 w-4 rounded-md"></div>
                                        </div>
                                        <div class="bg-[#cccccc] flex w-[100%] h-2 w-4 rounded-md"></div>
                                    </div>
                                    <div class="w-full h-14 bg-[#cccccc] rounded-sm mt-[2px]"></div>
                                    <div class="w-8 h-[2px] bg-[#cccccc] rounded-full mt-[5px]"></div>
                                    
                                    <div class="grid grid-cols-3 gap-[2px] mt-1 w-[100%]">
                                        <div class="bg-[#cccccc] flex w-[100%] h-10 rounded-sm flex text-white items-center justify-center">
                                            
                                        </div>
                                        <div class="bg-[#cccccc] flex w-[100%] h-10 rounded-sm flex text-white items-center justify-center">
                                            
                                        </div>
                                        <div class="bg-[#cccccc] flex w-[100%] h-10 rounded-sm flex text-white items-center justify-center">
                                            
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2 mt-2 items-center w-full mb-1">
                                        <div class="bg-gray-300 w-20 h-[4px]"></div>
                                        <div class="bg-gray-300 w-16 h-[4px]"></div>
                                        <div class="bg-gray-300 w-12 h-[4px]"></div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-[6px] mt-1 w-[100%]">
                                        <div class="bg-gray-300 flex w-[100%] h-14 rounded-sm flex text-white items-center justify-center">
                                            
                                        </div>
                                        <div class="bg-gray-300 flex w-[100%] h-14 rounded-sm flex text-white items-center justify-center">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   </span>
               </div>
             </div>
              <form @submit.prevent="loading=true;
              setTimeout(function(){
                  $wire.createAi(aiContent).then(r => { loading=false; })
              }, 1000)" class="pb-6 lg:pb-8 px-6 lg:px-8">
                <div x-cloak x-show="__page=='business'">
                    <div class="">
                        <div class="opacity-100 h-6 mb-6">
                            <button class="ceramic link gap-1.5" type="button" @click="__page='language'">
                                <i class="ph ph-arrow-left text-lg"></i>
                                
                                <span class="text-body font-semibold">{{ __('Back') }}</span></button>
                        </div>
                        <div class="relative overflow-hidden transition-all duration-300 h-[4rem] lg:h-[2rem]">
                           <div class="text-2xl lg:text-xl leading-8 font-semibold transition-all duration-500 relative opacity-100 z-0">{{ __('What is the name of your business?') }}</div>
                       </div>
                       <div>
                            <div class="yena-form-group mt-3">
                                <input type="text" class="!px-[1rem] !rounded-lg !shadow-lg !h-[var(--yena-sizes-14)] md:!text-[var(--yena-fontSizes-lg)] md:!h-[var(--yena-sizes-16)] !bg-white" wire:model="name" placeholder="{{ __('Your business name') }}">
                            </div>
                            
                            <div class="yena-form-group mt-3" x-data="{scrollHeight:5}">
                                <textarea type="text" :style="{
                                    'height': scrollHeight + 'px'
                                }" @input="scrollHeight-0;scrollHeight=$event.target.scrollHeight" x-model="aiContent.textPrompt" placeholder="{{ __('Tell us more... (e.g., We offer digital marketing services for small businesses)') }}" class="!px-[1rem] !rounded-lg !shadow-lg md:!text-[var(--yena-fontSizes-lg)] focus:!shadow-lg bg-white w-[100%] resize-none min-h-[150px] max-h-[350px]"></textarea>
                            </div>
                       </div>
                       <div class="mt-5">
                           <div class="flex flex-col gap-[var(--yena-space-4)] w-full">
                               <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)]">
                                   <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                                   <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Try it out') }}</span>
                                   <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                               </div>
   
                               <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                   <template x-for="(item, index) in randomPrompt2" :key="index">
                                    <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-start !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm" type="button" @click="aiContent.textPrompt = item">
       
                                           <p class="text-[var(--yena-fontSizes-sm)] text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)] overflow-hidden overflow-ellipsis whitespace-normal break-words [--yena-line-clamp:2] md:[--yena-line-clamp:3] [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:var(--yena-line-clamp)] flex-1" x-text="item"></p>
       
                                           <span class=" ml-2 !mr-0 items-center">
                                               <i class="ph ph-plus text-sm text-[color:#0000003d]"></i>
                                           </span>
                                       </button>
                                   </template>
                               </div>
   
                               <div class="flex justify-start">
                                   <button class="ceramic link gap-1.5 !text-[.875rem]" type="button" @click="randomPrompt2 = $store.builder.getTwoRandomValues(randomPrompt)">
                                       {!! __icon('interface-essential', 'thunder-lightning-notifications', 'w-4 h-4 transition') !!}
                                      <span>{{ __('Regenerate') }}</span>
                                   </button>
                               </div>
                           </div>
                       </div>
                       <div>
                            @php
                            $error = false;
                
                            if(!$errors->isEmpty()){
                                    $error = $errors->first();
                            }

                            if(Session::get('_error_error')) $error = Session::get('_error_error');
                            @endphp
                            @if ($error)
                            <div class="mt-4 bg-red-200 text-[11px] p-1 px-2 rounded-md">
                                    <div class="flex items-center">
                                        <div>
                                        <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                        </div>
                                        <div class="flex-grow ml-1 text-xs">{{ $error }}</div>
                                    </div>
                            </div>
                        @endif
                          <div class="flex justify-end gap-2 mt-4">
                            <button class="btn btn-medium neutral !h-[calc(var(--unit)*_4)] !w-[100%] !flex items-center justify-center gap-2" type="submit" @click="__page='business'"><i class="ph ph-sparkle flex"></i> {{ __('Generate') }}</button>
                          </div>
                       </div>
                    </div>
                </div>
                <div x-cloak x-show="__page=='language'">
                    <div class="">
                        <div class="opacity-100 h-6 mb-6">
                            <button class="ceramic link gap-1.5" type="button" @click="__page='-'">
                                <i class="ph ph-arrow-left text-lg"></i>
                                
                                <span class="text-body font-semibold">{{ __('Back') }}</span></button>
                        </div>
                       <div class="relative overflow-hidden transition-all duration-300 h-[8rem] lg:h-[5.5rem]">
                           <div class="text-2xl lg:text-xl leading-8 font-semibold transition-all duration-500 relative opacity-100 z-0">{{ __('Choose your website language') }}</div>
                           <div class="opacity-100 translate-t-0"><p class="text-body leading-6 text-gray-500 mt-2">{{ __('Your website content will generate in the following language') }}</p></div>
                       </div>
                       <div class="relative mt-4 transition-all duration-500 !h-auto" x-tooltip="tippyTranslate">
                           <div class="w-full">
                               <div class="relative w-full">
                                   <div class="relative w-full">
                                    <div class="flex-shrink-0 absolute top-1/2 left-[18px] -translate-y-1/2 flex items-center justify-center w-5 h-5">
                                        <i class="ph ph-translate"></i>
                                    </div>
                                       <button type="button" class="ceramic-select !pl-11 !pr-20 lg:!px-11 capitalize" x-text="aiContent.textLanguage"></button>
                                       <div class="flex-shrink-0 absolute top-1/2 right-[18px] -translate-y-1/2 flex items-center justify-center w-5 h-5 text-gray-900 pointer-events-none">
                                           <i class="ph ph-caret-down text-lg"></i>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                       
                       <div>
                          <div class="flex justify-end gap-2 mt-4">
                            <button class="btn btn-medium neutral !h-[calc(var(--unit)*_4)] !w-[100%] !flex items-center justify-center gap-2" type="button" @click="__page='business'">{{ __('Next') }} <i class="ph ph-arrow-right flex"></i></button>
                          </div>
                       </div>
                    </div>
                </div>
                 <div class="" x-cloak x-show="__page=='-'">
                    <div class="relative overflow-hidden transition-all duration-300 h-[4rem] lg:h-[2rem]">
                        <div class="text-2xl lg:text-xl leading-8 font-semibold transition-all duration-500 relative opacity-100 z-0">{{ __('What type of business are you building?') }}</div>
                    </div>
                    <div class="relative mt-4 transition-all duration-500 !h-auto" x-tooltip="tippyCategory">
                        <div class="w-full">
                            <div class="relative w-full">
                                <div class="relative w-full">
                                    <button type="button" class="ceramic-select !pl-5 !pr-20 lg:!pr-11" x-text="aiContent.category"></button>
                                    <div class="flex-shrink-0 absolute top-1/2 right-[18px] -translate-y-1/2 flex items-center justify-center w-5 h-5 text-gray-900 pointer-events-none">
                                        <i class="ph ph-caret-down text-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <div class="mt-5">
                        <div class="flex flex-col gap-[var(--yena-space-4)] w-full">

                            <div class="flex justify-start">
                                <button class="ceramic link gap-1.5 !text-[.875rem]" type="button" @click="aiContent.category = $store.builder.selectRandomArray(generatePrompt)">
                                    {!! __icon('interface-essential', 'thunder-lightning-notifications', 'w-4 h-4 transition') !!}
                                   <span>{{ __('Random') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="relative mt-4 transition-all duration-500 !h-auto">
                        <div class="w-full">
                            <div class="relative w-full">
                                <div class="relative w-full">
                                    <div class="flex-shrink-0 absolute top-1/2 left-[18px] -translate-y-1/2 flex items-center justify-center w-5 h-5">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 512 512" width="24" height="24" class="icon h-5 w-5" aria-hidden="true">
                                            <g clip-path="url(#a)">
                                                <path fill="#F0F0F0" d="M256 512c141.385 0 256-114.615 256-256S397.385 0 256 0 0 114.615 0 256s114.615 256 256 256Z"></path>
                                                <path
                                                    fill="#D80027"
                                                    d="M244.87 256H512c0-23.106-3.08-45.49-8.819-66.783H244.87V256Zm0-133.565h229.556c-15.671-25.5721-35.708-48.1751-59.07-66.7831H244.87v66.7831ZM256 512c60.249 0 115.626-20.824 159.356-55.652H96.644C140.374 491.176 195.751 512 256 512ZM37.574 389.565h436.852c12.581-20.529 22.338-42.969 28.755-66.783H8.81897C15.236 346.596 24.993 369.036 37.574 389.565Z"
                                                ></path>
                                                <path
                                                    fill="#0052B4"
                                                    d="M118.584 39.978h23.329l-21.7 15.765 8.289 25.509-21.699-15.765-21.699 15.765 7.16-22.037C73.158 75.13 56.412 93.776 42.612 114.552h7.475l-13.813 10.035c-2.152 3.59-4.216 7.237-6.194 10.938l6.596 20.301-12.306-8.941c-3.059 6.481-5.857 13.108-8.372 19.873l7.267 22.368h26.822l-21.7 15.765 8.289 25.509-21.699-15.765-12.998 9.444C.678 234.537 0 245.189 0 256h256V0c-50.572 0-97.715 14.67-137.416 39.978Zm9.918 190.422-21.699-15.765L85.104 230.4l8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822l-21.7 15.765 8.289 25.509Zm-8.289-100.083 8.289 25.509-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822l-21.7 15.765ZM220.328 230.4l-21.699-15.765L176.93 230.4l8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822l-21.7 15.765 8.289 25.509Zm-8.289-100.083 8.289 25.509-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822l-21.7 15.765Zm0-74.574 8.289 25.509-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822l-21.7 15.765Z"
                                                ></path>
                                            </g>
                                            <defs>
                                                <clipPath id="a"><path fill="#fff" d="M0 0h512v512H0z"></path></clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <button class="ceramic-select !pl-11 !pr-20 lg:!px-11" id="headlessui-listbox-button-370" type="button" aria-haspopup="listbox" aria-expanded="false" data-headlessui-state="">English</button>
                                    <div class="flex-shrink-0 absolute top-1/2 right-[18px] -translate-y-1/2 flex items-center justify-center w-5 h-5 text-gray-900 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24" class="icon h-5 w-5" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m19 9-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    
                    <div>
                       <div class="flex justify-end gap-2 mt-4">
                        <button class="btn btn-medium neutral !h-[calc(var(--unit)*_4)] !w-[100%] !flex items-center justify-center gap-2" type="button" @click="__page='language'">{{ __('Next') }} <i class="ph ph-arrow-right flex"></i></button>
                       </div>
                    </div>
                    {{-- <div class="relative top-0 flex items-center justify-center gap-1 text-sm leading-4 h-6 mt-4 opacity-100 overflow-hidden transition-all duration-500">
                       <span class="font-medium">{{ __('Not sure?') }}</span>
                       <button class="ceramic link gap-1.5 !text-[.875rem]">
                          <span>{{ __('See some suggestions') }}</span>
                          {!! __icon('interface-essential', 'thunder-lightning-notifications', 'w-4 h-4 transition') !!}
                       </button>
                    </div> --}}
                 </div>
              </form>
           </div>
        </section>
     </main>
</div>