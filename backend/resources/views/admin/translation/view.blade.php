

<div class="w-full">
  <div>
    <div class="card shadow-card bg-white">

     <div class="p-10 shadow-xl rounded-xl mt-0">
      
      
      <div class="font-heading mb-6 pr-2 text-sm font-extrabold uppercase tracking-wider text-zinc-400 flex flex-col md:flex-row md:items-center mb-2">
        <span class="whitespace-nowrap">{{ __('Language') }}</span>
        <div class="border-b border-solid border-gray-300 w-full ml-2 hidden md:flex"></div>
        <div class="flex gap-2 mt-4 md:mt-0">

            <div class="flex z-menuc w-full" data-max-width="600" data-handle=".--control">

                <a class="yena-button-stack !text-sm px-3 --control">
                  {{ __('Auto') }}
  
                  <div class="inline-flex self-center ml-2 shrink-0">
                    <img src="{{ gs('assets/image/logos/google-translate.png') }}" class="w-5 h-5" alt="">
                  </div>
              </a>

                <div class="z-menuc-content-temp">
                    <ul class="z-menu-ul w-40em max-w-full shadow-xl rounded-xl">
                        <div class="p-6">
                          <form action="{{ route('console-admin-languages-post', 'auto_generate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="language" value="{{ $locale }}">
                            <div class="flex items-center gap-4 py-4 mb-0">
                              <div>
                                 <div class="flex w-full items-center justify-between">
                                  <div class="flex-grow">
                                    <h4 class="text-lg font-medium">{{ __('Auto Generate') }}</h4>
                                  </div>
                                  </div>
                                 <div class="border-b border-solid border-gray-300 my-3"></div>
                                 <div class="text-sm font-normal text-gray-500">{{ __('Automatically generate translations using the Google Translate service. This action will overwrite all existing translations. Use caution, or apply this to a new translation file.') }}</div>
                              </div>
                           </div>
                            
                            <button class="yena-button-stack !text-sm !w-full" aria-expanded="false">
                              <div class="--sandy-button-container">
                                  <span>{{ __('Confirm') }}</span>
                              </div>
                            </button>
                          </form>
                        </div>
                    </ul>
                </div>
            </div>
            <form action="{{ route('console-admin-languages-post', 'sync') }}" method="POST">
              @csrf
              <input type="hidden" name="language" value="{{ $locale }}">
              <button class="yena-button-stack !text-sm px-3 !w-full">
                {{ __('Sync') }}
              </button>
            </form>

            <div class="flex z-menuc w-full" data-max-width="600" data-handle=".--control">
                <a class="yena-button-stack !text-sm px-3 --control" aria-expanded="false">
                  <div class="--sandy-button-container">
                      <span>{{ __('Edit') }}</span>
                  </div>
                </a>

                <div class="z-menuc-content-temp">
                    <ul class="z-menu-ul w-40em max-w-full shadow-lg border border-solid border-gray-200 rounded-xl">
                        <div class="p-6">
                            @includeIf('admin.translation.edit')
                        </div>
                    </ul>
                </div>
            </div>

        </div>
      </div>
      <div class="info-box bg-gray-100 rounded-lg px-5 py-6 mb-5">
        <p>
            {{ __('Please preserve in your translations special string variables defined in format as') }}
            <b class="text-theme">:variable</b> {{ __('or') }} <b class="text-theme">{variable}</b>.
        </p>
      </div>
      @php
          $total_count = count($language);
          $translated = 0;
          foreach($language as $key => $value){
            if(!empty($value)) $translated++;
          }
      @endphp
      <div class="info-box bg-gray-100 rounded-lg px-5 py-6 mb-5">
        <p>
            {{ __(':translated of :total translated', [
              'total' => $total_count,
              'translated' => $translated
            ]) }}
        </p>
      </div>
      <form class="sticky top-10 z-10 mb-8 hidden md:block">

        <input type="hidden" name="language" value="{{ $locale }}">
        <div class="form-input relative">
          <input type="text" name="query" placeholder="{{ __('Search Language Translations...') }}" value="{{ request()->get('query') }}">
          
          <button class="absolute right-0 p-5 -top-2">
            <i class="fi fi-rr-search text-lg text-gray-400"></i>
          </button>
        </div>
     </form>

        <hr class="my-7">
        <div class="font-heading mb-6 pr-2 text-sm font-extrabold uppercase tracking-wider text-zinc-400 flex items-center mb-2">
          <span class="whitespace-nowrap"><i class="fi fi-rr-settings"></i></span>
          <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>


          <div class="flex gap-2">
{{-- 
              <div class="flex z-menuc w-full" data-max-width="600" data-handle=".--control">
                <a class="sandy-button --control" aria-expanded="false">
                  <div class="--sandy-button-container">
                      <span>{{ __('New Value') }}</span>
                  </div>
                </a>
    
                <div class="z-menuc-content-temp">
                    <ul class="z-menu-ul w-20em max-w-full shadow-lg border border-solid border-gray-200 rounded-xl">
                        <div class="p-6">
                            @includeIf('admin.translation.new-value')
                        </div>
                    </ul>
                </div>
            </div> --}}

            <x-parts.--delete>
              <x-slot name="content">{{ __('Are you sure you want to delete this language? This action is irreversible.') }}</x-slot>

              <x-slot name="handle">
                <a class="rounded-lg px-2 py-1 text-xs font-medium duration-200 bg-red-400 shadow-xl text-white flex items-center gap-1 --open-delete cursor-pointer">
                  <i class="fi fi-rr-trash"></i>
                  {{ __('Delete') }}
                </a>
              </x-slot>

              <x-slot name="form">
                <form action="{{ route('console-admin-languages-post', 'delete') }}" method="post">
                  @csrf
                  <input type="hidden" name="language" value="{{ $locale }}">
                  <button type="submit" class="first-letter: bg-red-500  text-white disabled:opacity-75 hover:bg-red-400
                         block appearance-none rounded-lg text-sm font-medium duration-100 focus:outline-transparent px-3 py-1.5">
                         <div class="relative flex items-center justify-center ">
                          <div class="duration-100">{{ __('Delete') }}</div>
                        </div>
                  </button>
                </form>
              </x-slot>
            </x-parts.--delete>
          </div>
        </div>

        <form action="{{ route('console-admin-languages-post', 'edit_language') }}" method="post">
          @csrf
          <input type="hidden" name="language" value="{{ $locale }}">
          <div class="sticky top-24 font-heading mb-6 pr-2 text-sm font-extrabold uppercase tracking-wider text-zinc-400 flex items-center mb-2 z-[99]">
            <span class="whitespace-nowrap">{{ __('Values') }}</span>
            <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>


            <div class="flex gap-2">

              <button class="yena-button-stack !text-sm !w-full --black !w-[150px]" aria-expanded="false">
                <div class="flex h-full items-center justify-center">
                    <div class="px-2 py-0.5 text-sm font-medium">{{ __('Save') }}</div>
                </div>
              </button>
            </div>
          </div>
          <div class="info-box bg-red-400 rounded-lg px-5 py-4 text-white text-sm mb-5">
            <p class=" text-white">
                {{ __('We highly recommend you to save the form at each 10-15 mins in order to prevent data loss.') }}
            </p>
          </div>

          @if (empty($language))
               <p class="mt-2 text-sm text-gray-600 flex items-center gap-1"><i class="fi fi-rr-triangle-warning"></i> {{ __('No translation found') }}</p>
          @endif

          <script>
            function _translation(){
              return {
                language: '{{ $locale }}',
                singleTranslate(key, callback){
                  var language = this.language.split('#')[1];

                  var url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl='+ language +'&dt=t&q=' + key;
                  
                  axios.get(url).then(response => {
                      var _r = response.data;
                      var translatedText = _r[0][0][0];

                      return callback(translatedText);
                  });
                },

                generateLanguage(id){
                  var __ = this;
                  var _el = document.getElementById(id);
                  var keyQuery = _el.querySelector('textarea');
                  var key = keyQuery.getAttribute('data-translation-key');

                  __.singleTranslate(key, function(value){
                    keyQuery.innerHTML = value;
                  });
                },
                
                init(){
                  var __ = this;
                  
                  var _all = this.$root.querySelectorAll('.page-trans-auto .language-form-input');

                  _all.forEach(element => {
                    var keyQuery = element.querySelector('textarea');
                    var key = keyQuery.getAttribute('data-translation-key');
                    
                  });
                }
              }
            }
          </script>

          <div x-data="_translation">

            <div class="page-trans-auto">
              
              <div class="page-trans-table flex flex-col gap-8">
                @foreach ($language as $key => $value)
                @php
                    $_id = md5("language::$key");
                @endphp
                <div class="form-input language-form-input {{ $_id }}" id="{{ $_id }}">
                  <label class="text-sm font-bold">{{ $key }}:</label>
                  <textarea name="value[{{ $key }}]" data-translation-key="{{ $key }}">{{ !is_array($value) ? $value : '' }}</textarea>
                  <div class="flex !hidden">
                    <a class="sandy-button --control bg-black block" @click="generateLanguage('{{ $_id }}')">
                      <div class="--sandy-button-container">
                          <span>{{ __('Generate') }}</span>
                          <img src="{{ gs('assets/image/logos/google-translate.png') }}" class="w-5 h-5" alt="">
                      </div>
                    </a>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </form>
      </div>
   </div>
  </div>
 </div>