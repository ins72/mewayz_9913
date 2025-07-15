@props(['checked' => false])

@php
    $attr = $attributes->merge(['class' => 'peer absolute left-1/2 hidden h-full w-full -translate-x-1/2 rounded-md']);

    if($checked) $attr = $attr->merge(['checked' => true]);
@endphp

<label class="group relative flex items-center gap-2 text-sm font-medium text-gray-600">
    
    <input type="checkbox" {!! $attr !!}>
    
    <span class="peer-checked:bg-primary-base flex h-6 w-12 flex-shrink-0 items-center rounded-full bg-gray-300 p-1 duration-300 ease-in-out after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow-md after:duration-300 group-hover:after:translate-x-1 peer-checked:after:translate-x-6">
        <span class="sr-only"></span>
    </span>

    <span>{{ $slot }}</span>
</label>