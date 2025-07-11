@props([
    'disabled' => false,
])

<div x-data="builder_textarea_component" class="w-[100%]">


    <div class="flex flex-col relative">
        <textarea {!! $attributes !!}></textarea>

        {{-- <textarea name="" wire:stream="textareaSuggest" @input="console.log($event.target.value)" cols="30" rows="10"></textarea> --}}

        {{-- <textarea name="" wire:stream="textareaSuggest" cols="30" rows="10"></textarea> --}}
        {{-- <a @click="changeTextarea('generate heading')">zzz</a> --}}
        <span wire:stream="textareaSuggest" class="aiSuggestionElement hidden" contenteditable="true"></span>
        
        <div class="absolute right-0 bottom-0">
            <template x-ref="template">
                <div>
                    <form>
                      <div class="input-box">
                         <input type="text" placeholder="{{ __('Ask ai something to edit') }}" class="input-small search-input !bg-white !pr-[2.5rem]" x-model="query">
                         <div class="input-icon absolute top-[0] right-[0] !w-[40px] !h-[40px] items-center justify-center flex cursor-pointer bg-white" :class="{
                            '!hidden': query === '' || query === null,
                         }" @click="changeTextarea(query)">
                            <span :class="{
                             '!hidden': loading
                            }">
                            {!! __i('--ie', 'send-fasr-paper-plane', '!text-black') !!}
                            </span>
             
                            
                            <span class="block" :class="{
                             '!hidden': !loading
                            }">
                                <span class="loader-o20 !text-[9px] !text-black mx-auto block"></span>
                            </span>
                         </div>
                      </div>
                    </form>
                </div>
               <div class="yena-menu-list !w-full mt-1">
                  {{-- <div class="px-4">
                     <p class="yena-text">{{ __('Ask Ai') }}</p>
         
                     <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">Created September 18th, 2023</p>
                     <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">by Jeff Jola</p>
                  </div>
         
                  <hr class="--divider"> --}}
         
                  <a @click="changeTextarea('Please review and enhance the clarity, coherence, and overall effectiveness of the following text: \'[text]\' Aim for concise language and clear structure.')" class="yena-menu-list-item">
                     <div class="--icon">
                        {!! __icon('emails', 'Pen, Edit, Write', 'w-4 h-4') !!}
                     </div>
                     <span class="text-sm">{{ __('Improve writing') }}</span>
                  </a>
                  <div>
                    <a x-tooltip="tippyTone" class="yena-menu-list-item">
                      <div class="--icon">
                         {!! __icon('--ie', 'chat-voice-rec', 'w-4 h-4') !!}
                      </div>
                      <span class="text-sm">{{ __('Change Tone') }}</span>
                    </a>
                  </div>
                  <a @click="changeTextarea('make:grammar')" class="yena-menu-list-item">
                    <div class="--icon">
                       {!! __icon('--ie', 'checkbox.1', 'w-4 h-4') !!}
                    </div>
                    <span class="text-sm">{{ __('Fix grammer') }}</span>
                 </a>
                 <div>
                   <a x-tooltip="tippyTranslate" class="yena-menu-list-item">
                     <div class="--icon">
                        {!! __icon('--ie', 'chat-voice-rec', 'w-4 h-4') !!}
                     </div>
                     <span class="text-sm">{{ __('Translate') }}</span>
                   </a>
                 </div>
                 <a @click="changeTextarea('make:longer')" class="yena-menu-list-item">
                   <div class="--icon">
                      {!! __icon('--ie', 'chart-square.2', 'w-4 h-4') !!}
                   </div>
                   <span class="text-sm">{{ __('Make longer') }}</span>
                </a>
                 <a @click="changeTextarea('make:shorter')" class="yena-menu-list-item">
                    <div class="--icon">
                       {!! __icon('--ie', 'chart-square.3', 'w-4 h-4') !!}
                    </div>
                    <span class="text-sm">{{ __('Make shorter') }}</span>
                 </a>
                 <a @click="changeTextarea('make:emoji')" class="yena-menu-list-item">
                    <div class="--icon">
                       {!! __icon('--ie', 'stiker-smile.1', 'w-4 h-4') !!}
                    </div>
                    <span class="text-sm">{{ __('Add Emoji') }}</span>
                 </a>
                 <a @click="changeTextarea('make:random')" class="yena-menu-list-item">
                    <div class="--icon">
                       {!! __icon('--ie', 'magic-wand.1', 'w-4 h-4') !!}
                    </div>
                    <span class="text-sm">{{ __('Random Text') }}</span>
                 </a>
              </div>
            </template>
            <button type="button" class="yena-button-o !px-2 !bg-gray-100 !rounded-none !h-[36px] !w-[36px]" x-tooltip="tippy">
               <span class="--icon !mr-0" :class="{
                '!hidden': loading
               }">
                {!! __i('Photo Edit', 'Magic Wand, Photo, Edit', 'w-5 h-5 !text-black') !!}
               </span>

               
               <span class="block" :class="{
                '!hidden': !loading
               }">
                   <span class="loader-o20 !text-[9px] !text-black mx-auto block"></span>
               </span>
            </button>
         </div>

    </div>

    
    <template x-ref="tone_template">
        <div class="yena-menu-list !w-full">
            <a @click="changeTextarea('Make tone professional')" class="yena-menu-list-item">
               <div class="--icon">
                  {!! __icon('School, Learning', 'Graduate Hat', 'w-4 h-4') !!}
               </div>
               <span class="text-sm">{{ __('Professional') }}</span>
            </a>
            <a @click="changeTextarea('Make tone casual')" class="yena-menu-list-item">
               <div class="--icon">
                  {!! __icon('Fast Food Drink', 'coffee-cup', 'w-4 h-4') !!}
               </div>
               <span class="text-sm">{{ __('Casual') }}</span>
            </a>
            <a @click="changeTextarea('Make tone confident')" class="yena-menu-list-item">
               <div class="--icon">
                {!! __icon('Smileys', 'Smileys.4', 'w-4 h-4') !!}
               </div>
               <span class="text-sm">{{ __('Confident') }}</span>
            </a>
            <a @click="changeTextarea('Make tone straightforward')" class="yena-menu-list-item">
               <div class="--icon">
                  {!! __icon('Sport, Fitness', 'darts', 'w-4 h-4') !!}
               </div>
               <span class="text-sm">{{ __('Straightforward') }}</span>
            </a>
            <a @click="changeTextarea('Make tone friendly')" class="yena-menu-list-item">
               <div class="--icon">
                  {!! __icon('Smileys', 'Smileys.5', 'w-4 h-4') !!}
               </div>
               <span class="text-sm">{{ __('Friendly') }}</span>
            </a>
       </div>
    </template>

    <template x-ref="translate_template">
      <div class="yena-menu-list !w-full">
         <a @click="changeTextarea('Translate to english')" class="yena-menu-list-item">
            <div class="--icon">🇬🇧</div>
            <span class="text-sm">{{ __('English') }}</span>
         </a>
         <a @click="changeTextarea('Translate to spanish')" class="yena-menu-list-item">
            <div class="--icon">🇪🇸</div>
            <span class="text-sm">{{ __('Spanish') }}</span>
         </a>
         <a @click="changeTextarea('Translate to french')" class="yena-menu-list-item">
            <div class="--icon">🇫🇷</div>
            <span class="text-sm">{{ __('French') }}</span>
         </a>
         <a @click="changeTextarea('Translate to german')" class="yena-menu-list-item">
            <div class="--icon">🇩🇪</div>
            <span class="text-sm">{{ __('German') }}</span>
         </a>
         <a @click="changeTextarea('Translate to hindi')" class="yena-menu-list-item">
            <div class="--icon">🇪🇸</div>
            <span class="text-sm">{{ __('Hindi') }}</span>
         </a>
         <a @click="changeTextarea('Translate to chinese')" class="yena-menu-list-item">
            <div class="--icon">🇨🇳</div>
            <span class="text-sm">{{ __('Chinese') }}</span>
         </a>
         <a @click="changeTextarea('Translate to russian')" class="yena-menu-list-item">
            <div class="--icon">🇷🇺</div>
            <span class="text-sm">{{ __('Russian') }}</span>
         </a>
         <a @click="changeTextarea('Translate to italian')" class="yena-menu-list-item">
            <div class="--icon">🇮🇹</div>
            <span class="text-sm">{{ __('Italian') }}</span>
         </a>
         <a @click="changeTextarea('Translate to japanese')" class="yena-menu-list-item">
            <div class="--icon">🇯🇵</div>
            <span class="text-sm">{{ __('Japanese') }}</span>
         </a>
         <a @click="changeTextarea('Translate to vietnamese')" class="yena-menu-list-item">
            <div class="--icon">🇻🇳</div>
            <span class="text-sm">{{ __('Vietnamese') }}</span>
         </a>
     </div>
    </template>
    @script
    <script>
        
        Alpine.data('builder_textarea_component', () => {
            return {
                //sections: this.sections,
                tippy: {
                    allowHTML: true,
                    maxWidth: 360,
                    interactive: true,
                    trigger: 'click',
                    animation: 'scale',
                    placement: 'bottom',
                },

                tippyTone: {},
                tippyTranslate: {},

                query: '',
                changeInterval: null,
                loading: false,

                changeTextarea(query){
                    let $this = this;
                    let $element = $this.$root.querySelector('textarea');
                    let $outputElement = $this.$root.querySelector('.aiSuggestionElement');
                    // $element.setAttribute('wire:stream','textareaSuggest');

                    $this.loading=true;


                    let $context = $element.value;
                    $outputElement.innerText = '';

                    $this.openAi($context, query).then(r => {
                        setTimeout(() => {
                            clearInterval($this.changeInterval)
                            $this.loading=false;
                        }, 1000);
                    });

                    // $element.value = '';
                    // Alpine.bind(this.$root.querySelector('.aiSuggestionElement'), {'@input': function(){
                    //     console.log(this.$event.target.value)
                    // }});

                    $this.changeInterval = setInterval(function() {
                        let $text = $outputElement.innerText;
                        if($text){
                            $element.value = $outputElement.innerText;
                            $element.dispatchEvent(new Event('input', { bubbles: true }));
                        }

                        if($text && $text == $element.value){
                            // $outputElement.innerText='';
                            // clearInterval($this.changeInterval)
                        }
                    }, 100);
                },


                openAi(context, query){
                    // let query = 'generate 5 name';
                    let element = document.querySelector('.openai-livewire');
                    let $wire = Livewire.find(element.getAttribute('wire:id'))


                    return $wire.openAiGenerate(context, query);
                },
                set(path, value) {
                    var schema = this;  // a moving reference to internal objects within obj
                    var pList = path.split('.');
                    var len = pList.length;
                    for(var i = 0; i < len-1; i++) {
                        var elem = pList[i];
                        if( !schema[elem] ) schema[elem] = {}
                        schema = schema[elem];
                    }

                    schema[pList[len-1]] = value;
                },
                init(){
                    this.tippy.appendTo = this.$root;
                    this.tippy.content = this.$refs.template.innerHTML;

                    this.tippyTone = {
                        ...this.tippy,
                        trigger: 'click',
                        content: this.$refs.tone_template.innerHTML,
                    }
                    this.tippyTranslate = {
                        ...this.tippy,
                        trigger: 'click',
                        content: this.$refs.translate_template.innerHTML,
                    }
                    // Alpine.bind(this.$root.querySelector('textarea'), {
                    //     'wire:stream': 'textareaSuggest'
                    // });
                }
            }
        });
    </script>
    @endscript
</div>
