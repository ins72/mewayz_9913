@props(['active'])

@php
    $classes = 'z-0 group relative inline-flex items-center justify-center box-border appearance-none select-none whitespace-nowrap font-normal subpixel-antialiased overflow-hidden tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 gap-unit-2 rounded-small px-unit-0 !gap-unit-0 data-[pressed=true]:scale-[0.97] transition-transform-colors motion-reduce:transition-none bg-transparent text-default-foreground data-[hover=true]:bg-default/40 min-w-unit-8 w-unit-8 h-unit-8 text-lg';


    $_attributes = $attributes->merge(['class' => $classes]);
    
    if(isset($attributes['href'])){
        $_attributes = $_attributes->merge(['wire:navigate.hover' => 'true']);
    }
@endphp

<a {{ $_attributes }}>
    {{ $slot }}
</a>