<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function index() {
        return view("frontend.cart.checkout");
    }

    public function checkout() {

        // Get order data
        

        \Stripe\Stripe::setApiKey(config(key:'stripe.sk'));

        $session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data'=>[
                            'name'=>'Send me money!!!',
                        ],
                        'unit_amount'=> 500, // 5.00
                    ],
                    'quantity' => 1
                ],
            ],
            'mode'=>'payment',
            'success_url' => route(name: 'order_success'),
            'cancel_url' => route(name: 'payment'),
        ]);

        return redirect()->away($session->url);
    }

    public function success() {
        // Stuff to save order

        return view("frontend.cart.checkout");
    }
}
