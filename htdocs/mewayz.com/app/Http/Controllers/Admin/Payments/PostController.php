<?php

namespace App\Http\Controllers\Admin\Payments;

use App\Events\PlanEmails;
use App\Models\PendingPlan;
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
    
    public function pendingPost(Request $request){
        $type = $request->type;
        if (!$pending = PendingPlan::where('id', $request->pending)->first()) {
            abort(404);
        }

        // Plan
        $plan = $pending->plan;
        $user_id = $pending->user;
        $user = \App\User::find($user_id);

        if ($pending->status) {
            return back()->with('error', __('Cant use already confirmed Payment.'));
        }

        if (!in_array($type, ['accept', 'decline'])) {
            abort(403);
        }

        if ($type == 'decline') {
            $pending->status = 0;
            $pending->save();


            return back()->with('success', __('Plan Declined Successfully'));
        }

        $plan = \App\Models\Plan::find($pending->plan);
        $duration = $pending->duration;
        $duration_time = 0;

        switch ($duration) {
            case 'year':
                $duration_time = 365;
            break;
            case 'month':
                $duration_time = 30;
            break;
        }

        $user->cancelCurrentSubscription();
        $subscription = $user->upgradeCurrentPlanTo($plan, $duration_time, false, false);
        
        $paymentArray = [
            'user'          => $user->id,
            'name'          => $user->name,
            'plan'          => $plan->id,
            'plan_name'     => $plan->name,
            'email'         => $user->email,
            'ref'           => \Str::random(5),
            'currency'      => settings('payment.currency'),
            'duration'      => $duration,
            'price'         => ao($pending->info, 'duration_price'),
            'gateway'       => 'manual',
            'created_at'    => \Carbon\Carbon::now()
        ];

        \App\Models\PlanPayment::insert($paymentArray);
        
        $plan_history = new \App\Models\PlansHistory;
        $plan_history->plan_id = $plan->id;
        $plan_history->user_id = $user->id;
        $plan_history->save();

        // Update Pending
        $pending->status = 1;
        $pending->update();
        // Event
        event(new PlanEmails(\App\User::find($user_id), $plan));

        // Return back
        return back()->with('success', __('Plan Activated Successfully'));
    }
}
