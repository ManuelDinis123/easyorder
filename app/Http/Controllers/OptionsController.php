<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use Illuminate\Http\Request;

class OptionsController extends Controller
{
    function index()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            return redirect("/professional");
        }
        return view("frontend/professional/admin/options");
    }
}
