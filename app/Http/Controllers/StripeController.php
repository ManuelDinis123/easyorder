<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Shoppingcart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function index()
    {
        if (!AppHelper::hasLogin()) return redirect("/");
        return view("frontend.cart.checkout");
    }

    public function checkout(Request $deadline)
    {
        if (!AppHelper::hasLogin()) return redirect("/");
        session(["last_order_deadline" => $deadline->deadline]);

        // Get order data
        $data = Shoppingcart::select('shoppingcart.id', 'menu_item.name', DB::raw('((menu_item.price*cart_items.quantity)+coalesce(sides.side_total, 0)) as price'))
            ->join("cart_items", "cart_items.cart_id", '=', 'shoppingcart.id')
            ->join("menu_item", "menu_item.id", "=", "cart_items.item_id")
            ->leftJoin(DB::raw("(select side_dishes.cart_item_id as id, sum(menu_item_ingredients.price * side_dishes.quantity) as side_total from side_dishes inner join menu_item_ingredients on menu_item_ingredients.id = side_dishes.side_id) as sides"), "sides.id", "=", "cart_items.id")
            ->WHERE("shoppingcart.user_id", session()->get("user.id"))
            ->groupBy("cart_items.id")
            ->get();

        $name = "";
        $price = 0;
        foreach ($data as $key => $d) {
            $name .= " " . $d['name'] . ($key == (count($data) - 1) ? "" : ",");
            $price += $d['price'];
        }

        \Stripe\Stripe::setApiKey(config(key: 'stripe.sk'));

        $session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $name,
                        ],
                        'unit_amount' => ($price * 100),
                    ],
                    'quantity' => 1
                ],
            ],
            'mode' => 'payment',
            'success_url' => route(name: 'order_success'),
            'cancel_url' => route(name: 'payment'),
        ]);

        return redirect()->away($session->url);
    }

    public function success()
    {
        // Stuff to save order
        $deadline = session()->get("last_order_deadline");
        session()->forget("last_order_deadline");

        return view("frontend.cart.checkout")->with("deadline", $deadline);
    }
}
