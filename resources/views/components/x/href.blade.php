@props(['active'])

@php
    $_attributes = $attributes;
    $href = $attributes['href'];
    if(isset($attributes['href'])){
        $_attributes = $_attributes->merge(['x-link.prefetch' => 'true']);

        
        $parse = parse_url($href);
        if(request()->url() == $href){
            $_attributes = $_attributes->merge(['class' => '--active']);
        }
    }
@endphp

<a {{ $_attributes }} >{{ $slot }}</a>