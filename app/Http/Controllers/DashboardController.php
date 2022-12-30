<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\AppHelper;

class DashboardController extends Controller
{
    function index()
    {
        if(!AppHelper::checkAuth()) return redirect("/no-access");               

        return view('frontend/professional/dashboard');
    }
}
