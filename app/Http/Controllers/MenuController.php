<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    function index() {
        return view("frontend/professional/menu/menu");
    }
}
