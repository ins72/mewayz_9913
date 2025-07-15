<?php

namespace Modules\Dashboard\Http\Controllers\Admin\Analytics;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use App\Models\MySession;
use App\Models\User;
use App\Models\PlanPayment;
use App\Models\Visitor;

class AnalyticsController extends Controller{
    public function analytics(Request $request){

        $analytics = $this->thisAnalytics($request);

        return view('dashboard::admin.analytics', ['analytics' => $analytics]);
    }

    public function mostVisited(Request $request){
        $visted = Visitor::topUsers();

        return view('dashboard::admin.analytics.most-visited', ['visted' => $visted]);
    }

    public function loggedIn(Request $request){
        $live_model = \App\Models\MySession::activity(10)->hasUser()->get();
        $live = [];
        $insight = false;

        foreach ($live_model as $value) {
            if ($user = User::find($value->user_id)) {
                $live[] = $value;
            }
        }

        if (!empty($insight_query = $request->get('insight'))) {
            if ($insight_model = \App\Models\MySession::activity(10)->where('id', $insight_query)->first()) {
                if (User::find($insight_model->user_id)) {
                    $insight = $insight_model;
                }   
            }
        }
        if (!\App\License::has_full_license()) {
            $insight = [];
            $live = [];
        }

        return view('dashboard::admin.analytics.loggedin', ['live' => $live, 'insight' => $insight]);
    }


    public function thisAnalytics($request){
        // Start & End Date
        $start_date = Carbon::now(settings('others.timezone'))->subDays(30)->format('Y-m-d');
        $end_date = Carbon::now(settings('others.timezone'))->format('Y-m-d');

        // Query
        if (!empty($request->get('start_date'))) {
            if (validate_date_string($request->get('start_date'))) {
                $start_date = Carbon::parse($request->get('start_date'));
            }
        }

        if (!empty($request->get('end_date'))) {
            if (validate_date_string($request->get('end_date'))) {
                $end_date = Carbon::parse($request->get('end_date'));
            }
        }

        $end_date = Carbon::parse($end_date)->addDays(1)->format('Y-m-d');

        // Query dates
        $query_users = User::whereBetween('created_at', [$start_date, $end_date])->get();
        $query_payments = PlanPayment::whereBetween('created_at', [$start_date, $end_date])->get();
        $query_visitors = Visitor::whereBetween('created_at', [$start_date, $end_date])->get();
        $query_visitors_all = Visitor::get();

        // Users
        $users = [];

        // Loop Users
        foreach ($query_users as $item) {
            $date = Carbon::parse($item->created_at)->toFormattedDateString();
            if (!array_key_exists($date, $users)) {
                $users[$date] = [
                    'count' => 0
                ];
            }

            if (array_key_exists($date, $users)) {
                $users[$date]['count']++;
            }
        }

        $users = get_chart_data($users);


        // Payments
        $payments = [];

        // Loop Payments
        foreach ($query_payments as $item) {
            $date = Carbon::parse($item->created_at)->toFormattedDateString();
            if (!array_key_exists($date, $payments)) {
                $payments[$date] = [
                    'payments' => 0,
                    'earnings' => 0
                ];
            }

            if (array_key_exists($date, $payments)) {
                $earnings = (float) ($payments[$date]['earnings'] + $item->price);
                $payments[$date]['payments']++;
                $payments[$date]['earnings'] = number_format($earnings);
            }
        }
        $payments = get_chart_data($payments);

        // Total Earnings
        $totalEarningsModels = PlanPayment::get();
        $totalEarnings = 0;

        foreach ($totalEarningsModels as $item) {
            $totalEarnings = (float) ($totalEarnings + $item->price);
        }

        // Total UserViews
        $total_user_views = [
            'visits' => 0,
            'unique' => 0
        ];

        foreach ($query_visitors_all as $item) {
            $total_user_views['visits'] += $item->views;
            $total_user_views['unique']++;
        }

        // Visitors
        $user_visitors = [];

        // Loop Visitors
        foreach ($query_visitors as $item) {
            $date = Carbon::parse($item->created_at)->toFormattedDateString();
            if (!array_key_exists($date, $user_visitors)) {
                $user_visitors[$date] = [
                    'visits' => 0,
                    'unique' => 0
                ];
            }


            if (array_key_exists($date, $user_visitors)) {
                $user_visitors[$date]['visits'] += $item->views;
                $user_visitors[$date]['unique']++;
            }
        }

        $user_visitors = get_chart_data($user_visitors);

        if (!\App\License::has_full_license()) {
            #return [];
        }

        return ['users' => $users, 'payments' => $payments, 'totalEarnings' => $totalEarnings, 'user_visitors' => $user_visitors, 'total_user_views' => $total_user_views];
    }
}