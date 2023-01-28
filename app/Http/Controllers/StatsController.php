<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    function index() {
        if(!AppHelper::checkAuth()) return redirect("/no-access"); 
        if (!AppHelper::checkUserType(session()->get("type.id"), 'owner') || !AppHelper::checkUserType(session()->get("type.id"), 'admin')) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_stats')) return redirect("/professional");
        }

        return view("frontend/professional/stats/stats");
    }
}
