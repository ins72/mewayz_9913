<?php

namespace App\Http\Controllers\Admin\Website;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\YenaTemplate;

class WebsiteController extends Controller
{
    public function index(Request $request){
        $websites = Site::where('is_admin', 1)->orderBy('id', 'DESC')->paginate(15);
        
        return view('admin.website.index', ['websites' => $websites]);
    }
}
