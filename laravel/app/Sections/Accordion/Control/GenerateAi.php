<?php

namespace App\Sections\Accordion\Control;
use MarkSitko\LaravelUnsplash\Facades\Unsplash;

class GenerateAi {

    public $site;
    public $section;
    public $generate_image_type;
    public $context = [];

    public function setSite($site){
        $this->site = $site;

        return $this;
    }

    public function setContext($data = []){
        $this->context = $data;
        return $this;
    }

    public function setSection($section){
        $this->section = $section;
        return $this;
    }

    public function setImageType($type = 'unsplash'){
        $this->generate_image_type = $type;
        return $this;
    }

    public function getAi(){
        $client = \OpenAI::client(config('app.openai_key'));
        $category = ao($this->context, 'category');
        $prompt = ao($this->context, 'prompt');
        $items_count = $this->getItems()->count();
        if($items_count == 0) $items_count = 2;

        $messages[] = [
            'role' => 'system',
            'content' => 'You will be provided with user queries. Provide your output in json format with item keys: title and description.
            
            user query category is "'.$category.'"
            context to use "'.$prompt.'"',
        ];

        $context = "";
        if(!empty($c = ao($this->section->content, 'title'))){
            $context = "with the following '$c'";
        }
    
        $messages[] = [
            'role' => 'user',
            'content' => "Generate $items_count content with a title and a long description $context",
        ];
    
        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
        ]);

        $response = json_decode($result->choices[0]->message->content, true);

        if($r = ao($response, 'items')){
            return $r;
        }

        return [];
    }

    public function getItems(){
        $items = $this->section->getItems()->orderBy('id', 'DESC')->get();

        return $items;
    }

    public function generateText(){
        if($this->section->generated_ai) return;

        $ai = $this->getAi();


        foreach($this->getItems() as $key => $item){
            if(!array_key_exists($key, $ai)){
                $item->generated_ai = 1;
                $item->save();

                continue;
            }
            $_ai_content = $ai[$key];

            $content = $item->content;
            $content['title'] = ao($_ai_content, 'title');
            $content['text'] = ao($_ai_content, 'description');

            $item->generated_ai = 1;
            $item->content = $content;
            $item->save();
        }

        $i = true;

        foreach($this->getItems() as $key => $item){
            if(!$item->generated_ai) $i = false;
        }

        if($i){
            $this->section->generated_ai = 1;
            $this->section->save();
        }
    }

    public function generate(){
        $this->generateText();
    }
}