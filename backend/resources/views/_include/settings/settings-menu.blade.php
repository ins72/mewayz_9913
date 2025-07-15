@php
    
    $current = isset($current) ? $current : null;
@endphp
<div class="fixed h-[calc(100%-57px)] w-72 overflow-y-auto py-4 pr-6">
   <a href="{{ route('dashboard-index') }}" wire:navigate.hover class="flex w-full flex-row items-center gap-2 hidden">
      <button class="z-0 group relative inline-flex items-center justify-center box-border appearance-none select-none whitespace-nowrap font-normal subpixel-antialiased overflow-hidden tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 gap-unit-2 rounded-small px-unit-0 !gap-unit-0 data-[pressed=true]:scale-[0.97] transition-transform-colors motion-reduce:transition-none bg-transparent text-default-foreground data-[hover=true]:bg-default/40 min-w-unit-8 w-unit-8 h-unit-8 text-lg" type="button">
         {!! __icon('Arrows, Diagrams', 'Arrow.8', 'w-7 h-7') !!}
      </button>
      <span class="text-content-primary text-lg font-semibold">{{ __('Settings') }}</span>
   </a>
   <div class="mt-8 flex w-full flex-col">
      <ul class="flex w-full flex-col">
         <a href="{{ route('dashboard-index') }}" wire:navigate class="text-content-secondary hover:bg-action-hover-ghost flex w-full cursor-pointer flex-row items-center rounded-lg px-3 py-2 text-sm {{ $current == 'dashboard' ? '!text-content-brand-onSurface !bg-background-brand-subtle !font-semibold' : '' }}"><span>{{ __('Dashboard') }}</span></a>

         <a href="{{ route('dashboard-settings-index') }}" wire:navigate class="text-content-secondary hover:bg-action-hover-ghost flex w-full cursor-pointer flex-row items-center rounded-lg px-3 py-2 text-sm {{ $current == 'org' ? '!text-content-brand-onSurface !bg-background-brand-subtle !font-semibold' : '' }}"><span>{{ __('Organization') }}</span></a>
         
         <a href="{{ route('dashboard-settings-billings') }}" wire:navigate class="text-content-secondary hover:bg-action-hover-ghost flex w-full cursor-pointer flex-row items-center rounded-lg px-3 py-2 text-sm {{ $current == 'billing' ? '!text-content-brand-onSurface !bg-background-brand-subtle !font-semibold' : '' }}"><span>{{ __('Billing') }}</span></a>
         
         <a href="{{ route('console-settings-account') }}" wire:navigate class="text-content-secondary hover:bg-action-hover-ghost flex w-full cursor-pointer flex-row items-center rounded-lg px-3 py-2 text-sm {{ $current == 'account' ? '!text-content-brand-onSurface !bg-background-brand-subtle !font-semibold' : '' }}"><span>{{ __('Account') }}</span></a>         
      </ul>
   </div>
</div>