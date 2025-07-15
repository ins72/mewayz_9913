@props(['disabled' => false, 'label' => ''])

<div class="flex flex-col">
   <span class="text-content-primary text-sm">{{ $label }}</span>
   <div class="group flex flex-col data-[has-helper=true]:pb-4 w-full">
      <label class="block text-small font-medium text-foreground pb-1.5 will-change-auto origin-top-left transition-all !duration-200 !ease-out motion-reduce:transition-none"></label>
      <div class="relative w-full inline-flex tap-highlight-transparent flex-row items-center shadow-sm px-3 gap-3 bg-default-100 data-[hover=true]:bg-default-200 group-data-[focus=true]:bg-default-100 h-unit-10 min-h-unit-10 rounded-medium !h-auto transition-background motion-reduce:transition-none !duration-150 outline-none group-data-[focus-visible=true]:z-10 group-data-[focus-visible=true]:ring-2 group-data-[focus-visible=true]:ring-focus group-data-[focus-visible=true]:ring-offset-2 group-data-[focus-visible=true]:ring-offset-background" style="cursor: text;">
         
      <textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full h-full font-normal !bg-transparent outline-none placeholder:text-foreground-500 text-small resize-none py-2']) !!}></textarea></div>
      <div class="flex relative flex-col gap-1.5 pt-1 px-1"></div>
   </div>
</div>