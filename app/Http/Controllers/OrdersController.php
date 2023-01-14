<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Ingredients;
use App\Models\MenuItems;
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
    function get(Request $data)
    {
        // Get the orders and join the table with the users table to get the user who ordered it
        $orders = Orders::select("orders.id", DB::raw('CONCAT(users.first_name, \' \', users.last_name) as full_name'), "orders.deadline", "orders.progress", "orders.closed")
            ->where("restaurant_id", session()
                ->get("restaurant")["id"])->where('orders.closed', $data->closed)->where('orders.isCancelled', $data->cancelled)
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
            "orders.progress",
            "orders.closed",
            "orders.isCancelled"
        )->where("orders.id", $id->route('id'))
            ->join('users', 'users.id', '=', 'orders.ordered_by')
            ->get()
            ->first()
            ->toArray();

        // Get order items
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

    /**
     * Gets items from order
     * 
     * @return Array
     */
    function get_items_from_order(Request $id)
    {
        $items = OrderItems::select("order_items.id as order_item_id", "menu_item.id", "menu_item.name", "order_items.done")
            ->where("order_id", $id->id)
            ->join('menu_item', 'menu_item.id', '=', 'order_items.menu_item_id')
            ->get()
            ->toArray();

        return response()->json($items, 200);
    }

    /**
     * Change item in an order to done or undone depending on the data the user sends
     * 
     * @param id int
     * @param isDone Bool
     * @return Bool
     */
    function change_status(Request $data)
    {
        $status = "Erro";
        $message = "Occoreu um erro ao realizar esta ação";

        // Update the order item
        $update = OrderItems::whereId($data->id)->update([
            "done" => $data->isDone ? 1 : 0
        ]);

        // Change the message if the update went through
        if ($update) {
            $status = "Sucesso";
            $message = "Item " . $data->isDone ? "marcado como pronto" : "desmarcado" . " com sucesso!";
        }

        // Update percentage
        $progress = $this->calc_progress($data->order_id);
        Orders::whereId($data->order_id)->update([
            "progress" => $progress
        ]);

        return response()->json(["status" => $status, "message" => $message, "progress" => $progress], 200);
    }

    /**
     * Closes the order if it's done
     * 
     * @param int
     * @return array
     */
    function close_order(Request $id)
    {
        // Check if any of the items are not done
        $items = OrderItems::where("order_id", $id->id)
            ->join('menu_item', 'menu_item.id', '=', 'order_items.menu_item_id')
            ->get()
            ->toArray();

        foreach ($items as $item) {
            if ($item["done"] != 1) {
                return response()->json(["status" => "Erro", "message" => "Todos os items devem estar prontos para fechar o pedido"], 200);
            }
        }

        $update = Orders::whereId($id->id)->update([
            "closed" => 1
        ]);

        if (!$update) {
            return response()->json(["status" => "Erro", "message" => "Ocorreu um erro ao fechar o pedido"], 200);
        }

        return response()->json(["status" => "Sucesso", "message" => "Pedido fechado com sucesso"], 200);
    }

    /**
     * Cancel the order
     * 
     * @param int
     * @return array
     */
    function cancel_order(Request $id)
    {
        $update = Orders::whereId($id->id)->update([
            "isCancelled" => 1
        ]);

        if (!$update) {
            return response()->json(["status" => "Erro", "message" => "Ocorreu um erro ao cancelar o pedido"], 200);
        }

        return response()->json(["status" => "Sucesso", "message" => "Pedido cancelado com sucesso"], 200);
    }


    /**
     * Calculate the progress of a given order
     * 
     * @param id
     * @return int
     */
    function calc_progress($order_id)
    {
        // get all items 
        $order_items = OrderItems::where('order_id', $order_id)->get();

        // count how many are done
        $done = 0;
        foreach ($order_items as $item) {
            if ($item['done']) {
                $done++;
            }
        }

        // calculate the percentage
        $percentage = $done != 0 ? ($done / count($order_items)) * 100 : 0;

        return $percentage;
    }
}
