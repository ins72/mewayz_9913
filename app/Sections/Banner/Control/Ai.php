<?php

namespace App\Sections\Banner\Control;
use MarkSitko\LaravelUnsplash\Facades\Unsplash;

class Ai {

    public $site;
    public $section;
    public $generate_image_type;
    public $context = [];
    public $prompt = [];

    public function setSite($site){
        $this->site = $site;

        return $this;
    }

    public function setContext($data = []){
        $this->context = $data;
        return $this;
    }

    public function setPrompt($data = []){
        $this->prompt = $data;
        return $this;
    }

    public function setSection($section){
        $this->section = $section;
        return $this;
    }
    
    public function getBase(){

        return 'You will generate content based on the user inputed content.';
    }
    
    public function generateTitle(){

        return [
            'role' => 'user',
            'content' => "You must generate a short to mid title"
        ];
    }

    public function generateSubtitle(){

        return [

        ];
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
        // if($this->section->generated_ai) return;

        $client = \OpenAI::client(config('app.openai_key'));
        $category = ao($this->context, 'category');
        $prompt = ao($this->context, 'prompt');

        $messages = [
            ...$this->prompt
        ];
        $messages[] = [
            'role' => 'system',
            'content' => 'Provide your output in json format with the keys: title and subtitle.'
        ];
        
        $messages[] = [
            'role' => 'user',
            'content' => "Generate only based on this content $prompt",
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