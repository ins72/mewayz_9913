<?php

namespace App\Http\Controllers\Run;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class DatabaseController extends Controller{

    public function update(Request $request){
        DB::table('migrations')->where('migration', '2023_12_28_092315_updates')->delete();
        Artisan::call('migrate', ["--force" => true]);

        if(config('app.env') == 'production' || request()->get('production')){
            Artisan::call('config:cache');

            // Optimize
            Artisan::call('optimize:clear');
            Artisan::call('optimize');
        }
    }
}
