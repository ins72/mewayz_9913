<div class="pt-[26px] px-[36px]">

    <div class="settings__upload" data-generic-preview>
       <div class="settings__preview">
        <template x-if="!site.logo">
           <div class="default-image p-16 !block">
              {!! __i('--ie', 'image-picture', 'text-gray-300') !!}
           </div>
        </template>
        <template x-if="site.logo">
           <img :src="$store.builder.getMedia(site.logo)" alt="">
        </template>
       </div>
       <div class="settings__wrap">
       <div class="text-[2rem] leading-10 font-bold">{{ __('Profile photo') }}</div>
       <div class="settings__content">{{ __('We recommended an image of at least 80x80. Gifs work too.') }}</div>
       <div class="settings__file" @click="openMedia({
            event: 'sectionMediaEvent:logo',
            sectionBack:'navigatePage(\'__last_state\')'
        });">
          {{-- <input class="settings__input z-50" type="file" wire:model="logo"> --}}
          <a class="yena-button-stack">{{ __('Choose') }}</a>
       </div>
       </div>
    </div>
    <div class="grid grid-cols-2 gap-4 mt-5">
       <x-input-x x-model="site.name" label="{{ __('Display name') }}"></x-input-x>
       <x-input-x x-model="site.location" label="{{ __('Location') }}"></x-input-x>
       <x-input-x x-model="site.bio" label="{{ __('Bio') }}"></x-input-x>
    </div>

    <div class="flex flex-col justify-between normal-case my-5">
        <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Alignment') }}</span>
    </div>
    <div class="grid grid-cols-3 gap-4">
        @foreach (['left' => 'Left', 'center' => 'Center', 'right' => 'Right'] as $key => $value)
        <label class="sandy-big-checkbox is-bio-radius">
            <input type="radio" x-model="site.settings.align" class="sandy-input-inner" name="settings[bio_align]"
                value="{{ $key }}">
            <div class="checkbox-inner !p-3 !h-10 !border-2 !border-dashed !border-color--hover">
                <div class="checkbox-wrap">
                    <div class="content">
                        <h1>{{ __($value) }}</h1>
                    </div>
                    <div class="icon">
                        <div class="active-dot rounded-lg w-5 h-5">
                        <i class="la la-check text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>
        </label>
        @endforeach
    </div>
    
    <div class="flex flex-col justify-between normal-case my-5">
        <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Header text size') }}</span>
    </div>
    

    <div class="flex gap-2">
        <div tabindex="0" role="button" x-on:click="site.settings.header_fontsize = 's'" :class="{
            'border-black text-black': site.settings.header_fontsize == 's',
            'border-gray-400 text-gray-400': site.settings.header_fontsize !== 's'
            }" class="cursor-pointer box-border font-semibold flex items-center justify-center h-12 w-12 border-[2px] border-solid rounded-8">S</div>
        <div tabindex="0" role="button" x-on:click="site.settings.header_fontsize = 'm'" :class="{
            'border-black text-black': site.settings.header_fontsize == 'm',
            'border-gray-400 text-gray-400': site.settings.header_fontsize !== 'm'
            }" class="cursor-pointer box-border font-semibold flex items-center justify-center h-12 w-12 border-[2px] border-solid rounded-8">M</div>
        <div tabindex="0" role="button" x-on:click="site.settings.header_fontsize = 'l'" :class="{
            'border-black text-black': site.settings.header_fontsize == 'l',
            'border-gray-400 text-gray-400': site.settings.header_fontsize !== 'l'
            }" class="cursor-pointer box-border font-semibold flex items-center justify-center h-12 w-12 border-[2px] border-solid rounded-8">L</div>
    </div>
    <div class="flex flex-col justify-between normal-case my-5">
        <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Profile size') }}</span>
    </div>

    <div class="input-group flex-[70%] flex relative">
        <input type="range" class="input-small range-slider appearance-none h-[calc(.625rem_*_4)] outline-[none] p-0 overflow-hidden rounded-[calc(.625rem_/_2)] text-[14px] w-[100%] block bg-[unset] text-[color:#111] border border-solid border-[#eee] leading-[1.6] relative [box-shadow:none]" min="40" max="200" step="1" x-model="site.settings.avatar_size">
        <p class="absolute top-[10px] right-[12px] text-[14px] leading-[1.6] pointer-events-none capitalize" x-text="site.settings.avatar_size ? site.settings.avatar_size + 'px' : ''"></p>
    </div>

</div>