<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Types;
use Illuminate\Http\Request;

class PermsController extends Controller
{
    function index() {
        if(!AppHelper::checkAuth()) return redirect("/no-access"); 
        
        return view("frontend/professional/admin/permissions/perms");
    }

    function new() {
        if(!AppHelper::checkAuth()) return redirect("/no-access"); 
        
        return view("frontend/professional/admin/permissions/new");
    }

    /**
     * Gets the types that exist in the db
     * 
     * @return response
     */
    function getTypes() {
        $perms = Types::select("id", "label")->where('restaurant_id', session()->get('restaurant.id'))->get();

        return response()->json($perms, 200);
    }
}
