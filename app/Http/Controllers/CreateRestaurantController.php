<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use Illuminate\Http\Request;

class CreateRestaurantController extends Controller
{
    function index()
    {
        // only users that aren't already with professional accounts can access this page        
        if (session()->get("user.isProfessional")  || session()->get("user.authenticated")) return redirect("/no-access");

        return view("frontend/create_restaurant");
    }
}
