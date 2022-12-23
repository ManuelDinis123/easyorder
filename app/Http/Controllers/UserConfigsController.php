<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserConfigsController extends Controller
{
    function index() {
        return view("frontend/professional/admin/editUsers");
    }
}
