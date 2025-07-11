<?php

namespace App\Http\Controllers\Admin\Bio\Templates;

use App\Yena\Page\Generate;
use Illuminate\Http\Request;
use App\Models\YenaBioTemplate;
use App\Http\Controllers\Controller;
use App\Models\YenaTemplateAccess;

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

        $template = new YenaBioTemplate;
        $template->site_id = $build->id;
        $template->created_by = iam()->id;
        $template->name = $request->name;
        $template->price = $request->price;
        $template->save();

        // $build->published = 1;
        $build->is_template = 1;
        $build->save();

        return back()->with('success', __('Template created successfully'));
    }

    public function edit($request){
        if(!$template = YenaBioTemplate::find($request->_id)) abort(404);

        $template->site->is_template = 1;
        $template->site->save();

        $template->name = $request->name;
        $template->price = $request->price;
        $template->save();

        return back()->with('success', __('Template saved successfully'));
    }

    public function delete($request){
        if(!$template = YenaBioTemplate::find($request->_id)) abort(404);

        // Delete Site
        $template->site->deleteCompletely();

        // Delete access
        YenaTemplateAccess::where('template_id', $template->id)->delete();

        $template->delete();

        return back()->with('success', __('Template deleted successfully'));
    }
}
