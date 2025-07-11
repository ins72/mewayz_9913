
<?php
   use App\Livewire\Actions\ToastUp;
   use App\Models\Audience;
   use function Livewire\Volt\{state, mount, placeholder};

   state([
      
   ]);

   mount(fn() => '');

   placeholder('
   <div class="p-5 w-full mt-1">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');
?>


<div class="w-full">
   <div class="flex flex-col">
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross text-sm"></i>
      </a>

      <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Filter Audience') }}</header>

      <hr class="yena-divider">

      <form method="GET" class="px-8 pt-5 pb-6">

         <div class="search__form shadow-none border border-gray-200 border-solid w-full rounded-xl max-w-full">
             <input class="search__input h-10 rounded-xl" type="text" wire:model.live="$parent.query.search" name="query" value="{{ request()->get('query') }}"
                 placeholder="{{ __('Type your search word') }}">
             <button class="search__btn">
                 <svg class="icon icon-search">
                     <use xlink:href="{{ gs('assets/image/svg/sprite.svg#icon-search') }}">
                     </use>
                 </svg>
             </button>
         </div>

         <div class="block my-10"></div>
         <div class="flex flex-col justify-between normal-case">
             <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Origin') }}</span>
             <span class="text-label text-[color:#8f8b8b] text-sm mt-1">{{ __('View contacts who were created or added by...') }}</span>
         </div>

         <div class="flex gap-2 mt-5">
             <label class="sandy-big-checkbox w-full -card-radio-focus-label">
                 <input type="radio" class="sandy-input-inner" wire:model.live="$parent.query.created_by" name="created_by" value="-">
                 <div class="checkbox-inner !h-10 bg-transparent z-50">
                     <div class="checkbox-wrap">
                         <div class="content flex items-center">
                             <h1>{{ __('Anyone') }}</h1>
                         </div>
                         <div class="icon">
                             <div class="active-dot !w-5 !h-5">
                                 <i class="la la-check"></i>
                             </div>
                         </div>
                     </div>
                 </div>
             </label>
             <label class="sandy-big-checkbox w-full -card-radio-focus-label">
                 <input type="radio" class="sandy-input-inner" wire:model.live="$parent.query.created_by" name="created_by" value="me">
                 <div class="checkbox-inner !h-10 z-50 bg-transparent">
                     <div class="checkbox-wrap">
                         <div class="content flex items-center">
                             <h1>{{ __('Me') }}</h1>
                         </div>
                         <div class="icon">
                             <div class="active-dot !w-5 !h-5">
                                 <i class="la la-check"></i>
                             </div>
                         </div>
                     </div>
                 </div>
             </label>
             <label class="sandy-big-checkbox w-full -card-radio-focus-label">
                 <input type="radio" class="sandy-input-inner" wire:model.live="$parent.query.created_by" name="created_by" value="others">
                 <div class="checkbox-inner !h-10 z-50 bg-transparent">
                     <div class="checkbox-wrap">
                         <div class="content flex items-center">
                             <h1>{{ __('Others') }}</h1>
                         </div>
                         <div class="icon">
                             <div class="active-dot !w-5 !h-5">
                                 <i class="la la-check"></i>
                             </div>
                         </div>
                     </div>
                 </div>
             </label>
         </div>


         <div class="block my-10"></div>
         <div class="subtitle-border fs-15px flex flex-col justify-between normal-case">
             <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Sort By') }}</span>
         </div>
         <div class="flex gap-2 mt-5">
             <label class="sandy-big-checkbox w-full -card-radio-focus-label">
                 <input type="radio" class="sandy-input-inner" wire:model.live="$parent.query.search_by" name="search_by" value="name">
                 <div class="checkbox-inner !h-10 bg-transparent z-50">
                     <div class="checkbox-wrap">
                         <div class="content">
                             <h1>{{ __('Name') }}</h1>
                         </div>
                         <div class="icon">
                             <div class="active-dot !w-5 !h-5">
                                 <i class="la la-check"></i>
                             </div>
                         </div>
                     </div>
                 </div>
             </label>
             <label class="sandy-big-checkbox w-full -card-radio-focus-label">
                 <input type="radio" class="sandy-input-inner" wire:model.live="$parent.query.search_by" name="search_by" value="email" checked="">
                 <div class="checkbox-inner !h-10 bg-transparent z-50">
                     <div class="checkbox-wrap">
                         <div class="content">
                             <h1>{{ __('Email') }}</h1>
                         </div>
                         <div class="icon">
                             <div class="active-dot !w-5 !h-5">
                                 <i class="la la-check"></i>
                             </div>
                         </div>
                     </div>
                 </div>
             </label>
             <label class="sandy-big-checkbox w-full -card-radio-focus-label !hidden">
                 <input type="radio" class="sandy-input-inner" wire:model.live="$parent.query.search_by" name="search_by" value="created_at">
                 <div class="checkbox-inner !h-10 z-50 bg-transparent">
                     <div class="checkbox-wrap">
                         <div class="content">
                             <h1>{{ __('Date') }}</h1>
                         </div>
                         <div class="icon">
                             <div class="active-dot !w-5 !h-5">
                                 <i class="la la-check"></i>
                             </div>
                         </div>
                     </div>
                 </div>
             </label>
         </div>
         

         <div class="block my-10"></div>
         <div class="flex flex-col justify-between normal-case">
             <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Order By') }}</span>
         </div>
         <div class="flex gap-2 mt-5">
             <label class="sandy-big-checkbox w-full -card-radio-focus-label">
                 <input type="radio" class="sandy-input-inner" name="orderby" value="DESC" wire:model.live="$parent.query.orderby">
                 <div class="checkbox-inner !h-10 bg-transparent z-50">
                     <div class="checkbox-wrap">
                         <div class="content">
                             <h1>{{ __('DESC') }}</h1>
                         </div>
                         <div class="icon">
                             <div class="active-dot !w-5 !h-5">
                                 <i class="la la-check"></i>
                             </div>
                         </div>
                     </div>
                 </div>
             </label>
             <label class="sandy-big-checkbox w-full -card-radio-focus-label">
                 <input type="radio" class="sandy-input-inner" name="orderby" value="ASC" wire:model.live="$parent.query.orderby">
                 <div class="checkbox-inner !h-10 z-50 bg-transparent">
                     <div class="checkbox-wrap">
                         <div class="content">
                             <h1>{{ __('ASC') }}</h1>
                         </div>
                         <div class="icon">
                             <div class="active-dot !w-5 !h-5">
                                 <i class="la la-check"></i>
                             </div>
                         </div>
                     </div>
                 </div>
             </label>
         </div>

         <button class="yena-button-stack mt-5 w-full" type="button" @click="$dispatch('close')">{{ __('Filter') }}</button>

     </form>
   </div>
</div>