<?php

namespace App\Http\Controllers\Admin\Templates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\YenaTemplate;

class TemplatesController extends Controller
{
    public function index(Request $request){
        $templates = YenaTemplate::paginate(15);
        
        return view('admin.templates.index', ['templates' => $templates]);
    }
}
