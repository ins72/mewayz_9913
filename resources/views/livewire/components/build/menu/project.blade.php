
<?php

use function Livewire\Volt\{state, mount};
    
state([
    'projects' => [],
]);

mount(function () {
    $this->projects = Auth::check() ? Auth::user()->projects : [];
});

$setProject = function($index){
    if(empty($project = $this->projects[$index])) return;
    $user = Auth::user();

    $user->_last_project_id = $project->id;
    $user->save();


    $this->redirect(route('dashboard-index'), navigate: true);
};
?>

<div>
    
    <div>
        <ul class="w-full flex flex-col gap-0.5 p-1 outline-none">

            <div>
                @foreach ($projects as $index => $item)
                <x-link wire:click="setProject({{ $index }})" >
                    <div class="flex flex-row items-center justify-center rounded-lg h-8 w-8" style="background: {{ $item->brandColor() }}; color: {{ getContrastColor($item->brandColor()) }};"><span class="text-xs">{{ $item->getNameInitial() }}</span></div>
                    <span class="ms-2">{{ $item->name }}</span>    
                </x-link>
                @endforeach
            </div>
            <x-link href="{{ route('create-project') }}">
                {!! __icon('interface-essential', 'plus-add.3', 'w-5 h-5') !!}

                <span class="ms-1">{{ __('Create new') }}</span> 
            </x-link>
        </ul>
        
    </div>
</div>
