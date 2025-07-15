<div>
    @props([
        'title' => '',
        'description' => '',
    ])

    <div x-data="__popup">
        <div class="overlay !block !opacity-100 !bg-[rgb(0_0_0_/_70%)] !z-[9999]" x-cloak :class="{
            '!block': popup
        }" @click="popup=false" data-teleport-target="true">
            <div class="delete-site-card !border-0 !shadow-lg" @click="$event.stopPropagation()">
               <div class="overlay-card-body !rounded-md [box-shadow:var(--yena-shadows-2xl)]">
                  <h2>{{ __($title) }}</h2>
                  <p class="mt-4 mb-4 text-[var(--t-m)] text-center leading-[var(--l-body)] text-[var(--c-mix-3)] w-3/5 mx-auto">{{ __($description) }}</p>

                  <div class="card-button pl-[var(--s-2)] pr-[var(--s-2)] pt-[0] pb-[var(--s-2)] flex gap-2">
    
                     {{ $slot }}
                  </div>
               </div>
            </div>
         </div>
    </div>
     
     @script
     <script>
         Alpine.data('__popup', () => {
             return {
                
             }
         });
     </script>
     @endscript
</div>