
<?php

use function Livewire\Volt\{state, mount, usesFileUploads, updated, on};

// Methods

$openAiGenerate = function($context = '', $query = ''){
    $client = \OpenAI::client(config('app.openai_key'));
    $messages = [];

    $messages[] = [
        'role' => 'system',
        'content' => 'This is not interactive, generate only response. Dont act like a chatbot or generate response like a chatbot. Just generate response to the answer asked without starting with "sure", "certainly", "here is your response". Limit your response to no more than 200 characters, but make sure to construct complete sentences. If content is not enough to generate a reasonable response then respond "help"'
    ];

    // $messages[] = [
    //     'role' => 'system',
    //     'content' => "This is the context to work with '$context'",
    // ];

    $messages[] = [
        'role' => 'user',
        'content' => "Content to work with '$context'"
    ];
    $messages[] = [
        'role' => 'user',
        'content' => $query
    ];
    
    $result = $client->chat()->createStreamed([
        'model' => 'gpt-3.5-turbo',
        'messages' => $messages,
    ]);

    $content = '';
    foreach ($result as $result) {
        $_array = $streamContent = $result->choices[0]->toArray();
        if(ao($_array, 'delta.content')){
            $streamContent = $_array['delta']['content'];
            $this->stream(
                to: 'textareaSuggest',
                content: $streamContent,
                replace: false,
            );
        }
    }
};


$generate = function($event, $context = ''){
    $query = 'hiii, how are you?';
    $client = \OpenAI::client(config('app.openai_key'));
    $messages = [];

    $messages[] = [
        'role' => 'system',
        'content' => 'This is not interactive, generate only response. Dont act like a chatbot or generate response like a chatbot. Just generate response to the answer asked without starting with "sure", "certainly", "here is your response". Limit your response to no more than 200 characters, but make sure to construct complete sentences.'
    ];

    // $messages[] = [
    //     'role' => 'system',
    //     'content' => "This is the context to work with '$context'",
    // ];

    $messages[] = [
        'role' => 'user',
        'content' => "Content to work with '$context'"
    ];
    $messages[] = [
        'role' => 'user',
        'content' => $query
    ];
    
    $result = $client->chat()->createStreamed([
        'model' => 'gpt-3.5-turbo',
        'messages' => $messages,
    ]);

    // dd($result);

    $content = '';
    foreach ($result as $result) {
        $_array = $streamContent = $result->choices[0]->toArray();

        // $this->js('heyy');
        // $this->js('console.log("'.$_array['delta']['content'].'")');
        if(ao($_array, 'delta.content')){
            $streamContent = $_array['delta']['content'];
            // $this->dispatch($event, ['text' => $streamContent]);
            // $this->js("heyy = '$streamContent'; console.log('$streamContent')");

            // echo $streamContent;

            $this->stream(
                to: ''.$event.'',
                content: $streamContent,
                replace: false,
            );
        }
    }
};


$runAi = function ($data, $stream = ''){
    $client = \OpenAI::client(config('app.openai_key'));
    $results = $client->chat()->createStreamed([
        'model' => 'gpt-3.5-turbo',
        'messages' => $data,
    ]);

    foreach ($results as $key => $result) {
        $_array = $result->choices[0]->toArray();
        $streamContent = ao($_array, 'delta.content');
        if(ao($_array, 'finish_reason') == 'stop'){
            $streamContent = '--ai-end';
        }

        if(!ao($_array, 'delta.content') && ao($_array, 'finish_reason') !== 'stop'){
            $streamContent = '--ai-start ';
        }

        // if(ao($_array, 'finish_reason') !== 'stop'){}
        $this->stream(
            to: ''.$stream.'',
            content: $streamContent,
            replace: false,
        );
    }

    return $result;
};

$_section_ai = function($event, $section, $prompt, $method){
    $_section = ao($section, 'section');
    $config = config("yena.sections.$_section");

    $class = app()->make(ao($config, 'ai'));


    $messages = [];
    $messages[] = [
        'role' => 'system',
        'content' => "Provide concise answers without additional explanations or apologies. Give me the information directly without any introductory sentences. Exclude any extra wording and just provide the essential answer. {$class->getBase()}",
    ];
    // $messages[] = [
    //     'role' => 'user',
    //     'content' => "Provide a JavaScript solution to create a dynamic countdown timer for an upcoming product launch event."
    // ];

    if(method_exists($class, $method)){
        $messages[] = [
            ...$class->{$method}(),
        ];
    }

    $this->runAi($messages, $event);


    // dd($messages);


    return;

    // dd($section);
    $_section = ao($section, 'section');
    $sectionAi = config("yena.aiSectionPrompt.$_section");
    $messages = [];

    $messages[] = [
        'role' => 'system',
        'content' => ao($sectionAi, 'base'),
    ];

    foreach ($sectionAi as $key => $value) {
        if($key == 'base') continue;

        // $messages[] = [
        //     'role' => 'user',
        //     'content' => "Ai must follow the following instructions '$value' for $key"
        // ];
    }

    $messages[] = [
        'role' => 'user',
        'content' => "You must use a casual tone"
    ];
    
    $messages[] = [
        'role' => 'user',
        'content' => "Provide a JavaScript solution to create a dynamic countdown timer for an upcoming product launch event."
    ];

    $messages[] = [
        'role' => 'user',
        'content' => "You must respond in english"
    ];

    $messages[] = [
        'role' => 'user',
        'content' => "You must generate a mid to long subtitle"
    ];
    
    $this->runAi($messages, $event);
};

?>
<div class="ai-livewire" x-data="__ai_alpine" wire:ignore>
    {{-- <span wire:stream="aiTextStream" x-ref="aiStream" class="h-[10rem] block fixed z-[99] text-lg" contenteditable="true"></span> --}}


    <div class="alpine-o"></div>
    
    @script
        <script>
            Alpine.data('__ai_alpine', () => {
                return {
                    heyy: 'booo',

                    event: null,

                    _section(event, section, prompt, method){
                        let l = Livewire.hook('stream', ({ content }) => {
                            console.log(content)

                            if(content === '--ai-end'){
                                return false;
                            }
                        });

                        // i();
                        console.log(l)
                        return this.$wire._section_ai(event, section, prompt, method);
                    },

                    _generate(event){
                        this.event = event;
                        // this.heyy = null;
                        
                        this.$wire.generate(event);
                        Livewire.hook('stream', ({ content }) => {
                            // console.log(content)
                        });
                        // window.addEventListener('stream', (event) => {
                        //     console.log(event, 'zzzz')
                        // })
                        // this.$wire.on('stream', ({ postId }) => {
                        //     console.log(postId, 'zzz')
                        // });
                    },

                    init(){
                    }
                }
            });
        </script>
    @endscript
</div>