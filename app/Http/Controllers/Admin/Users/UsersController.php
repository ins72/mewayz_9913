<?php

namespace App\Http\Controllers\Admin\Users;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function index(Request $request){

        // Lets fetch all users;
        $users = $this->Usersfilter($request);
    
         // Pass users to blade
        return view('admin.users.index', ['users' => $users]);
    }

    public function Usersfilter ($request){
        $users = new User;

        // Paginate results per page
        $paginate = (int) $request->get('per_page');
        if (!in_array($paginate, [10, 25, 50, 100, 250])) {
            $paginate = 10;
        }

        // Order Type
        $order_type = $request->get('order_type');
        if (!in_array($order_type, ['ASC', 'DESC'])) {
            $order_type = 'DESC';
        }
        // Order By
        $order_by = $request->get('order_by');
        if (!in_array($order_by, ['created_at', 'lastActivity', 'email', 'name'])) {
            $order_by = 'created_at';
        }

        $users = $users->orderBy($order_by, $order_type);

        //Query Type
        $searchBy = $request->get('search_by');
        if (!in_array($searchBy, ['email', 'name'])) {
            $searchBy = 'email';
        }

        // Query
        if (!empty($query = $request->get('query'))) {
            $users = $users->where($searchBy, 'LIKE','%'.$query.'%');
        }

        // Country
        if (!empty($country = $request->get('country'))) {
            $users = $users->where('lastCountry', $country);
        }

        // Status
        if (!empty($status = $request->get('status'))) {
            if (in_array($status, ['active', 'disabled'])) {

                switch ($status) {
                    case 'active':
                        $status = 1;
                    break;

                    case 'disabled': 
                        $status = 0;
                    break;
                }

                $users = $users->where('status', $status);
            }
        }

        // if (!empty($plan = $request->get('plan'))) {
        //     $users = $users->whereHas('plan', function($q) use ($plan){
        //         $plan = (int) $plan;
        //         $q->where('plan_id', '=', $plan);
        //     });
        // }

        // Returned Array of Paginate
        $users = $users->paginate(
            $paginate,
        )->onEachSide(1)->withQueryString();


        // Filter Plan
        return $users;
    }
}
