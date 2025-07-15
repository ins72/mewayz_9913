<?php

namespace App\Http\Controllers\Admin\Settings;

use App\User;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\Controller;
use Camroncade\Timezone\Facades\Timezone;

class SettingsController extends Controller{
    public function index(){
        $timezone = Timezone::selectForm(settings('others.timezone'), '', ['name' => 'settings[others][timezone]', 'class' => 'bg-w']);

         // Pass users to blade
        return view('admin.settings.index', ['timezone' => $timezone]);
    }
}
