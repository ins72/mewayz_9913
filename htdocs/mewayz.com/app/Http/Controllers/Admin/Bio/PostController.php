<?php

namespace App\Http\Controllers\Admin\Bio;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\BioSite;
use App\Yena\Page\Generate;

class PostController extends Controller
{
    
    public function tree(Request $request){
        // Check if method exists first or abort

        if(!method_exists($this, $request->tree)) abort(404);

        // If passed, then we know the method exists and we can continue

        return $this->{$request->tree}($request);

        // That's all lol
    }

    // So you can have other method here like

    public function create($request){
        $request->validate([
            'name' => 'required',
        ]);

        $request->validate([
            'name' => 'required'
        ]);

        $generate = new Generate;
        $build = $generate->setOwner(iam())->setName($request->name)->build();

        // $build->published = 1;
        $build->user_id = $request->user_id;
        $build->save();

        return back()->with('success', __('Bio created successfully'));
    }

    public function delete($request){
        if(!$site = BioSite::find($request->_id)) abort(404);

        // Delete Site
        $site->deleteCompletely();

        return back()->with('success', __('Bio deleted successfully'));
    }
}
