


<div {{ $attributes->merge(['wire:key' => \Str::uuid()]) }}>
    {{ $slot }}
</div>