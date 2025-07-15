
<div>
    <div class="flex flex-row gap-3 px-4 relative z-10 w-full">

        <div class="flex flex-1">
            <a @click="page='-'" class="yena-button-o !bg-white">
                <span class="--icon">
                    {!! __i('Arrows, Diagrams', 'Arrow.8', 'h-5') !!}
                </span>
                {{ __('Back') }}
            </a>
        </div>

        <div class="flex items-center justify-center flex-[3_1_0%]">
            <div class="flex items-center justify-center flex-row gap-2 w-full">
                <h2 class="leading-[1.33] font-semibold text-md md:text-lg lg:text-xl lg:leading-[1.2]">{{ __('Prompt editor') }}</h2>
            </div>
        </div>

        <div class="flex flex-1 justify-end items-center"></div>
    </div>

    <div class="flex flex-col gap-0 content-center relative z-0 pt-[var(--yena-space-9)] pb-[var(--yena-space-9)] w-full md:pt-[8vh] md:pb-[8vh]">
        <div class="ai-advance-grid">
            <div class="[grid-area:settings]">
                
                <div class="flex items-stretch flex-col gap-3">
                    <div class="text-sm">{{ __('Settings') }}</div>

                    <div class="yena-accordion">
                        <div class="-accordion-item" x-data="{ expanded: false }" :class="{'-active': expanded}">
                            <button class="-accordion-button" @click="expanded = ! expanded" type="button">
                                
                                <div class="flex flex-row gap-3 w-full">
                                    <div class="inline text-sm text-[var(--yena-colors-blue-300)]">
                                        {!! __i('--ie', 'item-pen-text-square', 'w-5 h-5') !!}
                                    </div>
                                    <div class="flex flex-col gap-0 text-left">
                                        <div class="font-bold">{{ __('Text content') }}</div>

                                        <p class="text-sm font-medium overflow-hidden overflow-ellipsis [display:-webkit-box] [-moz-box-orient:vertical] [-webkit-line-clamp:var(--yena-line-clamp)] [--yena-line-clamp:2]" x-show="!expanded" x-transition>{{ __('Generate · Detailed · English (US)') }}</p>
                                    </div>
                                    <div class="flex-[1] justify-self-stretch self-stretch"></div>
                                    {!! __i('Arrows, Diagrams', 'Arrow.2', '-open-icon') !!}
                                </div>
                            </button>
                            <div x-show="expanded" x-collapse>
                                <div class="-accordion-panel">
                                    sss
                                </div>
                            </div>
                        </div>
                        <div class="-accordion-item" x-data="{ expanded: false }" :class="{'-active': expanded}">
                            <button class="-accordion-button" @click="expanded = ! expanded" type="button">
                                
                                <div class="flex flex-row gap-3 w-full">
                                    <div class="inline text-sm text-[var(--yena-colors-red-300)]">
                                        {!! __i('--ie', 'image-picture', 'w-5 h-5') !!}
                                    </div>
                                    <div class="flex flex-col gap-0 text-left">
                                        <div class="font-bold">{{ __('Images') }}</div>

                                        <p class="text-sm font-medium overflow-hidden overflow-ellipsis [display:-webkit-box] [-moz-box-orient:vertical] [-webkit-line-clamp:var(--yena-line-clamp)] [--yena-line-clamp:2]" x-show="!expanded" x-transition>{{ __('Generate · Detailed · English (US)') }}</p>
                                    </div>
                                    <div class="flex-[1] justify-self-stretch self-stretch"></div>
                                    {!! __i('Arrows, Diagrams', 'Arrow.2', '-open-icon') !!}
                                </div>
                            </button>
                            <div x-show="expanded" x-collapse>
                                <div class="-accordion-panel">
                                    sss
                                </div>
                            </div>
                        </div>
                        <div class="-accordion-item" x-data="{ expanded: false }" :class="{'-active': expanded}">
                            <button class="-accordion-button" @click="expanded = ! expanded" type="button">
                                
                                <div class="flex flex-row gap-3 w-full">
                                    <div class="inline text-sm text-[var(--yena-colors-purple-300)]">
                                        {!! __i('interface-essential', 'browser-internet-web-network-window-app-icon', 'w-5 h-5') !!}
                                    </div>
                                    <div class="flex flex-col gap-0 text-left">
                                        <div class="font-bold">{{ __('Site') }}</div>

                                        <p class="text-sm font-medium overflow-hidden overflow-ellipsis [display:-webkit-box] [-moz-box-orient:vertical] [-webkit-line-clamp:var(--yena-line-clamp)] [--yena-line-clamp:2]" x-show="!expanded" x-transition>{{ __('Generate · Detailed · English (US)') }}</p>
                                    </div>
                                    <div class="flex-[1] justify-self-stretch self-stretch"></div>
                                    {!! __i('Arrows, Diagrams', 'Arrow.2', '-open-icon') !!}
                                </div>
                            </button>
                            <div x-show="expanded" x-collapse>
                                <div class="-accordion-panel">
                                    sss
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="[grid-area:editor] h-full">
                <div class="flex items-stretch flex-col gap-3">
                    <div class="text-sm">{{ __('Content') }}</div>

                    <div class="flex flex-col flex-1 w-full bg-white rounded-lg shadow-sm">
                        
                    </div>
                </div>

            </div>
            <div class="[grid-area:tips]">
                <div class="flex items-stretch flex-col gap-3">
                    <div class="text-sm">{{ __('Tips') }}</div>
                    <div class="p-4 rounded-md bg-[var(--yena-colors-whiteAlpha-700)]">
                        <div class="flex items-center flex-row gap-1 text-sm">
                            <i class="ph ph-question-mark"></i>
                            <p class="font-semibold text-[var(--yena-colors-gray-800)]">
                                {{ __('Freeform') }}
                            </p>
                        </div>
                        <p class="text-sm">Freeform lets you scale or shrink your content into as many cards as you want. For example, you can turn a long document into a concise presentation.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>