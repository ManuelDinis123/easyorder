<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\CartItems;
use App\Models\Ingredients;
use App\Models\Menu;
use App\Models\MenuItems;
use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\OrderSides;
use App\Models\SideDishes;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    function index()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_orders')) return redirect("/professional");
        }

        return view("frontend/professional/orders/orders");
    }

    /**
     * Gets all orders
     * 
     * @return Array
     */
    function get(Request $data)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_orders')) return redirect("/professional");
        }
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
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_orders')) return redirect("/professional");
        }
        if (!$this->can_view_order($id->route('id'))) return redirect("/professional/encomendas");

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
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_orders')) return redirect("/professional");
        }
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
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'write_orders')) return response()->json(["status" => "Erro", "message" => "Não tem permissão para realizar esta ação"], 403);
        }

        // Update the order item
        $update = OrderItems::whereId($data->id)->update([
            "done" => $data->isDone ? 1 : 0
        ]);

        if (!$update) response()->json(["status" => "Erro", "message" => "Ocorreu um erro"], 500);

        // Update percentage
        $progress = $this->calc_progress($data->order_id);
        $progressUpdate = Orders::whereId($data->order_id)->update([
            "progress" => $progress
        ]);

        if (!$progressUpdate) response()->json(["status" => "Erro", "message" => "Ocorreu um erro"], 500);

        return response()->json(["status" => "Sucesso", "message" => "Item " . ($data->isDone == 1 ? "marcado como pronto" : "desmarcado") . " com sucesso!", "progress" => $progress], 200);
    }

    /**
     * Closes the order if it's done
     * 
     * @param int
     * @return array
     */
    function close_order(Request $id)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'write_orders')) return response()->json(["status" => "Erro", "message" => "Não tem permissão para realizar esta ação"], 403);
        }
        // Check if any of the items are not done
        $items = OrderItems::where("order_id", $id->id)
            ->join('menu_item', 'menu_item.id', '=', 'order_items.menu_item_id')
            ->get()
            ->toArray();

        foreach ($items as $item) {
            if ($item["done"] != 1) {
                return response()->json(["status" => "Erro", "message" => "Todos os items devem estar prontos para fechar o pedido"], 400);
            }
        }

        $update = Orders::whereId($id->id)->update([
            "closed" => 1
        ]);

        if (!$update) {
            return response()->json(["status" => "Erro", "message" => "Ocorreu um erro ao fechar o pedido"], 500);
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
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'write_orders')) return response()->json(["status" => "Erro", "message" => "Não tem permissão para realizar esta ação"], 403);
        }
        $update = Orders::whereId($id->id)->update([
            "isCancelled" => 1
        ]);

        if (!$update) {
            return response()->json(["status" => "Erro", "message" => "Ocorreu um erro ao cancelar o pedido"], 500);
        }

        return response()->json(["status" => "Sucesso", "message" => "Pedido cancelado com sucesso"], 200);
    }

    /**
     * Get choosen side dishes from an item
     * 
     * 
     * @return Array
     */
    function get_sides(Request $id)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_menu')) return redirect("/professional");
        }

        $sides = OrderItems::select('menu_item_ingredients.ingredient', 'order_items_sides.quantity')
            ->join('order_items_sides', 'order_items_sides.order_item_id', '=', 'order_items.id')
            ->join('menu_item_ingredients', 'menu_item_ingredients.id', '=', 'order_items_sides.side_id')
            ->where('order_items.menu_item_id', $id->id)
            ->where('order_items.order_id', $id->order_id)
            ->get();

        return response()->json($sides, 200);
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

    // Check if an order is from the restaurant that the user is associated with
    function can_view_order($id)
    {
        $order = Orders::whereId($id)->get()->first();
        if (session()->get("restaurant.id") != $order->restaurant_id) return 0;
        return 1;
    }

    // Methods for client

    /**
     * Creates an order for client
     * 
     * @return Response
     */
    function create_order(Request $r)
    {
        $deadline = new DateTime($r->deadline);
        $formatted_deadline = $deadline->format("Y-m-d H:i:s");

        $orderItems = CartItems::where('cart_id', session()->get("shoppingCart"))->get();

        // Get all orders items and put them in an array associated with the restaurant they come from
        $sortedItems = [];
        foreach ($orderItems as $item) {
            $mi = MenuItems::whereId($item->item_id)->get()->first();
            $menu = Menu::whereId($mi->menu_id)->get()->first();
            $sortedItems[$menu->restaurant_id][] = [
                "id" => $item->id,
                "item_id" => $item->item_id,
                "quantity" => $item->quantity,
                "cart_id" => $item->cart_id,
                "note" => $item->note,
            ];
        }

        // Make the order for each restaurant
        foreach ($sortedItems as $key => $s) {
            $order = Orders::create([
                "ordered_at" => date("Y-m-d H:i:s"),
                "ordered_by" => session()->get("user.id"),
                "restaurant_id" => $key,
                "progress" => 0,
                "closed" => 0,
                "deadline" => strval($formatted_deadline),
                "isCancelled" => 0,
            ]);
            foreach ($s as $details) {
                $orderItms = OrderItems::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $details['item_id'],
                    'quantity' => $details['quantity'],
                    'note' => $details['note'],
                    'done' => 0,
                ]);
                $sides = SideDishes::where("cart_item_id", $details['id'])->get();
                if ($sides) {
                    foreach ($sides as $side) {
                        OrderSides::create([
                            "order_item_id" => $orderItms->id,
                            "side_id" => $side['side_id'],
                            "quantity" => $side['quantity'],
                        ]);
                    }
                }
            }
        }

        AppHelper::clearCart();

        return response()->json(["title" => "Sucesso", "message" => "Pedido efetuado com sucesso"]);
    }
}
