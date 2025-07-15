
<div>
   
   <main class="relative flex flex-col gap-3 bg-center bg-no-repeat bg-cover lg:bg-contain transition-all duration-300 [height:100dvh]" style="background-image:url({{ gs('assets/image/others/pattern.png') }})">
    <section class="lg:fixed top-0 flex items-center justify-between w-full py-6 px-6 lg:px-8 translate-y-0 transition-all duration-300">
        <div class="flex flex-row gap-3 px-0 relative z-10">
            <div class="flex flex-1">
                <a @click="page='-'" class="yena-button-o !h-10 !w-10 !rounded-full !bg-white">
                    <span class="--icon !m-0">
                        {!! __i('Arrows, Diagrams', 'Arrow.8', 'h-8') !!}
                    </span>
                </a>
            </div>
        </div>
       <div class="flex items-center justify-center gap-2 lg:gap-6 opacity-0 -translate-y-full transition-all duration-300 !opacity-100 !translate-y-0">
        <div class="flex items-center justify-center flex-row gap-2 w-full">
            <h2 class="leading-[1.33] font-semibold text-md md:text-lg lg:text-xl lg:leading-[1.2] text-white">{{ __('Template') }}</h2>
        </div>
       </div>
       <div class="w-6 lg:w-9"></div>
    </section>
    <section class="flex-1 flex flex-col items-center justify-end lg:justify-center text-gray-900">
       <div class="w-full max-w-[900px] h-full lg:h-auto bg-white rounded-t-3xl lg:rounded-2xl shadow-xl translate-y-0 transition-all duration-300">

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
                    
            <div class="w-full px-4 max-w-[var(--yena-sizes-5xl)] mx-auto [&_.dashboard-template-header]:!hidden">
                <livewire:components.console.templates.page lazy :key="uukey('sites', 'site-temples')"/>
            </div>
       </div>
    </section>
 </main>
</div>