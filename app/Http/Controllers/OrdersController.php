<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    function index() {
        if(!AppHelper::checkAuth()) return redirect("/no-access"); 

        return view("frontend/professional/orders/orders");
    }

    /**
     * Gets all orders
     * 
     * @return Array
     */
    function get() {
        $orders = Orders::where("restaurant_id", session()->get("restaurant")["id"])->join('users', 'users.id', '=', 'orders.ordered_by')->get();

        Log::info($orders);

        return response()->json($orders, 200);
    }
}
