
<div>
    <div class="flex flex-row gap-3 px-4 relative z-10 w-full">
        <div class="flex flex-1">
            <a href="{{ route('dashboard-index') }}" @navigate class="yena-button-o !bg-white">
                <span class="--icon">
                    {!! __i('--ie', 'home-house-line', 'h-[1em]') !!}
                </span>
                {{ __('Home') }}
            </a>
        </div>
    </div>

    <div class="flex flex-col gap-0 content-center relative z-0 pt-[var(--yena-space-9)] pb-[var(--yena-space-9)] w-full md:pt-[8vh] md:pb-[8vh]">

        <div class="flex flex-col gap-[var(--yena-space-2)] mb-[var(--yena-space-5)] w-full md:gap-4 md:mb-8">
            <h2 class="text-center zztext-[var(--yena-colors-gray-800)] font-semibold w-full md:text-5xl md:leading-none text-3xl leading-[1.33] text-white">{{ __('Create with AI') }}</h2>
            
            <h2 class="text-center font-medium zztext-[var(--yena-colors-blackAlpha-700)] md:text-xl md:leading-[1.2] text-white">{{ __('How would you like to get started?') }}</h2>
        </div>
{{-- 
        <svg class="sp-genie-anime-svg" width="19" height="22" viewBox="0 0 19 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.6831 3.81352C11.0845 2.72883 12.6187 2.72883 13.02 3.81352L14.1765 6.93894C14.3027 7.27996 14.5716 7.54884 14.9126 7.67503L18.038 8.83154C19.1227 9.23291 19.1227 10.7671 18.038 11.1685L14.9126 12.325C14.5716 12.4512 14.3027 12.72 14.1765 13.0611L13.02 16.1865C12.6187 17.2712 11.0845 17.2712 10.6831 16.1865L9.52659 13.0611C9.4004 12.72 9.13152 12.4512 8.7905 12.325L5.66509 11.1685C4.58039 10.7671 4.58039 9.23291 5.66509 8.83154L8.7905 7.67503C9.13152 7.54884 9.4004 7.27996 9.52659 6.93894L10.6831 3.81352Z" fill="#FDFDFB" fill-opacity="0.9"></path><path d="M3.06269 14.9098C3.26327 14.3678 4.02996 14.3678 4.23054 14.9098L4.8085 16.4717C4.87156 16.6422 5.00593 16.7765 5.17635 16.8396L6.73824 17.4175C7.28031 17.6181 7.28031 18.3848 6.73824 18.5854L5.17635 19.1634C5.00593 19.2264 4.87156 19.3608 4.8085 19.5312L4.23054 21.0931C4.02996 21.6352 3.26327 21.6352 3.06269 21.0931L2.48474 19.5312C2.42167 19.3608 2.2873 19.2264 2.11688 19.1634L0.554987 18.5854C0.0129209 18.3848 0.0129209 17.6181 0.554987 17.4175L2.11688 16.8396C2.2873 16.7765 2.42167 16.6422 2.48474 16.4717L3.06269 14.9098Z" fill="white"></path><path d="M2.47984 0.825606C2.6403 0.391953 3.25366 0.391953 3.41412 0.825606L3.87648 2.07512C3.92693 2.21146 4.03443 2.31895 4.17077 2.3694L5.42028 2.83177C5.85394 2.99223 5.85394 3.60558 5.42028 3.76605L4.17077 4.22841C4.03443 4.27886 3.92693 4.38636 3.87648 4.5227L3.41412 5.77221C3.25366 6.20586 2.6403 6.20586 2.47984 5.77221L2.01748 4.5227C1.96703 4.38636 1.85953 4.27886 1.72319 4.22841L0.473677 3.76605C0.0400242 3.60558 0.0400242 2.99223 0.473677 2.83177L1.72319 2.3694C1.85953 2.31895 1.96703 2.21146 2.01748 2.07512L2.47984 0.825606Z" fill="white"></path></svg> --}}

        <div class="flex w-full justify-center">
            <div class="w-full px-4 max-w-[var(--yena-sizes-4xl)]">

                <div class="flex items-center flex-col gap-12">
                    <div class="ai-items">
                        <div class="ai-item [grid-area:paste] -left cursor-pointer" @click="page='blank'">
                            <a class="-item-inner">
                                <div class="-item-stack">
                                    <div class="-item-header !bg-white !text-black">
                                        <div class="">
                                            <div class="absolute inset-0 z-20 bg-[radial-gradient(transparent_50%,_rgb(196,_182,_182)_90%),_linear-gradient(transparent_70%,_rgb(255,_255,_255)_90%)]"></div>
                                           <div class="absolute top-0 opacity-0 left-0 w-full h-full transition-all duration-300 z-10 !opacity-100 flex items-center justify-center">
                                            {!! __i('Files', 'file-blank-edit-pen', 'w-8 h-8') !!}
                                           </div>
                                         </div>
                                    </div>
                                    <div class="flex items-stretch justify-between flex-row gap-[var(--yena-space-1-5)] p-4 max-w-full min-h-[100px] flex-[1.5_1_0%] relative h-full md:flex-col">
                                        <div class="flex flex-col gap-2">
                                            <h2 class="leading-[1.33] text-xl font-semibold">{{ __('Blank') }}</h2>

                                            <div class="flex items-center flex-row gap-2">
                                                <div class="text-sm md:text-base font-medium leading-[1.4] text-[var(--yena-colors-gray-600)]">{{ __('Start with a pre-generated blank site with random content.') }}</div>
                                            </div>
                                        </div>


                                        <div class="flex items-start justify-end flex-row gap-2 max-w-full text-sm p-1 sm:text-base md:p-0">
                                            <p class="hidden [transition:all_300ms_ease_0s] opacity-0 md:block continue-text">{{ __('Continue') }}</p>
                                            <div>
                                                {!! __i('Arrows, Diagrams', 'Arrow', 'h-6') !!}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="ai-item [grid-area:generate] -center relative" @click="{{ __o_feature('feature.ai_site_generator') ? 'page=\'generate\'' : '$dispatch(\'open-modal\', \'upgrade-modal\')' }}">
                            @if (!__o_feature('feature.ai_site_generator'))
                                <span class="beta-tag z-[99] top-0 absolute !right-0 ![bottom:initial] !transform !rotate-[20deg] !text-xs">{{ __('Upgrade') }}</span>
                            @endif
                            <a class="-item-inner">
                                <div class="-item-stack">
                                    <div class="-item-header">
                                        <div class="">
                                            <div class="absolute inset-0 z-20 bg-[radial-gradient(transparent_50%,_rgb(255,_255,_255)_90%),_linear-gradient(transparent_70%,_rgb(255,_255,_255)_90%)]"></div>
                                           <div class="absolute inset-0 w-full h-full bg-indigo-50 animate-translate-blocks opacity-0 transition-opacity duration-500 z-5 !opacity-100">
                                               <span class="block overflow-hidden [width:initial] [height:initial] bg-none opacity-100 border-[0] m-0 p-0 absolute top-[0] left-[0] bottom-[0] right-[0]">
                                                   <img alt=" " src="{{gs('assets/__icons/others/blocks.svg')}}" class="absolute top-[0] left-[0] bottom-[0] right-[0] box-border p-0 border-none m-auto block w-[0] h-[0] min-w-full max-w-full min-h-full max-h-full object-cover">
                                               </span>
                                           </div>
                                           <div class="absolute top-0 opacity-0 left-0 w-full h-full transition-all duration-300 z-10 !opacity-100 flex items-center justify-center">
                                            <svg class="sp-genie-anime-svg" width="19" height="22" viewBox="0 0 19 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.6831 3.81352C11.0845 2.72883 12.6187 2.72883 13.02 3.81352L14.1765 6.93894C14.3027 7.27996 14.5716 7.54884 14.9126 7.67503L18.038 8.83154C19.1227 9.23291 19.1227 10.7671 18.038 11.1685L14.9126 12.325C14.5716 12.4512 14.3027 12.72 14.1765 13.0611L13.02 16.1865C12.6187 17.2712 11.0845 17.2712 10.6831 16.1865L9.52659 13.0611C9.4004 12.72 9.13152 12.4512 8.7905 12.325L5.66509 11.1685C4.58039 10.7671 4.58039 9.23291 5.66509 8.83154L8.7905 7.67503C9.13152 7.54884 9.4004 7.27996 9.52659 6.93894L10.6831 3.81352Z" fill="#FDFDFB" fill-opacity="0.9"></path><path d="M3.06269 14.9098C3.26327 14.3678 4.02996 14.3678 4.23054 14.9098L4.8085 16.4717C4.87156 16.6422 5.00593 16.7765 5.17635 16.8396L6.73824 17.4175C7.28031 17.6181 7.28031 18.3848 6.73824 18.5854L5.17635 19.1634C5.00593 19.2264 4.87156 19.3608 4.8085 19.5312L4.23054 21.0931C4.02996 21.6352 3.26327 21.6352 3.06269 21.0931L2.48474 19.5312C2.42167 19.3608 2.2873 19.2264 2.11688 19.1634L0.554987 18.5854C0.0129209 18.3848 0.0129209 17.6181 0.554987 17.4175L2.11688 16.8396C2.2873 16.7765 2.42167 16.6422 2.48474 16.4717L3.06269 14.9098Z" fill="white"></path><path d="M2.47984 0.825606C2.6403 0.391953 3.25366 0.391953 3.41412 0.825606L3.87648 2.07512C3.92693 2.21146 4.03443 2.31895 4.17077 2.3694L5.42028 2.83177C5.85394 2.99223 5.85394 3.60558 5.42028 3.76605L4.17077 4.22841C4.03443 4.27886 3.92693 4.38636 3.87648 4.5227L3.41412 5.77221C3.25366 6.20586 2.6403 6.20586 2.47984 5.77221L2.01748 4.5227C1.96703 4.38636 1.85953 4.27886 1.72319 4.22841L0.473677 3.76605C0.0400242 3.60558 0.0400242 2.99223 0.473677 2.83177L1.72319 2.3694C1.85953 2.31895 1.96703 2.21146 2.01748 2.07512L2.47984 0.825606Z" fill="white"></path></svg>
                                           </div>
                                         </div>
                                    </div>

                                    <div class="flex items-stretch justify-between flex-row gap-[var(--yena-space-1-5)] p-4 max-w-full min-h-[100px] flex-[1.5_1_0%] relative h-full md:flex-col">
                                        <div class="flex flex-col gap-2">
                                            <h2 class="leading-[1.33] text-xl font-semibold">{{ __('Create with AI') }}</h2>

                                            <div class="flex items-center flex-row gap-2">
                                                <div class="text-sm md:text-base font-medium leading-[1.4] text-[var(--yena-colors-gray-600)]">{{ __('Let Ai help you with generating content and launch faster!') }}</div>
                                            </div>
                                        </div>


                                        <div class="flex items-start justify-end flex-row gap-2 max-w-full text-sm p-1 sm:text-base md:p-0">
                                            <p class="hidden [transition:all_300ms_ease_0s] opacity-0 md:block continue-text">{{ __('Continue') }}</p>
                                            <div>
                                                {!! __i('Arrows, Diagrams', 'Arrow', 'h-6') !!}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="ai-item [grid-area:advance] -right" @click="page='template'">
                            <a class="-item-inner">
                                <div class="-item-stack">
                                    <div class="-item-header">
                                        <div class="">
                                            <div class="absolute inset-0 z-20 bg-[radial-gradient(transparent_50%,_rgb(255,_255,_255)_90%),_linear-gradient(transparent_70%,_rgb(255,_255,_255)_90%)]"></div>
                                           {{-- <div class="absolute inset-0 w-full h-full bg-indigo-50 animate-translate-blocks opacity-0 transition-opacity duration-500 z-5 !opacity-100">
                                               <span class="block overflow-hidden [width:initial] [height:initial] bg-none opacity-100 border-[0] m-0 p-0 absolute top-[0] left-[0] bottom-[0] right-[0]">
                                                   <img alt=" " src="{{gs('assets/__icons/others/blocks.svg')}}" class="absolute top-[0] left-[0] bottom-[0] right-[0] box-border p-0 border-none m-auto block w-[0] h-[0] min-w-full max-w-full min-h-full max-h-full object-cover">
                                               </span>
                                           </div> --}}
                                           <div class="absolute top-0 opacity-0 left-0 w-full h-full transition-all duration-300 z-10 !opacity-100 flex items-center justify-center">
                                                {!! __i('interface-essential', 'thunder-lightning-notifications', 'w-8 h-8') !!}
                                           </div>
                                         </div>
                                    </div>

                                    <div class="flex items-stretch justify-between flex-row gap-[var(--yena-space-1-5)] p-4 max-w-full min-h-[100px] flex-[1.5_1_0%] relative h-full md:flex-col">
                                        <div class="flex flex-col gap-2">
                                            <h2 class="leading-[1.33] text-xl font-semibold">{{ __('Template') }}</h2>

                                            <div class="flex items-center flex-row gap-2">
                                                <div class="text-sm md:text-base font-medium leading-[1.4] text-[var(--yena-colors-gray-600)]">{{ __('Start from our list of templates') }}</div>
                                            </div>
                                        </div>

                                        <div class="flex items-start justify-end flex-row gap-2 max-w-full text-sm p-1 sm:text-base md:p-0">
                                            <p class="hidden [transition:all_300ms_ease_0s] opacity-0 md:block continue-text">{{ __('Continue') }}</p>
                                            <div>
                                                {!! __i('Arrows, Diagrams', 'Arrow', 'h-6') !!}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>