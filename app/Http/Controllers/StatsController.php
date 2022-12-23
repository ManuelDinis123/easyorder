<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatsController extends Controller
{
    function index() {
        return view("frontend/professional/stats/stats");
    }
}
