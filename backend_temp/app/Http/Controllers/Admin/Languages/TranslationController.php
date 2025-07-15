<?php

namespace App\Http\Controllers\Admin\Languages;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;

class TranslationController extends Controller{
    public function languages(Request $request){
       // Get current translation locale
       $locale = config('app.locale');

       // Get all translations files
       $path = resource_path('lang');

       $language = false;

       // Languages array
       $languages = File::files($path);
       if(request()->get('language')){
        $locale = $request->get('language');
       }

       if(!file_exists(resource_path("lang/$locale.json")) && !empty($languages)){
        // First Lang
        $first = $languages[0];
        $first = pathinfo($first);

        $locale = ao($first, 'filename');
       }

       // Check for default or single lang

       $language = $this->singeLanguage($locale);

       $info = function($file){
            $details = [];

            $ex = explode('#', $file);
            $details['name'] = ao($ex, '0') ?? '';
            $details['locale'] = ao($ex, '1') ?? '';
        
            return $details;
       };

       if($language){
        $details = $info($locale);
       }
       
       return view('admin.translation.index', ['language' => $language, 'languages' => $languages, 'info' => $info, 'locale' => $locale]);
    }

    public function singeLanguage($locale){
        $request = request();

        // Search for array
        $query = $request->get('query');

        if(!file_exists($path = resource_path("lang/$locale.json"))){
            return false;
        }

        // Get translations values
        $values = file_get_contents($path);
        $values = json_decode($values, true);

        $input = preg_quote($query, '~'); // don't forget to quote input string!

        if (!empty($query)) {
            $values = preg_grep_keys_values('~' . $input . '~i', $values);
        }

        // Sort array's
        $values = !empty($values) ? array_reverse($values, true) : $values;

        return $values;
    }

    public function duplicateLanguage($lang){
        // Path of language
        $path = resource_path("lang/$lang.json");
        // Get all translations files
        $newpath = resource_path("lang/".$lang."_copy.json");
        // Check if file exists
        if (!file_exists($path)) {
            abort(404);
        }

        // Duplicate
        File::copy($path, $newpath);
        // Return back with success
        return back()->with('success', __('Saved Successfully'));
    }

    public function newLanguage(Request $request){
        $name = $request->name;
        $name = slugify($name, '_');
        // Path of language
        $path = resource_path("lang/$name.json");
        // Check if file exists
        if (file_exists($path)) {
            back()->with('error', __('Language exists'));
        }

        // Create language
        file_put_contents($path, '{}');

        // Return back with success
        return back()->with('success', __('Saved Successfully'));
    }


    // View & post translations
    public function viewLang($lang, Request $request){
        // Get all translations files
        $path = resource_path('lang');
        // Languages array
        $languages = File::files($path);
        // Path of language
        $path = resource_path("lang/$lang.json");

        // Search for array
        $query = $request->get('query');

        // Check if file exists
        if (!file_exists($path)) {
            abort(404);
        }

        // Get translations values
        $values = file_get_contents($path);
        $values = json_decode($values, true);

        $input = preg_quote($query, '~'); // don't forget to quote input string!

        if (!empty($query)) {
            $values = preg_grep_keys_values('~' . $input . '~i', $values);
        }

        // Sort array's
        $values = !empty($values) ? array_reverse($values, true) : $values;

        // View the page
        return view('mix::translation.view', ['values' => $values, 'lang' => $lang, 'languages' => $languages]);
    }


    public function postTrans($type, $language, Request $request){
        // Path of language
        $path = resource_path("lang/$language.json");
        // Verify Type
        if (!in_array($type, ['new', 'edit', 'edit_lang', 'delete', 'set_as_main', 'multi_delete'])) {
            abort(404);
        }

        // Check if language exists
        if (!file_exists($path)) {
            abort(404);
        }
        // Get translations values
        $values = file_get_contents($path);
        $values = json_decode($values, true);

        //
        switch ($type) {
            case 'set_as_main':
                $update = [
                    'APP_LOCALE' => $language,
                ];
                env_update($update);

                // Return back with success
                return back()->with('success', __('Saved Successfully'));
            break;

            case 'auto_generate':
                
            break;

            case 'edit_lang':
                $newName = $request->newName;
                $newName = slugify($newName, '_');
                $newLocation = resource_path("lang/$newName.json");

                // Check if new name exists
                if (file_exists($newLocation)) {
                    return back()->with('error', __('File name already exists'));
                }

                // Change name
                File::move($path, $newLocation);

                // Check if active and change to new active language
                if (config('app.APP_LOCALE') == $language) {
                    env_update(['APP_LOCALE' => $newName]);
                }

                // Return to new location with success
                return redirect()->route('mix-admin-languages-index')->with('success', __('Saved Successfully'));
            break;

            case 'multi_delete':
                // Loop actions
                if (!empty($request->action)) {
                    foreach($_POST['action'] as $key => $value){
                        // Check if array exists
                        if (array_key_exists($value, $values)) {
                            unset($values[$value]);
                        }
                    }
                }

                // Turn array to json
                $new = json_encode($values);

                // Add array's to json
                file_put_contents($path, $new);

                return ['response' => 'success'];
            break;
            case 'new':
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
            break;
            
            case 'edit':
                // Check if array exists
                if (!array_key_exists($request->previous, $values)) {
                    // return if previous doesnt exists
                    return back()->with('error', __('Previous value doesnt exists'));   
                }

                // Unset previous array
                unset($values[$request->previous]);

                // Add new array
                $values[$request->previous] = $request->new;

                // Turn array to json
                $new = json_encode($values);

                // Add array's to json
                file_put_contents($path, $new);

                // Return back with success
                return back()->with('success', __('Saved Successfully'));
            break;

            case 'delete':
                // Check if array exists
                if (!array_key_exists($request->previous, $values)) {
                    // return if previous doesnt exists
                    return back()->with('error', __('Previous value doesnt exists'));   
                }

                // Unset previous array
                unset($values[$request->previous]);

                // Turn array to json
                $new = json_encode($values);

                // Add array's to json
                file_put_contents($path, $new);

                // Return back with success
                return back()->with('success', __('Saved Successfully'));
            break;
        }

    }
}