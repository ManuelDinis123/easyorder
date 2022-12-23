<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrdersController extends Controller
{
    function index() {
        return view("frontend/professional/orders/orders");
    }
}
