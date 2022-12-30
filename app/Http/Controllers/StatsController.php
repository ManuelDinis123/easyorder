<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    function index() {
        if(!AppHelper::checkAuth()) return redirect("/no-access"); 

        return view("frontend/professional/stats/stats");
    }
}
