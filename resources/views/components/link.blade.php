@props(['active'])

@php
    $classes = 'flex group gap-2 items-center justify-between relative px-2 py-1.5 w-full h-full box-border rounded-small subpixel-antialiased cursor-pointer tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 data-[focus-visible=true]:dark:ring-offset-background-content1 data-[hover=true]:bg-default data-[hover=true]:text-default-foreground data-[selectable=true]:focus:bg-default data-[selectable=true]:focus:text-default-foreground';


    $_attributes = $attributes->merge(['class' => $classes]);
    
    if(isset($attributes['href'])){
        $_attributes = $_attributes->merge(['wire:navigate.hover' => 'true']);
    }
@endphp

<a {{ $_attributes }}>
    <span class="flex-1 text-small font-normal truncate">
        <div class="flex flex-row items-center">
            {{ $slot }}
        </div>
    </span>
</a>