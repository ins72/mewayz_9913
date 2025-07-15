
<?php

use App\Yena\Pexels\Clients\PhotoApiClient;
use App\Yena\Pexels\Facades\Pexels;
use App\Yena\YenaStream;
use function Livewire\Volt\{state, mount, usesFileUploads, updated, on};

// Methods

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

$generateUnsplash = function($search){
    $response = Unsplash::search()->term($search)->count(30)->toArray();
    $results = $response['results'];
    
    $k = array_rand($results);
    $unsplash = $results[$k];

    $image = ao($unsplash, "urls." . config('app.unsplashQuality'));
    return $image;
};
$generatePexels = function($search){

        
    $client = new PhotoApiClient();
    $client = Pexels::photos();

    $response = $client->search($search);

    $results = collect($response->photos)->all();
    $k = array_rand($results);
    $pic = $results[$k];

    $quality = config('app.pexelsQuality');

    $image = null;
    try {
        $image = $pic->src->{$quality};
    } catch (\Throwable $th) {
        //throw $th;
    }

    return $image;
};
$generate_image = function($query = '', $source = 'unsplash'){

    $image = null;
    switch ($source) {
        case 'unsplash':
            $image = $this->generateUnsplash($query);
        break;
        case 'pexels':
            $image = $this->generatePexels($query);
        break;
    }


    return $image;
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
                        // let l = Livewire.hook('stream', ({ content }) => {
                        //     console.log(content)

                        //     if(content === '--ai-end'){
                        //         return false;
                        //     }
                        // });

                        // // i();
                        // console.log(l)
                        
                        // return this.$wire._section_ai(event, section, prompt, method);
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