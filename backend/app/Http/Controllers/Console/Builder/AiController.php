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
        ob_start();
        $client = new OpenAi(config('app.openai_key'));
        
        if(!$section = Section::where('uuid', $request->get('section_id'))->first()) abort(404);
        if(!__o_feature('feature.ai_pages_sections', $section->site->user)) return;
        
        $config = config("yena.sections.$section->section");
        $category = $request->get('category');


        $_take = $request->get('take');
        $tone = $request->get('textTone');
        $language = $request->get('textLanguage');
        $content = $request->get('textPrompt');

        $basePrompt = config("prompts.section.$section->section.$_take");
        $basePrompt = str_replace(['[text]','[category]'], [$content,$category], $basePrompt);
        $messages = [];
        $messages[] = [
            'role' => 'user',
            'content' => "$basePrompt Make it in $language."
        ];
        
        $opts = [
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'temperature' => 1,
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
            echo $data;
            
            echo PHP_EOL;
            ob_flush();
            flush();
            return strlen($data);
        });
    }
}
