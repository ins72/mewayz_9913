<?php

namespace App\Http\Controllers\Console\Builder;

// use OpenAI;
use App\Models\Section;
use App\Yena\YenaStream;
use Illuminate\Http\Request;
use Orhanerday\OpenAi\OpenAi;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiController extends Controller{

    public function ai(Request $request){
        $client = new OpenAi(config('app.openai_key'));
        $section = Section::where('uuid', $request->get('section_id'))->first();
        
        $config = config("yena.sections.$section->section");
        $category = $request->get('category');
        if(!$basePrompt = config("yena.generatePrompt.$category")) abort(404);


        $_take = $request->get('take');
        $tone = $request->get('textTone');
        $language = $request->get('textLanguage');
        $content = $request->get('textContent');
        $basePrompt = str_replace('[text]', $content, $basePrompt);

        // $class = app()->make(ao($config, 'ai'));
    
    
        $messages = [];
        $messages[] = [
            'role' => 'user',
            'content' => "$basePrompt Make the tone professional. Make it in English."
        ];
        $messages[] = [
            'role' => 'user',
            'content' => config("prompts.section.$section->section.$_take")
        ];

        // foreach ($results as $result) {
        //     $_array = $result->choices[0]->toArray();
        //     $streamContent = ao($_array, 'delta.content');
        //     if(ao($_array, 'finish_reason') == 'stop'){
        //         $streamContent = '--ai-end';
        //     }
    
        //     if(!ao($_array, 'delta.content') && ao($_array, 'finish_reason') !== 'stop'){
        //         $streamContent = '--ai-start ';
        //     }
            
        // }
        $opts = [
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'temperature' => 1.0,
            'max_tokens' => 100,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
            'stream' => true
        ];
 
        header('Content-type: text/event-stream');
        header('X-Accel-Buffering: no');
        header('Cache-Control: no-cache');
        $txt = "";
        $client->chat($opts, function ($curl_info, $data) use (&$txt) {

            if ($obj = json_decode($data) and $obj->error->message != "") {
                // echo $data;
            } else {
                echo $data;
                $clean = str_replace("data: ", "", $data);
                $arr = json_decode($clean, true);
                if ($data != "data: [DONE]\n\n" and isset($arr["choices"][0]["delta"]["content"])) {
                    $txt .= $arr["choices"][0]["delta"]["content"];
                }
            }

            echo PHP_EOL;
            ob_flush();
            flush();
            return strlen($data);
        });

        // $opts = [
        //     'model' => 'gpt-3.5-turbo-instruct',
        //     'prompt' => "Category: Art. Given the statement, rewrite it to focus exclusively on Art-related content: 'Hello.' Provide a direct response without introductory or unrelated sentences. Make the tone friendly. Make it a title for a card section. Make it in English. Make the text Condense.",
        //     'temperature' => 1,
        //     "max_tokens" => 1000,
        //     "frequency_penalty" => 0,
        //     "presence_penalty" => 0.6,
        //     "stream" => true,
        // ];
 
        // header('Content-type: text/event-stream');
        // header('X-Accel-Buffering: no');
        // header('Cache-Control: no-cache');
        // $txt = "";
        // $client->completion($opts, function ($curl_info, $data) use (&$txt) {

        //     if ($obj = json_decode($data) and $obj->error->message != "") {
        //         // echo $data;
        //     } else {
        //         echo $data;
        //         $clean = str_replace("data: ", "", $data);
        //         $arr = json_decode($clean, true);
        //         if ($data != "data: [DONE]\n\n" and isset($arr["choices"][0]["delta"]["content"])) {
        //             $txt .= $arr["choices"][0]["delta"]["content"];
        //         }
        //     }

        //     echo PHP_EOL;
        //     ob_flush();
        //     flush();
        //     return strlen($data);
        // });

        // return;

		// $response->setCallback(function () use ($results){
        //     foreach ($results as $response) {
        //         $_array = $response->choices[0]->toArray();
        //         $streamContent = ao($_array, 'delta.content');
        //         if(ao($_array, 'finish_reason') == 'stop'){
        //             $streamContent = '--ai-end';
        //         }
            
        //         if(!ao($_array, 'delta.content') && ao($_array, 'finish_reason') !== 'stop'){
        //             $streamContent = '--ai-start ';
        //         }
        //         echo 'data: {"text": "'.$streamContent.'"}', "\n\n";
        //         ob_flush();
        //         flush();
        //     }
		// });
        
		// $response->headers->set('Content-Type', 'text/event-stream');
		// $response->headers->set('X-Accel-Buffering', 'no');
		// $response->headers->set('Cach-Control', 'no-cache');
		// $response->send();

        // return;

        // return response()->stream(function () use ($results) {
        //     foreach ($results as $response) {
        //         echo 'data: {"highlights":';
        //         ob_flush();
        //         flush();
        //     }
        // },
        // 200, ['X-Accel-Buffering' => 'no']);
    }
}
