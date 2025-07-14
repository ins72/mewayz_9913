
<?php

use App\Models\Section;
use App\Yena\Ai\Purify;
use App\Models\SiteAiChatSession;
use App\Models\SiteAiChatHistory;
use function Livewire\Volt\{state, mount, usesFileUploads, updated, on};

// Methods

state([
    'site'
]);

state([
    'session' => [],
    'history' => [],
    'section_id' => 'a3cc6d6a-c645-4917-90f1-ef04c8154a16',
]);

mount(function(){
    $this->get();

    // $n = new SiteAiChatSession;
    // $n->site_id = $this->site->id;
    // $n->started_by = iam()->id;
    // $n->session = \Session::getId();
    // $n->save();
});

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

$get = function(){
    $this->session = SiteAiChatSession::first();
    $this->history = $this->session->history()->get()->toArray();
};

$runAi = function ($message){
    $client = \OpenAI::client(config('app.openai_key'));
    $section = Section::where('uuid', $this->section_id)->first();
    $config = config("yena.sections.$section->section");
    $class = app()->make(ao($config, 'aiClass'));
    $sectionContent['banner'] = [
        'content' => [
            ...$section->content,
        ],
        'settings' => [
            ...$section->settings,
        ],
        'form' => [
            ...$section->form,
        ],
    ];
    $sectionContent = json_encode($sectionContent);

    $data = [];
    $data[] = [
        'role' => 'system',
        'content' => $class->getPrompt(),
    ];
    $history = $this->session->history()->get();
    foreach ($history as $item){
        $msg = $item->role == 'user' ? $item->human : $item->ai;
        $data[] = [
            'role' => $item->role,
            'content' => $msg !== 'OK' ? $msg : '',
        ];
    }
    
    $data[] = [
        'role' => 'user',
        'content' => $message,
    ];

    $result = $client->chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => $data,
        'max_tokens' => 2000,
        'temperature' => 1
    ]);

    return $result->choices[0]->message->content;
};

$saveChat = function($role = 'user', $message = '', $response = []){
    $history = new SiteAiChatHistory;
    $history->session_id = $this->session->uuid;
    $history->site_id = $this->site->id;
    $history->role = $role;
    $column = $role == 'user' ? 'human' : 'ai';
    $history->$column = $message;
    $history->response = $response;
    $history->save();

    $this->get();
};

$saveChato = function($_history = [], $section = []){
    $history = new SiteAiChatHistory;
    $history->fill($_history);
    $history->session_id = $this->session->uuid;
    $history->site_id = $this->site->id;
    $history->response = [$section];
    $history->save();

    $this->get();
};

$chat = function($history, $message = ''){
    $ai = $this->runAi($message);
    // Save ai response
    $section = Section::where('uuid', $this->section_id)->first();
    $config = config("yena.sections.$section->section");
    $class = app()->make(ao($config, 'aiClass'));
    $class->setChat($ai);
    $class->purify();

    $chatResponse = $class->getChatResponse();
    $this->saveChat('assistant', $chatResponse, _ai_convert_to_section($class->sections, $section));

    return _ai_convert_to_section($class->sections, $section);
};

?>
<div class="ai-c-livewire relative" :class="{
    'ai-c-livewire-opened': openChat
}" x-data="__ai_chat_alpine" wire:ignore>

    <div class="builder-chat" x-show="openChat">
        <div class="builder-partner-panel">
           <div class="builder-partner-panel-wrapper">
              <div class="-panel-head">
                <a x-on:click="openChat=false" class="absolute appearance-none select-none top-2 right-4 p-2 text-foreground-500 rounded-full hover:bg-gray-200 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
                    <i class="fi fi-rr-cross-small text-base"></i>
                </a>
              </div>
              <div class="flex flex-col h-full w-[100%] px-6 pb-2">

                 <div class="flex flex-1 flex-col justify-end">
                    <div class="flex flex-col gap-2 pt-12 -chat-body">

                        <input type="text" x-model="message">

                        <a @click="submitAi">CLICIK</a>

                        <template x-for="(item, index) in history" :key="index">
                            <div class="-card" :class="{
                                '-card-left': item.role == 'assistant',
                                '-card-right': item.role == 'user',
                            }">
                                <div class="-card-wrapper pb-2" :style="styles()">
                                    <div class="-card-body" x-text="item.role == 'user' ? item.human : (item.ai !== 'OK' ? item.ai : '')"></div>

                                    {{-- <template x-if="item.role == 'assistant' && item.response && item.response.length > 0">
                                        <div class="page-subpanel-section !h-auto !pb-0 px-3" x-data x-init="item.response" x-intersect="$store.builder.rescaleDiv($root);">
                                            <div class="page-type-options !mb-0 !bg-[var(--yena-colors-whiteAlpha-600)]">
                                                <div class="page-type-item !overflow-y-auto !h-[130px]">
                                                    <div class="container-small edit-board !origin-[0px_0px] !h-full">
                                                        <div class="card">
                                                            <div class="card-body" wire:ignore>
                                                                <template x-for="(_section, index) in item.response" :key="index">
                                                                    <div class="card-body-inner">
                                                                        <div x-bit="'section-' + _section.section" x-data="{section:_section}"></div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <button class="btn !rounded-full" @click="generatePage(template, item)">{{ __('Add Section') }}</button>
                                                    <button class="btn"></button>
                                                </div>
                                            </div>
                                        </div>
                                    </template> --}}
                                </div>
                                
                            </div>
                        </template>

                        <template x-if="loading">
                            <div class="loader-animation-container my-2">
                              <div class="inner-circles-loader"></div>
                           </div>
                        </template>
                    </div>
                 </div>
                 <div class="flex flex-col gap-2 sticky bottom-0 pt-4 pb-2 bg-[var(--yena-colors-gray-50)]"></div>
                 <div class="inline-flex absolute left-4 top-1"></div>
              </div>
           </div>
        </div>
     </div>
    
    @script
        <script>
            Alpine.data('__ai_chat_alpine', () => {
                return {
                    session: @entangle('session'),
                    history: @entangle('history'),
                    section_id: @entangle('section_id').live,
                    loading: false,
                    message: 'How are you',
                    sectionEditing: null,
                    section: [],
                    _section(){
                        let $this = this;
                        this.sections.forEach(element => {
                            if(element.uuid == $this.section_id) {
                                $this.section = element;
                            }
                        });

                        console.log(this.section, this.sections)
                    },

                    submitAi(){
                        let $this = this;
                        $this.loading = true;

                        let history = {
                            session_id: this.session.uuid,
                            role: 'user',
                            human: this.message,
                        };
                        $this.history.push(history);

                        $this.$wire.saveChato(history, this.section);
                        $this.$wire.chat(history, $this.message).then(r => {
                            $this.loading = false;

                            r.forEach(e => {
                                $this.sections.forEach((element, index) => {
                                    if(element.uuid == e.uuid) {
                                        $this.sections[index] = {
                                            ...element,
                                            ...e
                                        };
                                        element = {
                                            ...element,
                                            ...e
                                        };

                                        $this.$dispatch('section::' + element.uuid, element);

                                        console.log(element, e, $this.sections[index])
                                    }
                                });
                            });
                            console.log(r)
                        });
                    },
                    styles(){
                        var site = this.site;
                        return this.$store.builder.generateSiteDesign(site);
                    },

                    sectionEditing(){

                    },

                    init(){
                        this._section();
                    }
                }
            });
        </script>
    @endscript
</div>