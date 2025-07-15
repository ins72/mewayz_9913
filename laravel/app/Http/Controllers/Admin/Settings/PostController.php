<?php

namespace App\Http\Controllers\Admin\settings;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class PostController extends Controller
{
    
    public function tree(Request $request){
        // Check if method exists first or abort

        if(!method_exists($this, $request->tree)) abort(404);

        // If passed, then we know the method exists and we can continue

        return $this->{$request->tree}($request);

        // That's all lol
    }


    public function post(Request $request){

        // Loop & post settings

        if (!empty($request->settings)) {
            $settings = [];
            foreach ($request->settings as $key => $value) {
                
                $settings[$key] = $value;

                $value = $value;
                if (is_array($value)) {
                    $settings[$key] = json_encode($value);
                    $value = json_encode($value);
                }

                $key_value = ['key' => $key, 'value' => $value];

                if (Setting::where('key', $key)->first()) {
                    Setting::where('key', $key)->update(['value' => $value]);
                }else{
                    Setting::insert($key_value);
                }
            }
        }

        // Logo & Favicon
        $this->logo_favicon($request);

        if(!ao($request->env, 'email_verification')){
            $allUsers = User::whereNull('email_verified_at')->get();
            foreach ($allUsers as $item) {
                $item->email_verified_at = now();
                $item->save();
            }
        }
        // if(!$request)

        // Loop & post env

        if (!empty($request->env)) {
            $env = [];
            foreach ($request->env as $key => $value) {
                $env[$key] = $value;
            }

            env_update($env);
        }

        // env_update(['APP_URL' => route('index-home')]);

        if(config('app.env') == 'production'){
            Artisan::call('config:cache');
        }

        return back()->with('success', __('Saved Successfully'));
    }

    private function logo_favicon($request){
        if (!empty($request->logo_icon)) {
            $request->validate([
                'logo_icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
            ]);

            if (!empty(settings('logo_icon'))) {
                if(mediaExists('media/site/logo', settings('logo_icon'))){
                    storageDelete('media/site/logo', settings('logo_icon')); 
                }
            }

            $imageName = putStorage('media/site/logo', $request->logo_icon);

            $values = array('value' => $imageName);
            Setting::where('key', 'logo_icon')->first() ? Setting::where('key', 'logo_icon')->update($values) : Setting::insert(['key' => 'logo_icon', 'value' => $imageName]);
        }
        
        if (!empty($request->login_logo)) {
            $request->validate([
                'login_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
            ]);

            if (!empty(settings('login_logo'))) {
                if(mediaExists('media/site/login', settings('login_logo'))){
                    storageDelete('media/site/login', settings('login_logo')); 
                }
            }

            $imageName = putStorage('media/site/login', $request->login_logo);

            $values = array('value' => $imageName);
            Setting::where('key', 'login_logo')->first() ? Setting::where('key', 'login_logo')->update($values) : Setting::insert(['key' => 'login_logo', 'value' => $imageName]);
        }

        if (!empty($request->mix_logo)) {
            $slug = md5(microtime());
            $request->validate([
                'mix_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
            ]);

            if (!empty(settings('mix_logo'))) {
                if(mediaExists('media/site/logo', settings('mix_logo'))){
                    storageDelete('media/site/logo', settings('mix_logo')); 
                }
            }

            $imageName = putStorage('media/site/logo', $request->mix_logo);

            $values = array('value' => $imageName);
            Setting::where('key', 'mix_logo')->first() ? Setting::where('key', 'mix_logo')->update($values) : Setting::insert(['key' => 'mix_logo', 'value' => $imageName]);
        }

        if (!empty($request->logo)) {
            $slug = md5(microtime());
            $request->validate([
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
            ]);

            if (!empty(settings('logo'))) {
                if(mediaExists('media/site/logo', settings('logo'))){
                    storageDelete('media/site/logo', settings('logo')); 
                }
            }

            $imageName = putStorage('media/site/logo', $request->logo);

            $values = array('value' => $imageName);
            Setting::where('key', 'logo')->first() ? Setting::where('key', 'logo')->update($values) : Setting::insert(['key' => 'logo', 'value' => $imageName]);
        }

        if (!empty($request->favicon)) {
            $slug = md5(microtime());
            $request->validate([
                'favicon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
            ]);

            if (!empty(settings('favicon'))) {
                if(mediaExists('media/site/favicon', settings('favicon'))){
                    storageDelete('media/site/favicon', settings('favicon')); 
                }
            }

            $imageName = putStorage('media/site/favicon', $request->favicon);


            $values = array('value' => $imageName);
            Setting::where('key', 'favicon')->first() ? Setting::where('key', 'favicon')->update($values) : Setting::insert(['key' => 'favicon', 'value' => $imageName]);
        }


        //

        
        if (!empty($request->branding_light)) {
            $slug = md5(microtime());
            $request->validate([
                'branding_light' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
            ]);

            if (!empty(settings('branding_logo_light'))) {
                if(mediaExists('media/site/branding', settings('branding_logo_light'))){
                    storageDelete('media/site/branding', settings('branding_logo_light')); 
                }
            }

            $imageName = putStorage('media/site/branding', $request->branding_light);

            $values = array('value' => $imageName);
            Setting::where('key', 'branding_logo_light')->first() ? Setting::where('key', 'branding_logo_light')->update($values) : Setting::insert(['key' => 'branding_logo_light', 'value' => $imageName]);
        }
        
        if (!empty($request->branding_dark)) {
            $slug = md5(microtime());
            $request->validate([
                'branding_dark' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
            ]);

            if (!empty(settings('branding_logo_dark'))) {
                if(mediaExists('media/site/branding', settings('branding_logo_dark'))){
                    storageDelete('media/site/branding', settings('branding_logo_dark')); 
                }
            }

            $imageName = putStorage('media/site/branding', $request->branding_dark);

            $values = array('value' => $imageName);
            Setting::where('key', 'branding_logo_dark')->first() ? Setting::where('key', 'branding_logo_dark')->update($values) : Setting::insert(['key' => 'branding_logo_dark', 'value' => $imageName]);
        }
    }
}
