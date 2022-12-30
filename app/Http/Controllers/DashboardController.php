<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    function index() {
        if(!session()->get('authenticated')) return redirect("/no-access");

        return view('frontend/professional/dashboard');
    }
}
