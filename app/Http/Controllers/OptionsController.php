<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OptionsController extends Controller
{
    function index() {
        return view("frontend/professional/admin/options");
    }
}
