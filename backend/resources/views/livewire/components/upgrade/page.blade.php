<?php

    use App\Models\Plan;
    use function Laravel\Folio\name;
    use function Livewire\Volt\{state, uses, mount, computed};
    name('dashboard-upgrade-index');

    // uses([\App\Traits\FolioAuth::class]);

    state([
        'annual' => false
    ]);

    state([
        '_popular_plan' => function(){
            $data = [];
            foreach ($this->plans as $plan) {
                $data[$plan->id] = $plan->subscribers()->paid()->notCancelled()->count();
            }

            // Find all keys that have this maximum value
            // $mostPopularPosts = array_keys($data, max($data));
            return collect(array_keys($data, max($data)))->first();
        }
    ]);

    $plans = computed(function(){
        return Plan::where('status', 1)->orderBy('position', 'ASC')->orderBy('id', 'DESC')->get();
    });

    $_cancel_subscription = function(){
        iam()->cancelCurrentSubscription();
    };

?>


<div>
    <style>
        @media(max-width: 768px){
            .yena-container, .yena-root-main{
                padding: 0 !important;
            }
        }
    </style>
    <div class="min-h-screen bg--gray-100 pb-20">
        <div class="mx-auto [max-w-var(--yena-sizes-5xl)] px-8 pt-8">
        <div class="flex flex-col items-start justify-start">
            
            <section class="relative mx-auto flex flex-col justify-between gap-8 md:flex-row md:items-center !mb-5 w-[100%]">
                
                <div class="flex flex-col items-start">
            
                <div class="!mb-5">
                    <div class="font-heading !mb-2 font--12 font-extrabold upper-case tracking-wider flex items-center">
                        <div class="text-3xl font-bold leading-normal sm:text-4xl sm:leading-normal whitespace-nowrap font--caveat">{{ __('Upgrade your plan') }}</div>
                    </div>

                    @php
                        $sub = iam()->activeSubscription();
                    @endphp
                    @if ($sub && $sub->plan)
                        
                    <p class="!mt-4 pr-8 text-lg text-gray-500">{!! __t('You are currently on <b>:plan</b>. :extra', ['plan' => $sub->plan->name, 'extra' => $sub->plan->is_free ? '' : __('Will expire in :expiry Days', ['expiry' => $sub->getRemainingDays()])]) !!}</p>
                                            
                    <form wire:submit.prevent="_cancel_subscription" class="hidden">
                        <button type="submit" class="first-letter: bg-red-500  text-white disabled:opacity-75 hover:bg-red-400
                            block appearance-none rounded-lg text-sm font-medium duration-100 focus:outline-transparent px-3 py-1.5">
                            <div class="relative flex items-center justify-center ">
                                <div class="duration-100">{{ __('Ok, proceed') }}</div>
                            </div>
                        </button>
                    </form>
                    
                    {{-- <div class="!mt-3 flex items-center">

                        <div class="flex z-menuc" data-wire-updates data-appends-to=".--appended" wire:ignore.self data-max-width="600" data-handle=".--control">

                        
                            <a class="sandy-button --control" wire:ignore aria-expanded="false">
                                <div class="--sandy-button-container">
                                    <span class="text-xs">{{ __('Unsubscribe') }}</span>
                                </div>
                            </a>

                            <div class="--appended" wire:ignore></div>

                            <div class="z-menuc-content-temp" wire:ignore.self>
                                <div>
                                    <ul class="z-menu-ul w-30em max-w-[100%] shadow-xl border border-solid border-gray-200 rounded-xl">
                                        <div class="p-6">
                                            <div class="w-[100%]">
                                            <div class="flex w-[100%] items-center justify-between">
                                                <div
                                                    class="font-heading mb-2 px-0 text-sm font-extrabold uppercase tracking-wider text-gray-400 flex items-center w-[100%]">
                                                    <span class="whitespace-nowrap">{{ __('Unsubscribe') }}</span>
                                                    <div class="border-b border-solid border-gray-400 w-[100%] ml-2 flex"></div>
                                                    <button type="button" class="-mt-1 rounded-md p-1 text-gray-500 hover:bg-zinc-50 z-menu-close" tabindex="0">
                                                    <i class="fi fi-rr-cross"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="mt-1 mb-5 text-sm text-gray-600">
                                            <div>
                                                {{ __('Are you sure you want to unsubscribe from this plan? This action is irreversible.') }}
                                            </div>
                                            </div>
                                            
                                            <form wire:submit.prevent="_cancel_subscription">
                                                <button type="submit" class="first-letter: bg-red-500  text-white disabled:opacity-75 hover:bg-red-400
                                                    block appearance-none rounded-lg text-sm font-medium duration-100 focus:outline-transparent px-3 py-1.5">
                                                    <div class="relative flex items-center justify-center ">
                                                        <div class="duration-100">{{ __('Ok, proceed') }}</div>
                                                    </div>
                                                </button>
                                            </form>
                                        </div>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div> --}}
                    @else
                    {{-- <p class="mt-4 pr-8 text-lg text-gray-500">{{ __('You have no active subscription') }}</p> --}}
                    @endif
                </div>
                {{-- <p class="mt-6 text-base text-gray-500">{{ __('No contract. Cancel any time') }}</p> --}}
                </div>
            </section>
            <div class="sm:align-center flex items-center gap-4">
                <div class="relative flex w-[100%] rounded-full bg-zinc-200 p-2">
                    
                    <a wire:click="$set('annual', false)" class="rounded-full border px-4 py-2 text-sm font-medium focus:z-10 focus:outline-none focus:ring-0 sm:w-auto {{ !$annual ? 'border-gray-200 shadow-sm bg-white text-gray-900' : 'text-gray-700 cursor-pointer' }}">{{ __('Monthly billing') }}</a>
                    
                    <a wire:click="$set('annual', true)" class="ml-0.5 rounded-full border border-transparent px-4 py-2 text-sm font-medium text-gray-700 duration-150 focus:z-10 focus:ring-0 sm:w-auto {{ $annual ? 'border-gray-200 shadow-sm bg-white text-gray-900' : 'text-gray-700 cursor-pointer' }}">{{ __('Annual billing') }}</a>
                </div>
            </div>


            @if ($this->plans->isEmpty())
        
            <div>
                <p class="mt-5 text-xs text-gray-600 flex items-center gap-1"><i class="fi fi-rr-triangle-warning"></i> {{ __('No available plan(s)') }}</p>
            </div>
            
            @endif

            <div class="relative mt-4 grid w-[100%] gap-4 grid-cols-1 md:!grid-cols-3">
                @foreach ($this->plans as $item)

                @php
                    $price = (float) $item->price;
                    $annual_price = (float) $item->annual_price;

                    if($annual) $price = $item->annual_price;

                    if($item->is_free){
                        $price = 0;
                    }
                @endphp
                <div class="-pricing-list w-[100%] {{ $item->id == $_popular_plan ? '-popular' : '' }}">
                    <div class="-list-inner">
                        <div class="--header">
                            <div class="--background"></div>
                            <h2 class="--header-name">{{ $item->name }}</h2>
                            @if (!empty($item->description))
                            <p class="--header-text">{{ $item->description }}</p>
                            @endif

                            @if ($item->id == $_popular_plan)
                                <span class="yena-badge-g popular-badge !py-1 border-b border-[var(--yena-colors-gray-50)]">{{ __('Popular') }}</span>
                            @endif
                        </div>

                        <div class="h-full gap-5 bg-white pb-8 px-6 pt-6">

                            @php
                                $features = $item->features()->orderBy('id', 'ASC')->get();
                            @endphp

                            @if ($features->isEmpty())
                                <div>
                                    <p class="mt-2 text-xs text-gray-600 flex items-center gap-1"><i class="fi fi-rr-triangle-warning"></i> {{ __('No feature(s) available.') }}</p>
                                </div>
                            @endif
                            <div>
                                <div class="[min-h-var(--yena-sizes-12)] mb-2">
                                    <div class="flex items-end flex-row gap-2">
                                        <p class="font-semibold leading-none text-[2.5rem] text-[var(--yena-colors-gray-800)]">{!! \Currency::symbol(settings('payment.currency')) . formatNum($price) !!}</p>
                                        <p class="text-[var(--yena-colors-gray-600)] text-[var(--yena-fontSizes-sm)]">/ {{ $annual ? __('year') : __('month') }}</p>
                                    </div>

                                    @php
                                        $show_discount = false;
                                        $discount = 0;
                                        
                                        $_price = (float) $item->price;

                                        if($annual_price > $_price){
                                            $discount = ($_price/$annual_price) * 100;
                                            $discount = number_format($discount, 0);
                                            $show_discount = true;
                                        }
                                    @endphp

                                    @if ($show_discount && $annual)
                                        <span class="yena-badge-g">{{ __('Save :percent with annual', [
                                            'percent' => $discount.'%'
                                        ]) }}</span>
                                    @endif

                                    {{-- @if (!$item->is_free && $annual)
                                    <p class="text-[var(--yena-colors-gray-700)] font-bold">{{ __('When billed annually') }} ({{ $item->annual_price }})</p>
                                    @endif --}}
                                </div>
                                <a href="{{ route('console-upgrade-view', ['_id' => $item->id]) }}" x-link.prefetch class="btn mt-4 w-[100%] text-center disabled:opacity-80 bg-black text-white shadow-lg shadow-black/50 block appearance-none rounded-md text-sm font-medium duration-100 focus:outline-transparent px-4 py-2.5 mb-1">
                                    <div class="relative flex items-center justify-center ">
                                        <div class="duration-100 undefined false">{{ iam()->activeSubscription() && iam()->activeSubscription()->plan && iam()->activeSubscription()->plan->id == $item->id ? __('Current Plan') : __('Proceed')  }}</div>
                                    </div>
                                </a>
                                <div class="mb-4 font-bold">{{ __('Plan includes:') }}</div>
                                <ul class="list-none flex flex-col gap-2">
                                    @foreach ($features as $feature)
                                    <li class="flex items-start font-normal text-sm">
                                        <div class="flex items-center flex-row gap-2 {{ $feature->type == 'limit' && $feature->isUnlimited() && str()->contains($feature->name, 'Ai') ? 'text-[var(--yena-colors-orchid-500)]' : '' }}">
                                            
                                            @if (str()->contains($feature->name, 'Ai'))
                                                <div class="ztext-[var(--yena-colors-green-500)]">
                                                    <i class="ph ph-sparkle text-base {{ $feature->enable ? 'text-[var(--yena-colors-green-500)]' : 'text-[var(--yena-colors-red-500)]' }}"></i>
                                                </div>
                                            @elseif ($feature->code == 'feature.export_site')
                                            <div>
                                                <i class="ph ph-file-html text-base {{ $feature->enable ? 'text-[var(--yena-colors-green-500)]' : 'text-[var(--yena-colors-red-500)]' }}"></i>
                                            </div>
                                            @else
                                                @if ($feature->enable)
                                                    <div class="text-[var(--yena-colors-green-500)]">
                                                        <i class="ph ph-check text-base"></i>
                                                    </div>
                                                    @else
                                                    <div class="text-[var(--yena-colors-red-500)]">
                                                        <i class="ph ph-x text-base"></i>
                                                    </div>
                                                @endif
                                            @endif
                                            <span class="inline-block">{{ $feature->type == 'limit' ? ($feature->isUnlimited() ? __('Unlimited') : $feature->limit) : '' }} {{ $feature->name }}</span>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex flex-col gap-4 w-[100%] mt-10">
                <div class="text-center text-[var(--yena-colors-gray-600)] text-sm">
                    <i class="ph ph-lock"></i> {{ __('Secure checkout') }} ·
                    <i class="ph ph-chat-text"></i> {{ __('Priority customer support') }} ·
                    <i class="ph ph-heart"></i> {{ __('Built with love') }}
                </div>
            </div>
        </div>
        </div>
    </div>
</div>