<?php

namespace App\Http\Controllers\Admin\Sites;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SitesController extends Controller
{
    public function index(Request $request){

        // Lets fetch all users;
        $sites = $this->_filter($request);
    
         // Pass users to blade
        return view('admin.sites.index', ['sites' => $sites]);
    }

    public function _filter ($request){
        $pages = new Site;

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
        if (!in_array($order_by, ['created_at', 'name'])) {
            $order_by = 'created_at';
        }

        $pages = $pages->orderBy($order_by, $order_type);

        //Query Type
        $searchBy = $request->get('search_by');
        if (!in_array($searchBy, ['name'])) {
            $searchBy = 'name';
        }

        // Query
        if (!empty($query = $request->get('query'))) {
            $pages = $pages->where($searchBy, 'LIKE','%'.$query.'%');
        }
        
        // Returned Array of Paginate
        $pages = $pages->paginate(
            $paginate,
        )->onEachSide(1)->withQueryString();


        // Filter Plan
        return $pages;
    }
}
