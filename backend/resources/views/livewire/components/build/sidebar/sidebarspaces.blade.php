<?php

use App\Models\OrganizationSpace;
use function Livewire\Volt\{state, computed, on, mount};
state([
    '_org' => fn () => request()->organization,
]);

$spaces = computed(function () {
    return OrganizationSpace::where('_org', $this->_org->id)->orderBy('id', 'DESC')->get();
})->persist(seconds: 7200);

on(['spaceCreated' => function (){
    unset($this->spaces);
}]);

mount(function(){
    //unset($this->spaces);
});


?>

<div>
    

    <script>
        function _spaces(){
            return {
                createSpace: true,
            }
        }
    </script>
    <div x-data="_spaces">

        <div class="relative mb-8">

        
            <div class="flex z-menuc" data-max-width="600" data-use-handle-width="-" data-handle=".--control">
        
                <div class="hover:bg-action-hover-ghost flex cursor-pointer items-center gap-2 rounded-lg p-2 --control w-full">
                    <div class="rounded-xl p-1 bg-background-positive-subtle text-content-positive-onSurface">
                        {!! __icon('Folders', 'folder-bookmark', 'w-4 h-4') !!}
                    </div>
                    <p class="text-content-primary flex-1 select-none text-sm font-semibold">srrwer</p>
                    
                    
                    {!! __icon('interface-essential', 'arrow', 'w-4 h-4') !!}
                </div>
        
                <div class="z-menuc-content-temp">
                    <ul class="z-menu-ul min-w-[200px] w-full shadow-lg border border-solid border-gray-200 rounded-xl">
                        <div>
                            <ul class="w-full flex flex-col gap-0.5 p-1 outline-none">
                                
                                @foreach ($this->spaces as $item)

                                <div class="hover:bg-action-hover-ghost flex cursor-pointer items-center gap-2 rounded-lg p-2 --control w-full" wire:key="{{ uukey("spaces-item-$item->id") }}">
                                    <div class="rounded-xl p-1 bg-background-positive-subtle text-content-positive-onSurface">
                                        {!! __icon('Folders', 'folder-bookmark', 'w-4 h-4') !!}
                                    </div>
                                    <p class="text-content-primary flex-1 select-none text-sm font-semibold">{{ $item->name }}</p>
                                    
                                    {!! __icon('Content Edit', 'Pen, Edit', 'w-[15px] h-[15px] hover:text-content-primary text-content-tertiary') !!}
                                </div>
                                @endforeach
                                <x-link @click="$dispatch('open-modal', 'create-space-modal')">
                                    {!! __icon('interface-essential', 'plus-add.3', 'w-5 h-5') !!}
        
                                    <span class="ms-1">{{ __('New Space') }}</span> 
                                </x-link>
                            </ul>
                            
                        </div>
                    </ul>
                </div>
            </div>
            
        </div>

    
        @push('scripts')
            <x-modal name="create-space-modal" maxWidth="md" focusable>
                
                <livewire:components.build.sidebar.spaces.create :key="uukey('spaces.create')">
            </x-modal>
        @endpush
    </div>
</div>
