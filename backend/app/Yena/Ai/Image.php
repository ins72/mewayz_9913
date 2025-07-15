<?php

namespace App\Yena\Ai;

use App\Models\SitesUpload;

class Image {
	public $prompt;
	public $image = null;
    public $site;

    public function setSite($site){
        $this->site = $site;

        return $this;
    }

	public function setPrompt($prompt){
		$this->prompt = $prompt;

		return $this;
	}

	public function create(){
		$client = \OpenAI::client(config('app.openai_key'));
		$response = $client->images()->create([
			'model' => 'dall-e-3',
			'n' => 1,
			'prompt' => $this->prompt,
			'size' => '1024x1024',
			'response_format' => 'url',
		]);

		$image = null;
		
		foreach ($response->data as $data) {
			$this->image = $data->url;
			$file = 'base64_'. md5(\Carbon\Carbon::now()->toDateTimeString());
			$image = "$file.png";

			$upload = new SitesUpload;
			$upload->site_id = $this->site->id;
			$upload->is_ai = 1;
			$upload->temp_ai_url = $data->url;
			$upload->size = 1000;
			$upload->name = basename($image);
			$upload->path = $data->url;
			$upload->save();
		}
	}

	public function save(){
		// $filesystem = sandy_filesystem('media/site/images');

		// \Storage::disk($filesystem)->put("media/site/images/$image", file_get_contents($url));
		// $size = storageFileSize('media/site/images', $image);
	 
		// $upload = SitesUpload::find($upload_id);
		// $upload->size = $size;
		// $upload->name = basename($image);
		// $upload->path = $image;
		// $upload->ai_uploaded = 1;
		// $upload->save();
		// $this->getAiMedia();
	 
		// $this->mediaTotalSize = $this->site->getUploadedSizesMB();

	}

	public function generate(){
		$this->create();

		return $this->image;
	}
}