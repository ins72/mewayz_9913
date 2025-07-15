
<?php

use function Livewire\Volt\{state, mount, usesFileUploads, updated, on};

// Methods

$openAiGenerate = function($context = '', $query = ''){
    $client = \OpenAI::client(config('app.openai_key'));
    $messages = [];
    $temp = 0.5;
    $tokens = 1000;

    // $messages[] = [
    //     'role' => 'system',
    //     'content' => 'This is not interactive, generate only response. Dont act like a chatbot or generate response like a chatbot. Just generate response to the answer asked without starting with "sure", "certainly", "here is your response". Limit your response to no more than 200 characters, but make sure to construct complete sentences. If content is not enough to generate a reasonable response then respond "help"'
    // ];

    if(config("yena.textareaPrompt.$query")){
        $config = config("yena.textareaPrompt.$query");

        if(!is_array($config)) $query = config("yena.textareaPrompt.$query");

        if(is_array($config)){
            $query = ao($config, 'query');

            if(ao($config, 'temprature')){
                $temp = ao($config, 'temprature');
            }
            if(ao($config, 'tokens')){
                $tokens = ao($config, 'tokens');
            }
        }
    }

    $text = str_replace('[text]', $context, $query);
    $tokens = (int) $tokens;

    // Str::contains($haystack, 'needles')
    // dd($context, $query, $text);

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
        'temperature' => $temp,
        'max_tokens' => $tokens,
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

?>
<div class="openai-livewire"></div>