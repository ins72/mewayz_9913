@props([
    'title' => '',
    'desc' => '',
    'fullScreen' => true,
    'image' => '21.png',
    'svg' => false,
])
@php

    if(!validate_url($image)){
        $image = gs("assets/image/silly/$image");
    }
@endphp
<div {{ $attributes->merge([
    'class' => 'w-full lg:px-4 max-w-[80em] h-full mx-auto'
]) }}>
    <div class="flex items-center flex-row gap-4 flex-1 h-full max-w-[var(--yena-sizes-3xl)] m-auto {{ $fullScreen ? 'min-h-screen lg:min-h-[700px]' : '' }}">
        <div class="flex items-start justify-center flex-col gap-6 flex-1">

            @if ($svg)
                {!! $svg !!}
            @endif


            <h2 class="text-3xl font-bold leading-[1.33] md:leading-[1.2] md:text-4xl">{{ $title }}</h2>

            @if (!$svg)
                <div class="flex-1 flex lg:hidden max-h-[300px]">
                    <img src="{{ $image }}" class="object-contain" alt="">
                </div>
            @endif
            <p class="text-lg">{!! $desc !!}</p>

            {{ $slot }}
        </div>

        @if (!$svg)
        <div class="flex-1 hidden lg:flex">
            <img src="{{ $image }}" alt="">
        </div>
        @endif
    </div>
</div>