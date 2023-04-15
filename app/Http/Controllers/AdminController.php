<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Admin page
     * 
     * @return \Illuminate\View
     */
    function index() {
        if(!AppHelper::app_admin()) return redirect("/");

        return view("admin.dashboard");
    }

    /**
     * Admin restaurant page
     * 
     * @return \Illuminate\View
     */
    function restaurant() {
        if(!AppHelper::app_admin()) return redirect("/");

        return view("admin.restaurants");
    }

    /**
     * Admin users page
     * 
     * @return \Illuminate\View
     */
    function users() {
        if(!AppHelper::app_admin()) return redirect("/");

        return view("admin.users");
    }

    /**
     * Admin reports page
     * 
     * @return \Illuminate\View
     */
    function reports() {
        if(!AppHelper::app_admin()) return redirect("/");

        return view("admin.reports");
    }
}
