<?php

use function Livewire\Volt\{state, updated};
use App\Models\OrganizationSpace;

updated(['createTitle' => function (){
    $this->createSlug = slugify($this->createTitle, '-');
}]);
state([
    'createTitle' => '',
    'createSlug' => '',

    '_org' => fn () => request()->organization,
]);

$createSpace = function(){
    $this->validate([
        'createTitle' => 'required',
        'createSlug' => 'required',
    ]);
    $new = new OrganizationSpace;
    $new->_org = $this->_org->id;
    $new->name = $this->createTitle;
    $new->slug = slugify($this->createSlug, '-');
    $new->save();
    

    $this->createTitle = '';
    $this->createSlug = '';

    $this->dispatch('spaceCreated');
    $this->dispatch('close');
};

?>

<div class="w-full">
    
                
    <form wire:submit="createSpace">
        <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-1 right-1 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
            <i class="fi fi-rr-cross-small"></i>
        </a>

        <header class="flex py-4 px-6 flex-initial text-large font-semibold">{{ __('New space') }}</header>

        <div class="flex flex-1 flex-col gap-3 px-6 py-2">
            <div>
                <x-input-x wire:model.blur="createTitle" placeholder="{{ __('Space title') }}"></x-input-x>
            </div>

            <div>
                <x-input-x wire:model="createSlug" placeholder="{{ __('Slug') }}"></x-input-x>
            </div>
        
            @php
               $error = false;
   
               if(!$errors->isEmpty()){
                     $error = $errors->first();
               }
   
               if(Session::get('error._error')){
                     $error = Session::get('error._error');
               }
            @endphp
            @if ($error)
               <div class="mt-5 bg-red-200 font--11 p-1 px-2 rounded-md">
                     <div class="flex items-center">
                        <div>
                           <i class="fi fi-rr-cross-circle flex text-xs"></i>
                        </div>
                        <div class="flex-grow ml-1 text-xs">{{ $error }}</div>
                     </div>
               </div>
            @endif   
        </div>
        <footer class="flex flex-row gap-2 px-6 py-4 justify-end">
            
            <button class="z-0 group relative inline-flex items-center justify-center box-border appearance-none select-none whitespace-nowrap font-normal subpixel-antialiased overflow-hidden tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 px-unit-4 min-w-unit-20 h-unit-10 text-small gap-unit-2 rounded-medium [&amp;>svg]:max-w-[theme(spacing.unit-8)] data-[pressed=true]:scale-[0.97] transition-transform-colors motion-reduce:transition-none bg-default text-default-foreground" type="button">Discard</button>
                
                
            <button class="z-0 group relative inline-flex items-center justify-center box-border appearance-none select-none whitespace-nowrap font-normal subpixel-antialiased overflow-hidden tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 px-unit-4 min-w-unit-20 h-unit-10 text-small gap-unit-2 rounded-medium [&amp;>svg]:max-w-[theme(spacing.unit-8)] data-[pressed=true]:scale-[0.97] transition-transform-colors motion-reduce:transition-none bg-primary text-primary-foreground" type="submit">{{ __('Add') }}</button>
        </footer>
    </form>
</div>
