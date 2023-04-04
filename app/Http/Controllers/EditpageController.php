<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use Illuminate\Http\Request;

class EditpageController extends Controller
{

    /**
     * Main method
     * 
     * @return \Illuminate\View edit page view
     */
    function index()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'edit_page')) return redirect("/professional");
        }

        return view("frontend/professional/editpage/editpage");
    }
}
