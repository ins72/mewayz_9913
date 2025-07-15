<?php

namespace App\Http\Controllers\Admin\Website;

use App\Models\Site;
use App\Yena\Site\Generate;
use App\Models\YenaTemplate;
use Illuminate\Http\Request;
use App\Models\YenaTemplateAccess;
use App\Http\Controllers\Controller;

class PostController extends Controller{
    public function tree(Request $request){

        if(!method_exists($this, $request->tree)){
            abort(404);
        }


        return $this->{$request->tree}($request);
    }
    
    public function create($request){
        $request->validate([
            'name' => 'required'
        ]);

        $generate = new Generate;
        $build = $generate->setOwner(iam())->setName($request->name)->build();
        Site::where('is_admin_selected', 1)->update([
            'is_admin_selected' => 0,
        ]);

        $build->published = 1;
        $build->is_admin = 1;
        $build->is_admin_selected = 1;
        $build->save();

        return back()->with('success', __('Site created successfully'));
    }

    public function activate($request){
        if(!$site = Site::find($request->_id)) abort(404);
        
        Site::where('is_admin_selected', 1)->update([
            'is_admin_selected' => 0,
        ]);

        $site->is_admin_selected = 1;
        $site->save();

        return back()->with('success', __('Site set as active.'));
    }

    // public function edit($request){
    //     if(!$template = YenaTemplate::find($request->_id)) abort(404);

    //     $template->name = $request->name;
    //     $template->price = $request->price;
    //     $template->save();

    //     return back()->with('success', __('Site saved successfully'));
    // }

    public function delete($request){
        if(!$site = Site::find($request->_id)) abort(404);

        // Delete Site
        $site->deleteCompletely();

        return back()->with('success', __('Site deleted successfully'));
    }
}
