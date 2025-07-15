<?php

namespace App\Sections\Accordion\Control;

class Prompt {
    public $section;
    public $generate_image_type;
    public $context = [];
    public $prompt = [];

	public $chat;

	public $sections, $chatResponse;

    public function setSection($section){
        $this->section = $section;
        return $this;
    }

	public function setChat($chat){
		$this->chat = $chat;
		return $this;
	}

	public function emptyChat(){
		$response = '😔 Sorry, I tried this but it didn\'t work. Could you try asking in a different way?';

		return $response;
	}

    public function getPrompt(){
        return config('yena.sectionPrompt.accordion');
    }

    // public function setImageType($type = 'unsplash'){
    //     $this->generate_image_type = $type;
    //     return $this;
    // }

    // public function generateUnsplash($category = 'black'){
    //     $response = Unsplash::search()->term($category)->count(30)->toArray();
    //     $results = $response['results'];
        
    //     $k = array_rand($results);
    //     $unsplash = $results[$k];

    //     $image = ao($unsplash, "urls." . config('app.unsplashQuality'));
    //     return $image;
    // }
    
    // public function generateImage(){
    //     if($this->section->generated_ai_image) return;

    //     if(!ao($this->section->settings, 'enable_image')) return;

    //     $category = ao($this->context, 'category');
    //     switch ($this->generate_image_type) {
    //         case 'unsplash':
    //             $image = $this->generateUnsplash($category);
    //             $this->section->image = $image;
    //             $this->section->generated_ai_image = 1;
    //             $this->section->save();
    //         break;
    //     }

    // }

	public function getSections($array){
		return $array;
	}

	public function generateSection(){

	}

	public function getChatResponse(){
		
		return $this->chatResponse;
	}

	public function reconstructJson(){
		$response = [
			'chat' => 'Processing your request...'
		];


		if(!isJson($this->chat)){
			$response['chat'] = $this->chat;
		}

		if(isJson($this->chat)){
		
			// Convert json to array
			$array = json_decode($this->chat, true);
			$this->sections = $this->getSections($array);
		}

		$this->chatResponse = $response['chat'];

        // dd(aiConvertToSection($this->sections), $this->sections, $this->chatResponse, $this->chat);
		return $this;
	}

	public function response(){

		return $this->reconstructJson();
	}

	public function purify(){
		return $this->response();
	}
}