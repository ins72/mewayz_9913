<x-layouts.app>
  <x-slot:title>{{ __('Languages') }}</x-slot>


  <style>
    *{
      scrollbar-width: initial !important;
    }
  </style>

  <div class="h-full pb-16 pt-0 sm:pt-0" x-data>
    <div class="mb-6 ">
      <div class="flex flex-col mb-4">
        <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center">
          <span class="whitespace-nowrap">{{ __('Admin') }}</span>
        </div>
        <div class="border-b border-solid border-gray-300 w-[100%] my-4 flex"></div>
         
         <div class="flex items-center h-6">
            <h2 class="text-lg font-semibold ">
                {!! __icon('--ie', 'language-translate', 'w-6 h-6 inline-block') !!}
                <span class="ml-2">{{ __('Translations') }}</span>
            </h2>
         </div>
         <div class="flex flex-col gap-4 mt-4 lg:flex-row">
            <a @click="$dispatch('open-modal', 'create-translation');" class="cursor-pointer yena-button-stack">
              {{ __('Create Translation') }}
            </a>
         </div>
      </div>
   </div>



    <!-- // -->
    <div class="mx-auto w-full max-w-screen-xl px-0 md:px-0 pb-10 pt-8 flex md:space-x-6 mb-10">
      <div class="hidden md:block">
        <div class="sticky top-0 shadow-xl bg-white rounded-lg p-5 w-15em">
          <div class="font-heading mb-6 px-2 font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center mb-2">
            <span class="whitespace-nowrap">{{ __('Languages') }}</span>
            <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
          </div>
          
          @if (count($languages) > 0)
          <div class="flex flex-col gap-2">
            @foreach ($languages as $item)
            @php
                $it = pathinfo($item);
            @endphp
            <a href="{{ route('dashboard-admin-languages-index', ['language' => ao($it, 'filename'), 'query' => request()->get('query')]) }}" class="relative cursor-pointer flex mb-[1px] items-center gap-3 rounded-md px-2 py-[5px] text-left text-sm font-medium text-zinc-600 duration-100 hover:bg-zinc-200/70 {{ $locale == ao($it, 'filename') ? 'bg-zinc-200/70' : '' }}">
              <label class="text-base font-bold cursor-pointer capitalize text-theme">
                {{ ao($info(ao($it, 'filename')), 'name') }}
              </label>
            </a>
            @endforeach
          </div>
          @else
          <div>
            <p class="mt-2 text-xs text-gray-600 flex items-center gap-1"><i class="fi fi-rr-triangle-warning"></i> {{ __('No language found') }}</p>
          </div>
          @endif
      </div>
      </div>

      @if (is_array($language))
        @includeIf('admin.translation.view')
      @endif

      @if (!is_array($language) && !$language)
          

        <div class="w-full">
          <div>
            <div class="p-10 shadow-xl rounded-xl mt-5">
              <p class="mt-2 text-xs text-gray-600 flex items-center gap-1"><i class="fi fi-rr-triangle-warning"></i> {{ __('No language found') }}</p>
            </div>
          </div>
        </div>
      @endif
    </div>

    
    <template x-teleport="body">
      <x-modal name="create-translation" :show="false" removeoverflow="true" maxWidth="xl" >
        <div class="h-full overflow-y-auto">
          <a @click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
             <i class="fi fi-rr-cross text-sm"></i>
          </a>
       
          <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Language') }}</header>
          <hr class="yena-divider">

          <form class="px-6 pb-6 mt-4" method="post" action="{{ route('dashboard-admin-languages-post', 'create') }}">
             @csrf
       
             <div class="grid grid-cols-1 gap-4 mb-4">
                <div class="form-input">
                  <label>{{ __('Select Locale:') }}</label>
                  <select name="locale">
                    
                      @foreach (Country::google_trans_lang() as $key => $item)
                        <option value="{{ strtolower(ao($item, 'code')) }}">{{ ao($item, 'language') }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-input">
                  <label>{{ __('Language Name:') }}</label>
                  <input type="text" name="name">
                </div>
              </div>

             <button type="submit" class="yena-button-stack mt-0 w-[100%]">
                <div class="relative flex items-center justify-center ">
                   <div class="duration-100 undefined false">{{ __('Save') }}</div>
                </div>
             </button>
          </form>
       </div>
      </x-modal>
    </template>
  </div>
</x-layouts.app>