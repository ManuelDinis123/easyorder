<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Ingredients;
use App\Models\OrderItems;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    function index()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");

        return view("frontend/professional/orders/orders");
    }

    /**
     * Gets all orders
     * 
     * @return Array
     */
    function get()
    {
        // Get the orders and join the table with the users table to get the user who ordered it
        $orders = Orders::select("orders.id", DB::raw('CONCAT(users.first_name, \' \', users.last_name) as full_name'), "orders.deadline", "orders.progress")
            ->where("restaurant_id", session()
                ->get("restaurant")["id"])
            ->join('users', 'users.id', '=', 'orders.ordered_by')
            ->get();

        return response()->json($orders, 200);
    }

    /**
     * Gets the info of the order and sends them to the view to be loaded
     * 
     * 
     * @return View
     */
    function edit(Request $id)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");

        // Get data from the menu item
        $order_details = Orders::select(
            "orders.id",
            "users.first_name",
            "users.last_name",
            "orders.deadline",
            "orders.progress"
        )->where("orders.id", $id->route('id'))
            ->join('users', 'users.id', '=', 'orders.ordered_by')
            ->get()
            ->first()
            ->toArray();

        // Get order ingredients
        $items = OrderItems::where("order_id", $order_details["id"])
            ->join('menu_item', 'menu_item.id', '=', 'order_items.menu_item_id')
            ->get()
            ->toArray();

        // Calculate total price
        $prices = array_column($items, 'price');
        $quantities = array_column($items, 'quantity');

        $total_price = [];
        for ($i = 0; $i < count($prices); $i++) {
            array_push($total_price, $prices[$i] * $quantities[$i]);
        }        

        return view("frontend/professional/orders/edit")
            ->with('items', $items)
            ->with($order_details)
            ->with('total_price', array_sum($total_price));
    }
}
