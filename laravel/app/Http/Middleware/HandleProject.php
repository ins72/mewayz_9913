<?php

namespace App\Http\Middleware;

use Closure;
use App\Yena\Teams;
use App\Models\ProjectPixel;

class HandleProject
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        // Check for current organization
        Teams::init();
        
        // $_project = ProjectPixel::find($request->user()->_last_project_id);


        // if(!$_project){
        //     $_project = ProjectPixel::where('user_id', $request->user()->id)->first();
        // }

        // if(!$_project) return redirect()->route('create-project')->send();


        // $request->merge([
        //     'project' => $_project
        // ]);

        return $next($request);
    }
}
