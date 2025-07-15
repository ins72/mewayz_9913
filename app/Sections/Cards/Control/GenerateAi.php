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


    public function textAi($prompt = []){
        $client = \OpenAI::client(config('app.openai_key'));
        $category = ao($this->context, 'category');
        $prompt = ao($this->context, 'prompt');
        $messages[] = [
            'role' => 'system',
            'content' => 'You will be provided with user queries. Provide your output in json format with the keys: title and subtitle.
            
            user query category is "'.$category.'"
            context to use "'.$prompt.'"',
        ];
    
        $messages[] = [
            'role' => 'user',
            'content' => "Generate banner short title and a mid long subtitle",
        ];
    
        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
        ]);

        $response = json_decode($result->choices[0]->message->content, true);

    }

    public function generateUnsplash($category = 'black'){
        $response = Unsplash::search()->term($category)->count(30)->toArray();
        $results = $response['results'];
        
        $k = array_rand($results);
        $unsplash = $results[$k];

        $image = ao($unsplash, "urls." . config('app.unsplashQuality'));
        return $image;
    }
    
    public function generateAi(){
        
    }

    public function generateImage(){
        if($this->section->generated_ai_image) return;

        if(!ao($this->section->settings, 'enable_image')) return;

        $category = ao($this->context, 'category');
        switch ($this->generate_image_type) {
            case 'unsplash':
                $image = $this->generateUnsplash($category);
                $this->section->image = $image;
                $this->section->generated_ai_image = 1;
                $this->section->save();
            break;
        }

    }

    public function generateText(){
        if($this->section->generated_ai) return;

        $client = \OpenAI::client(config('app.openai_key'));
        $category = ao($this->context, 'category');
        $prompt = ao($this->context, 'prompt');
        $messages[] = [
            'role' => 'system',
            'content' => 'You will be provided with user queries. Provide your output in json format with the keys: title and subtitle.
            
            user query category is "'.$category.'"
            context to use "'.$prompt.'"',
        ];
    
        $messages[] = [
            'role' => 'user',
            'content' => "Generate banner short title and a mid long subtitle",
        ];
    
        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
        ]);

        $response = json_decode($result->choices[0]->message->content, true);

        $content = $this->section->content ?? [];
        $content['title'] = ao($response, 'title');
        $content['subtitle'] = ao($response, 'subtitle');
    
        $this->section->generated_ai = 1;
        $this->section->content = $content;
        $this->section->save();
    }

    public function generate(){
        $this->generateText();
        $this->generateImage();
    }
}