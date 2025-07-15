
<?php
   use function Livewire\Volt\{placeholder};
?>
<div>

   <template x-teleport="body">
      <div x-data="alpineToasted" class="livewire_toast">
         <div x-cloak>
            <div class="z-[9998] bg-overlay/50 backdrop-opacity-disabled w-screen h-screen fixed inset-0" @click="close()" x-show="show"></div>
            <div class="flex w-screen h-[100dvh] fixed inset-0 z-[9999] overflow-x-auto justify-center [--scale-enter:100%] [--scale-exit:100%] [--slide-enter:0px] [--slide-exit:80px] sm:[--scale-enter:100%] sm:[--scale-exit:103%] sm:[--slide-enter:0px] sm:[--slide-exit:0px] items-end sm:items-center" @click="close()" x-show="show" x-transition x-transition:enter.duration.300ms
            x-transition:leave.duration.300ms>
               <section role="dialog" tabindex="-1" class="flex flex-col relative z-50 w-full box-border bg-content1 outline-none mx-1 my-1 sm:mx-6 sm:my-16 max-w-md rounded-large shadow-small overflow-y-hidden">
                  <a role="button" aria-label="Close" class="absolute appearance-none select-none top-1 right-1 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2" @click="close()">
                     <svg aria-hidden="true" fill="none" focusable="false" height="1em" role="presentation" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="1em">
                        <path d="M18 6L6 18M6 6l12 12"></path>
                     </svg>
                     </a>
                  <header class="flex py-4 px-6 flex-initial text-large font-semibold items-center">
                     <span class="me-2 text-2xl" :class="{'text-success-500': type == 'success', 'text-danger-500': type == 'error'}">
                        <template x-if="type == 'success'">
                           {!! __icon('interface-essential', 'checkmark-circle-1', 'w-5 h-5') !!}
                        </template>
                        <template x-if="type == 'error'">
                           {!! __icon('interface-essential', 'warning', 'w-5 h-5') !!}
                        </template>
                     </span>
                     <span x-text="message"></span>
                  </header>
                  <div class="w-[100%] max-w-full bg-white shadow rounded">
                     <div class="bg-gray-200 rounded h-4" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                        <div class="bg-[hsl(228,100%,90%)] rounded h-4 text-center" :style="`width: ${width}%; transition: width ${duration}s linear;`"></div>
                     </div>
                 </div>
               </section>
            </div>
         </div>
      </div>
   </template>
</div>
