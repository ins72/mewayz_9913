<?php

namespace App\Http\Controllers\Admin\Languages;

use File;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Amirami\Localizator\Services\Parser;
use Illuminate\Support\Facades\Validator;
use Amirami\Localizator\Services\FileFinder;
use Amirami\Localizator\Services\Localizator;

class PostController extends Controller
{
    
    public function tree(Request $request){
        // Check if method exists first or abort

        if(!method_exists($this, $request->tree)) abort(404);

        // If passed, then we know the method exists and we can continue

        return $this->{$request->tree}($request);

        // That's all lol
    }

    public function auto_generate($request){
        $path = resource_path("lang/$request->language.json");
        $ex = explode('#', $request->language);
        $_ex = ao($ex, '1');
        
        if (!file_exists($path)) abort(404);

        $values = file_get_contents($path);
        $values = json_decode($values, true);

        $data = [];
        foreach ($values as $key => $value) {
          $data[] = $key;   
        }


        $query = '';
        foreach ($data as $key => $value) {
            $value = urlencode($value);
            #$value = $value;
            if ($key !== array_key_last($data)) {
                $value = "$value&";
            }

            $query .= 'q='.$value;
        }

        $url = "https://translate.googleapis.com/translate_a/t?client=gtx&sl=en&tl=$_ex";
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $response = json_decode($response);

        if(!is_array($response)){
            return back()->with('error', __('Could not translate. Try again'));
        }

        foreach ($response as $key => $value) {
            $values[$data[$key]] = $value;
        }
        
        $new = json_encode($values);

        // Add array's to json
        file_put_contents($path, $new);
        //
        return back()->with('success', __('Language Saved Successfully'));
    }

    public function new_value($request){
        $request->validate([
            'previous' => 'required',
            'new' => 'required',
        ]);
        $path = resource_path("lang/$request->language.json");
        
        if (!file_exists($path)) {
            abort(404);
        }
        $values = file_get_contents($path);
        $values = json_decode($values, true);


        // Check if array exists
        if (array_key_exists($request->previous, $values)) {
            // return if previous doesnt exists
            return back()->with('error', __('Previous value exists'));   
        }

        // Add new and previous values
        $values[$request->previous] = $request->new;

        // Turn array to json
        $new = json_encode($values);

        // Add array's to json
        file_put_contents($path, $new);

        // Return back with success
        return back()->with('success', __('Saved Successfully'));
    }

    public function edit_language($request){
        $path = resource_path("lang/$request->language.json");
        
        if (!file_exists($path)) {
            abort(404);
        }
        $values = file_get_contents($path);
        $values = json_decode($values, true);

        if(!empty($request->value)){
            foreach ($request->value as $key => $value) {
            
                // Check if array exists
                if (array_key_exists($key, $values)) {
                    $values[$key] = $value;
                }
            }
        }
        $new = json_encode($values);

        // Add array's to json
        file_put_contents($path, $new);
        
        return back()->with('success', __('Language Saved Successfully'));
    }

    // So you can have other method here like

    public function create($request){
        $validate = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'locale' => ['required', 'string', 'max:255'],
        ]);

        $validate = $validate->validate();

        $name = $request->name;
        $name = slugify($name, '_');
        $locale = $request->locale;

        $language = $name . '#' . $locale;

        $path = resource_path("lang/$language.json");
        
        // Check if file exists
        if (file_exists($path)) {
            back()->with('error', __('Language exists'));
        }

        $types = array_keys(array_filter(config('localizator.localize')));

        $localizator = new Localizator;
        $parser = new Parser(config(), new FileFinder(config()));
        $parser->parseKeys();
        foreach ($types as $type) {
            $localizator->localize(
                $parser->getKeys($language, $type),
                $type,
                $language,
                false
            );
        }


        // \Artisan::call('sandy:update_database');
        // Create language
        // file_put_contents($path, $default);

        // Return Back
        return back()->with('success', __('Language Created Successfully.'));
    }

    public function sync($request){
        $path = resource_path("lang/$request->language.json");
        $ex = explode('#', $request->language);
        $_ex = ao($ex, '1');
        
        if (!file_exists($path)) abort(404);

        $language = $request->language;
        $types = array_keys(array_filter(config('localizator.localize')));

        $localizator = new Localizator;
        $parser = new Parser(config(), new FileFinder(config()));
        $parser->parseKeys();
        foreach ($types as $type) {
            $localizator->localize(
                $parser->getKeys($language, $type),
                $type,
                $language,
                false
            );
        }

        //
        return back()->with('success', __('Language Synced Successfully'));
    }

    public function edit($request){
        $request->validate([
            'name' => 'required'
        ]);
        $path = resource_path("lang/$request->language.json");
        
        if (!file_exists($path)) {
            abort(404);
        }
        
        $ex = explode('#', $request->language);

        $newName = slugify($request->name);
        $newName = $newName . '#' . ao($ex, '1') ?? '';


        $newLocation = resource_path("lang/$newName.json");

        // Check if new name exists
        if (file_exists($newLocation) && $request->language !== $newName) {
            return back()->with('error', __('File name already exists'));
        }

        if($request->default){
            env_update(['APP_LOCALE' => $newName]);
        }

        // Change name
        File::move($path, $newLocation);

        // Return to new location with success
        return redirect()->route('dashboard-admin-languages-index', ['language' => $newName])->with('success', __('Saved Successfully'));
    }


    public function delete($request){
        $path = resource_path("lang/$request->language.json");
        
        if (!file_exists($path)) {
            abort(404);
        }

        // Remove translation
        unlink($path);
        
        // Return back with success
        return back()->with('success', __('Language removed successfully'));
    }
    // Etc
}
