@props(['disabled' => false, 'label' => ''])

<div class="yena-input">
   <label class="{{ empty($label) ? 'hidden' : '' }}">{{ $label }}</label>
   <input {{ $disabled ? 'disabled' : '' }} {!! $attributes !!}>
</div>