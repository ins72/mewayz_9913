<?php

namespace App\Http\Controllers\Admin\Plans;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Models\Plan;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\Controller;

class PlansController extends Controller
{
    public function index(){
        $plans = Plan::orderBy('position', 'ASC')->orderBy('id', 'DESC')->get();
        
        
        $skeleton = config('yena.plans');

        return view('admin.plans.index', ['plans' => $plans, 'skeleton' => $skeleton]);
    }
}
