<?php

namespace App\Sections\Banner\Control;
use MarkSitko\LaravelUnsplash\Facades\Unsplash;

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
        return config('yena.sectionPrompt.banner');
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
		$sections = [];
		$sectionsKeys = array_keys(config('yena.sections'));

		if(!empty(ao($array, 'banner')) && is_array(ao($array, 'banner'))){
            $sections[] = [
                'banner' => ao($array, 'banner'),
            ];
		}
		foreach($sections as $key => $section){
			// Reconstruct again
			// Check if key is part of section texts;
			$new = [];
			if(in_array(array_key_first($section), $sectionsKeys)){
				$new = [
					'section' => array_key_first($section),
					...$section[array_key_first($section)],
				];
			}

			$sections[$key] = $new;
		}

		return $sections;
	}

	public function getChatResponse(){
		
		return $this->chatResponse;
	}

	public function reconstructJson(){
		$response = [
			'chat' => 'Processing your request...'
		];

        dd($this->chat);

		if(!isJson($this->chat)){
			$response['chat'] = $this->emptyChat();
		}
		
		// Convert json to array
		$array = json_decode($this->chat, true);

		if(!empty(ao($array, 'chat')) && is_array(ao($array, 'chat'))){
			// $response['chat'] = $this->emptyChat();
		}
		
		if(!empty(ao($array, 'chat')) && !is_array(ao($array, 'chat'))){
			$response['chat'] = $array['chat'];
		}

		$this->chatResponse = $response['chat'];
		$this->sections = $this->getSections($array);

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