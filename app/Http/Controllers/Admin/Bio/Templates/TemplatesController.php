<?php

namespace App\Http\Controllers\Admin\Bio\Templates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\YenaBioTemplate;

class TemplatesController extends Controller
{
    public function index(Request $request){
        $templates = YenaBioTemplate::paginate(15);
        
        return view('admin.bio.templates.index', ['templates' => $templates]);
    }
}
