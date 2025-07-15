@props([
    'prefix' => 'section.settings'
])
<div class="flex items-center">
    <template x-if="{{ $prefix }}.button_one_text">
        <a x-outlink="{{ $prefix }}.button_one_link" class="btn-1">
            <button class="t-1 shape" x-text="{{ $prefix }}.button_one_text"></button>
        </a>
    </template>
    <template x-if="{{ $prefix }}.button_two_text">
        <a x-outlink="{{ $prefix }}.button_two_link" class="btn-2">
            <button class="t-1 shape" x-text="{{ $prefix }}.button_two_text"></button>
        </a>
    </template>
    <div class="screen"></div>
</div>