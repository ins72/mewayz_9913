<?php

namespace App\Http\Controllers\Admin\Plans;

use App\Models\User;
use App\Models\Plan;
use App\Models\PlansFeature;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\Controller;

class PostController extends Controller{
    
    public function tree(Request $request){

        if(!method_exists($this, $request->tree)){
            abort(404);
        }


        return $this->{$request->tree}($request);
    }

    public function sort($request){
        foreach($request->data as $key) {
            $key['id'] = (int) $key['id'];
            $key['position'] = (int) $key['position'];
            $update = Plan::find($key['id']);
            $update->position = $key['position'];
            $update->save();
        }
    }

    public function add_user($request){

        $request->validate([
            'date' => 'required'
        ]);

        if (!$plan = Plan::find($request->plan_id)) {
            return back()->with('error', __('Plan not found'));
        }

        if (!$user = User::find($request->user_id)) {
            return back()->with('error', __('User not found'));
        }

        if (!validate_date_string($request->date)) {
            return back()->with('error', __('Date not valid'));
        }

        $user_id = $user->id;

        $duration_time = \Carbon\Carbon::parse($request->date);

        $duration_time = now()->diffInDays($duration_time);

        $user->cancelCurrentSubscription();
        $subscription = $user->upgradeCurrentPlanTo($plan, $duration_time, false, false);

        return back()->with('success', __('Plan added to user'));
    }

    public function _get_feature($plan, $key, $skeleton = []){

        if(!$feature = $plan->features()->code($key)->first()){

            $feature = new PlansFeature;
            $feature->name = ao($skeleton, 'name');
            $feature->plan_id = $plan->id;
            $feature->description = ao($skeleton, 'description');
            $feature->code = $key;
            $feature->save();
        }

        return $feature;
    }

    public function edit($request){
        if(!$plan = Plan::find($request->_id)) abort(404);
        $request->validate([
            'name' => 'required',
        ]);

        $request->monthly = (float) $request->monthly;
        $request->annual = (float) $request->annual;

        $skeleton = config('yena.plans');


        foreach (ao($skeleton, 'feature') as $key => $value) {

            $feature = $this->_get_feature($plan, $key, $value);

            $meta = $feature->meta;
            $feature->enable = $request->feature[$key];

            $feature->type = 'feature';
            //$feature->metadata = $meta;
            $feature->save();
        }

        foreach (ao($skeleton, 'consume') as $key => $value) {

            $feature = $this->_get_feature($plan, $key, $value);

            $meta = $feature->meta;
            $feature->enable = 1;
            $limit = $request->consume[$key];

            $feature->type = 'limit';
            $feature->limit = $limit;
            $feature->save();
        }

        $plan->name = $request->name;
        $plan->price = $request->monthly;
        $plan->description = $request->description;
        $plan->annual_price = $request->annual;
        $plan->is_free = (bool) $request->is_free;
        $plan->has_trial = (bool) $request->has_trial;
        $plan->trial_days = $request->trial_days;
        $plan->save();

        return back()->with('success', __('Saved Successfully'));
    }

    public function delete($request){
        if(!$plan = Plan::find($request->_id)) abort(404);

        $plan->features()->delete();
        
        $plan->delete();
        // Return back with success
        return back()->with('success', __('Deleted successfully'));
    }

    public function create($request){
        $skeleton = config('yena.plans');

        $plan = new Plan;
        $plan->name = $request->name;
        $plan->save();


        foreach (ao($skeleton, 'feature') as $key => $value) {
            $feature = $this->_get_feature($plan, $key, $value);
        }

        foreach (ao($skeleton, 'consume') as $key => $value) {
            $feature = $this->_get_feature($plan, $key, $value);
        }

        return back()->with('success', __('Saved Successfully'));
    }
}
