<?php

namespace App\Http\Controllers\Admin\Payments;

use App\Models\Blog;
use App\Models\PendingPlan;
use App\Models\PlanPayment;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\Controller;

class PaymentsController extends Controller
{
    public function index(Request $request){
        
        $payments = PlanPayment::orderBy('id', 'DESC');

        if (!empty($email = $request->get('email'))) {
            $payments = $payments->where('email', 'LIKE','%'.$email.'%');
        }
        $payments = $payments->get();


        // Pending

        $pendingCount = 0; // PendingPlan::where('status', 0)->count();

        return view('admin.payments.index', ['payments' => $payments, 'pendingCount' => $pendingCount]);
    }

    public function pending(Request $request){

        // Get All Pending Payments
        $pending = PendingPlan::orderBy('id', 'DESC');

        //Query Type
        $searchBy = $request->get('search_by');
        if (!in_array($searchBy, ['email', 'name', 'ref'])) {
            $searchBy = 'ref';
        }

        // Query
        if (!empty($query = $request->get('query'))) {
            $query = str_replace('#', '', $query);
            $pending = $pending->where($searchBy, 'LIKE','%'.$query.'%');
        }

        // Status
        if (!empty($status = $request->get('status'))) {
            if (in_array($status, ['confirmed', 'unconfirmed'])) {
                switch ($status) {
                    case 'confirmed':
                        $status = 1;
                    break;

                    case 'unconfirmed': 
                        $status = 0;
                    break;
                }

                $pending = $pending->where('status', $status);
            }
        }

        //
        $pending = $pending->get();

        // Return the view
        return view('admin.payments.pending', ['pending' => $pending, 'searchBy' => $searchBy]);
    }
}
