@php
    $_handle = null;
@endphp
@props(['maxwidth'])


<div class="flex z-menuc" data-max-width="{{ isset($maxwidth) ? $maxwidth : '600' }}" {{ $attributes }} data-appends-to=".--appended" data-handle=".--open-delete">

    @if (isset($handle))
        {!! $handle !!}
    @endif
    
    <div class="--appended" wire:ignore></div>

    <div class="z-menuc-content-temp">
        <ul class="z-menu-ul w-30em max-w-full shadow-xl border-gray-200 rounded-xl">
            <div class="p-6">
              <i class="fi fi-rr-triangle-warning text-lg text-red-500"></i>
              <div class="mt-1 text-sm text-gray-600">
                <div>
                    @if (isset($content))
                        <span slot="description">{!! $content !!}</span>
                    @endif
                </div>
              </div>

                <div class="border-b border-solid border-gray-300 my-3"></div>
                <div class="mt-4 flex justify-end gap-2">
                  <button type="button" class=" block appearance-none rounded-md border bg-white text-sm font-medium text-gray-600 shadow-sm duration-100 focus:ring-0
                  px-3 py-1.5 block appearance-none rounded-lg text-sm font-medium duration-100 focus:outline-transparent px-3 py-1.5 z-menu-close">{{ __('Cancel') }}</button>

                  @if (isset($form))
                      {!! $form !!}
                  @endif
                          
                </div>
            </div>
        </ul>
    </div>
</div>