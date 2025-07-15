
<?php
   use App\Yena\Languages;
   use App\Models\Site;
   use App\Livewire\Actions\ToastUp;
   use function Livewire\Volt\{state, mount, placeholder, updated, uses, on};

   uses([ToastUp::class]);
   mount(function(){
      
   });

   state([
      'staticLanguages' => fn() => Languages::data(),
      'languages' => function(){
         $all = allLocale();
         $array = [];
         foreach($all as $key => $value){
            $pathinfo = pathinfo($value);
            $e = explode('#', ao($pathinfo, 'filename'));
            $shortLang = null;
            if(!empty($e[1])) $shortLang = $e[1];

            $languages = $this->staticLanguages;

            $array[$key] = [
               'name' => $e[0],
               'filename' => ao($pathinfo, 'filename'),
               'language' => ao($languages, $shortLang),
            ];
         }

         return $array;
      },
      'selectedLanguage' => fn () => app()->getLocale(),
   ]);

   mount(function(){
      // dd($this->languages);
      // $this->getSites();
   });

   placeholder('
   <div class="p-5 w-[100%]">
      <div class="--placeholder-skeleton w-[100%] h-[60px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-[100%] h-[60px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-[100%] h-[60px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');


   $changeLanguage = function($lang){
      
      \Cookie::queue('yenaLocale', $lang, time() + 60 * 60 * 24 * 365);

      $this->js('location.reload();');
   };
?>


<div class="w-full">
   <div class="flex flex-col" x-data="language_modal">
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross text-sm"></i>
      </a>

      <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Change language') }}</header>

      <hr class="yena-divider">

      <form wire:submit="changeLanguage(selectedLanguage)" class="px-8 pt-4 pb-6">
         <div class="flex flex-col gap-6">
            <div class="yena-button-o !text-base !font-semibold !bg-white !border !border-solid !border-[var(--yena-colors-gray-200)] w-[100%] !h-10 !px-4 ![box-shadow:var(--yena-shadows-md)] active:![box-shadow:var(--yena-shadows-inner)] !justify-between" x-tooltip="tippy">
               <span x-text="getLanguageName(selectedLanguage)" class="capitalize"></span>
               <span class="--icon ml-2 !mr-0">
                   <i class="ph ph-caret-down"></i>
               </span>
            </div>
         </div>

         @php
            $error = false;

            if(!$errors->isEmpty()){
                  $error = $errors->first();
            }
         @endphp
         @if ($error)
            <div class="mt-4 bg-red-200 text-[11px] p-1 px-2 rounded-md">
                  <div class="flex items-center">
                     <div>
                        <i class="fi fi-rr-cross-circle flex text-xs"></i>
                     </div>
                     <div class="flex-grow ml-1 text-xs">{{ $error }}</div>
                  </div>
            </div>
         @endif

         <button class="yena-button-stack mt-5 w-full active:![box-shadow:var(--yena-shadows-inner)]">{{ __('Save') }}</button>
      </form>


      
   
      <template x-ref="translate_template">
         <div class="yena-menu-list !w-[300px] md:!w-[400px] !max-w-full z-[9999]">
            <template x-for="(item, index) in languages" :key="index">
                <a @click="selectedLanguage = item.filename" :class="{
                    '!bg-[var(--yena-colors-gray-100)] !border-[var(--yena-colors-gray-200)]': selectedLanguage == item.filename,
                }" class="yena-menu-list-item">

                  <div class="inline-flex items-center justify-center flex-shrink-0 text-[0.8em] opacity-100 mr-2" :class="{
                     'opacity-0': selectedLanguage !== item.filename,
                     'opacity-100': selectedLanguage == item.filename
                  }">
                     <i class="ph ph-check text-lg"></i>
                  </div>
                  <div class="flex-1">
                     <div class="flex items-center">
                        <div class="flex flex-col">
                           <span class="text-base capitalize" x-text="item.name"></span>
                           <span class="text-xs text-[var(--yena-colors-gray-500)] capitalize" x-text="getStaticLanguageName(item.filename)"></span>
                        </div>
                     </div>
                  </div>
                </a>
            </template>
         </div>
      </template>
   </div>


   
   @script
   <script>
       Alpine.data('language_modal', () => {
          return {
            languages: @entangle('languages'),
            selectedLanguage: @entangle('selectedLanguage'),
            staticLanguages: @entangle('staticLanguages'),
            tippy: {
                allowHTML: true,
                maxWidth: 400,
                interactive: true,
                trigger: 'click',
                animation: 'scale',
                placement: 'bottom',
            },

            getStaticLanguageName(lang){
               lang = this.getLanguage(lang);
               let split = lang.split('#');

               return this.staticLanguages[split[1]];
            },

            getLanguage(lang){
               let $this = this;
               let split = lang.split('#');
               
               if(!split[1]){
                  return $this.getLanguage($this.languages[0].filename);
               }

               return lang;
            },

            getLanguageName(lang){
               let $this = this;

               lang = $this.getLanguage(lang);
               let split = lang.split('#');
               let name = null;

               $this.languages.forEach(language => {
                  if(language.filename == lang){
                     name = language.name;
                  }
               });

               return name;
            },
            init(){
               var $this = this;
               this.tippy.appendTo = this.$root;
               this.tippy.content = this.$refs.translate_template.innerHTML;

               // console.log(this.$refs.translate_template)
            }
          }
       });
   </script>
   @endscript
</div>