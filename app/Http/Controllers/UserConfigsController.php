<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use Illuminate\Http\Request;

class UserConfigsController extends Controller
{
    function index() {
        if(!AppHelper::checkAuth()) return redirect("/no-access"); 
        if (!AppHelper::checkUserType(session()->get("type.id"), 'owner')) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'admin')) return redirect("/professional");
        }
        
        return view("frontend/professional/admin/editUsers");
    }
}
