<?php

namespace App\Yena\Ai;

class Purify {
	public $chat;

	public $sections, $chatResponse;


	public function setChat($chat){
		$this->chat = $chat;
		return $this;
	}

	public function emptyChat(){
		$response = [
			'response' => "😔 Sorry, I tried this but it didn't work. Could you try asking in a different way?",
		];

		return $response;
	}

	public function returnEmpty(){

	}

	public function getSections($array){
		$sections = [];
		$sectionsKeys = array_keys(config('yena.sections'));

		if(!empty(ao($array, 'sections')) && is_array(ao($array, 'sections'))){
			
			foreach(ao($array, 'sections') as $section){


				$sections[] = $section;
			}
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

	public function reconstructJson(){
		$response = [
			'chat' => [
				'response' => 'OK'
			]
		];

		if(!isJson($this->chat)){
			$response['chat'] = $this->emptyChat();
		}
		
		// Convert json to array
		$array = json_decode($this->chat, true);

		
		if(!empty(ao($array, 'chat')) && is_array(ao($array, 'chat'))){
			$response['chat'] = $array['chat'];
		}

		$this->chatResponse = $response['chat'];
		$this->sections = $this->getSections($array);
		return $this;
	}

	public function getChatResponse(){
		
		return $this->chatResponse;
	}

	public function convertToSection(){
		$blank = [];
		// $count = count($this->sections) + 1;
		
		foreach($this->sections as $section){
			$section_settings = is_array(ao($section, 'section_settings')) ? ao($section, 'section_settings') : [];
			
			if (is_array(ao($section, 'items')) && count(ao($section, 'items')) > 0) {
				$items = [];
				foreach (ao($section, 'items') as $_i) {
					$newItem = [
						'uuid' => str()->uuid(),
						...$_i
					];

					$items[] = $newItem;
				}

				$section['items'] = $items;
			}

			$new = [
				'uuid' => str()->uuid(),
				'published' => 1,
				// 'position' => $count,
				'settings' => [
					'silence' => 'golden',
				],
				'form' => [
					'email' => 'Email',
					'button_name' => 'Signup',
				],
				...$section,
				'section_settings' => [
					'height' => 'fit',
					'width' => 'fill',
					'spacing' => 'l',
					...$section_settings
				],
			];

			$blank[] = $new;
		}

		return $blank;
	}

	public function response(){

		return $this->reconstructJson();
	}

	public function purify(){



		return $this->response();
	}
}