<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    /**
     * Admin page
     * 
     * @return \Illuminate\View
     */
    function index()
    {
        if (!AppHelper::hasLogin()) return redirect("/");

        return view("frontend.developers.getapikey");
    }
}
